#!/usr/bin/env python
# can change to python3 when ready
# chmod +x read_serial_data.py

import serial
import MySQLdb
import time


arduino = serial.Serial('/dev/ttyACM0', 115200, timeout = 1)

while 1:

    if(arduino.readline().strip() == 'start'):
        
        temp = arduino.readline().strip()
        humidity = arduino.readline().strip()
        pressure = arduino.readline().strip()
        
        datetime = (time.strftime("%Y-%m-%d ") + time.strftime("%H:%M:%S"))
        
        query = """INSERT INTO sensor_data (GMT,decidegrees,pressure,humidity) VALUES (%s,%s,%s,%s)""",(datetime,temp,pressure,humidity)
        
        try:
            # Connect to MySQL
            db = MySQLdb.connect(host="localhost", user="database_writer",passwd="PASSWORD", db="weather_records")
            cur = db.cursor()

            try:
                cur.execute(*query)
                cur.close()
                db.commit()

            except:
                # Rollback if there is an error
                db.rollback()
        except:
            cur.close()

            
        db.close()
