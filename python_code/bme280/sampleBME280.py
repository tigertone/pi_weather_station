#!/usr/bin/env python3
# chmod +x read_serial_data.py

from bme280 import *
import MySQLdb
import time
import math
from datetime import datetime

starttime=time.time()

sensor = BME280(t_mode=BME280_OSAMPLE_16, p_mode=BME280_OSAMPLE_16, h_mode=BME280_OSAMPLE_16)


while True:
    temp = int(sensor.read_temperature()*10)
    pressure = int(sensor.read_pressure()/100)
    humidity = int(sensor.read_humidity())            
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

        except Exception as e:
            print(e)
            db.rollback()
    
    except Exception as e:
        print(e)
        
    cur.close()
    db.close()

            
    time.sleep(60 - ((time.time() - starttime) % 60))
    



