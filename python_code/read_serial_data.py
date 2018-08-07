
#!/usr/bin/env python3
# can change to python3 when ready
# chmod +x read_serial_data.py

import serial
import MySQLdb
import time
import math
from datetime import datetime


arduino = serial.Serial('/dev/ttyUSB0',baudrate=9600)

while 1:
    try:
	print(arduino.in_waiting)
        if(arduino.in_waiting >= 21):
            command=arduino.readline().strip().decode('ascii')
	    print(command)
            if(command == 'start'):
                temp = arduino.readline().strip().decode('ascii')
                humidity = arduino.readline().strip().decode('ascii') 
		pressure = arduino.readline().strip().decode('ascii')
		GMT_datetime = datetime.utcnow()
    		GMT_date = GMT_datetime.strftime("%Y-%m-%d ")
    		datetime_str = GMT_date + GMT_datetime.strftime("%H:%M:%S")

                try:
                    # Connect to MySQL
                    db = MySQLdb.connect(host="localhost", user="database_writer",passwd="PASSWORD", db="weather_records")
                    cur = db.cursor()

                    try:
			query = """INSERT INTO sensor_data (GMT,decidegrees,pressure,humidity) VALUES(%s,%s,%s,%s)""",(datetime_str,temp,pressure,humidity)
                        cur.execute(*query)
			db.commit()
                    except Exception as e:
                        print(e)
			print(humidity)
                        db.rollback()

		    try:
		        query = """SELECT ID, sampledDate, decidegreesHigh, decidegreesLow, humidityHigh, humidityLow, pressureHigh, pressureLow FROM dailyExtremes ORDER BY ID  DESC LIMIT 1;"""
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

			    if sampledDate.strftime("%Y-%m-%d ") == GMT_date and (temp>tempHigh or temp<tempLow or humidity>humidityHigh or humidity<humidityLow or pressure>pressureHigh or pressure<pressureLow):
	    			query = """UPDATE dailyExtremes SET decidegreesHigh=GREATEST(%s,%s),decidegreesLow=LEAST(%s,%s),pressureHigh=GREATEST(%s,%s),pressureLow=LEAST(%s,%s),humidityHigh=GREATEST(%s,%s),humidityLow=LEAST(%s,%s) WHERE ID=%s""",(temp,tempHigh,temp,tempLow,pressure,pressureHigh,pressure,pressureLow,humidity,humidityHigh,humidity,humidityLow,id)
			    else:
                                query = """INSERT INTO dailyExtremes (sampledDate,decidegreesHigh,decidegreesLow,pressureHigh,pressureLow,humidityHigh,humidityLow) VALUES(%s,%s,%s,%s,%s,%s,%s)""",(GMT_date,temp,temp,pressure,pressure,humidity,humidity)
			else:
            		    query = """INSERT INTO dailyExtremes (sampledDate,decidegreesHigh,decidegreesLow,pressureHigh,pressureLow,humidityHigh,humidityLow) VALUES(%s,%s,%s,%s,%s,%s,%s)""",(GMT_date,temp,temp,pressure,pressure,humidity,humidity)

	        	cur.execute(*query)
        		db.commit()

		    except Exception as e:
			print(e)
                        db.rollback()

                except Exception as e:
		    print(e)
		    print(humidity)

		cur.close()
                db.close()

    except Exception as e:
	print(e)
        pass
    time.sleep(1)

