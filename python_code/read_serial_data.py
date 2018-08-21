
#!/usr/bin/env python3
# chmod +x read_serial_data.py

import serial
import MySQLdb
import time
import math
from datetime import datetime


arduino = serial.Serial('/dev/ttyUSB0',baudrate=9600)

while 1:
    try:
        if(arduino.in_waiting >= 21):
            command=arduino.readline().strip().decode('ascii')
            if(command == 'start'):
                temp = int(arduino.readline().strip().decode('ascii'))
                humidity = int(arduino.readline().strip().decode('ascii'))
                pressure = int(arduino.readline().strip().decode('ascii'))
                local_datetime = datetime.now()
                local_date = local_datetime.strftime("%Y-%m-%d ")
                local_datetime_str = local_date + local_datetime.strftime("%H:%M:%S")

                try:
                    # Connect to MySQL
                    db = MySQLdb.connect(host="localhost", user="database_writer",passwd="PASSWORD", db="weather_records")
                    cur = db.cursor()

                    try:
                        query = """INSERT INTO sensor_data (GMT,decidegrees,pressure,humidity) VALUES(%s,%s,%s,%s)""",(local_datetime_str,temp,pressure,humidity)
                        cur.execute(*query)
                        db.commit()
                    except Exception as e:
                        print(e)
                        db.rollback()

                    try:
                        query = """SELECT ID, sampledDate, decidegreesHigh, decidegreesLow, humidityHigh, humidityLow, pressureHigh, pressureLow FROM dailyExtremes WHERE sampledDate = CURDATE();"""
                        cur.execute(query)
                       
                        if cur.rowcount>0:
                            results = cur.fetchone()
                            id = results[0]
                            sampledDate = results[1]
                            tempHigh = results[2]
                            tempLow = results[3]
                            humidityHigh = results[4]
                            humidityLow = results[5]
                            pressureHigh = results[6]
                            pressureLow = results[7]

                            if (temp>tempHigh or temp<tempLow or humidity>humidityHigh or humidity<humidityLow or pressure>pressureHigh or pressure<pressureLow):
                                query = """UPDATE dailyExtremes SET decidegreesHigh=GREATEST(%s,%s),decidegreesLow=LEAST(%s,%s),pressureHigh=GREATEST(%s,%s),pressureLow=LEAST(%s,%s),humidityHigh=GREATEST(%s,%s),humidityLow=LEAST(%s,%s) WHERE ID=%s""",(temp,tempHigh,temp,tempLow,pressure,pressureHigh,pressure,pressureLow,humidity,humidityHigh,humidity,humidityLow,id)
                                cur.execute(*query)
                                db.commit()
                        else:
                            query = """INSERT INTO dailyExtremes (sampledDate,decidegreesHigh,decidegreesLow,pressureHigh,pressureLow,humidityHigh,humidityLow) VALUES(%s,%s,%s,%s,%s,%s,%s)""",(local_date,temp,temp,pressure,pressure,humidity,humidity)
                            cur.execute(*query)
                            db.commit()
                            print("New")
                        
                    except Exception as e:
                        print(e)
                        db.rollback()

                except Exception as e:
               	    print(e)
		
                cur.close()
                db.close()

    except Exception as e:
        print(e)

    time.sleep(1)

