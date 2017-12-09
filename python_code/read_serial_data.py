#!/usr/bin/env python3
# can change to python3 when ready
# chmod +x read_serial_data.py

import serial
import MySQLdb
import time


arduino = serial.Serial('/dev/ttyUSB1',baudrate=9600)

while 1:
    try:
        if(arduino.in_waiting):
            command=arduino.readline().strip().decode('ascii')
            if(command == 'start'):
                temp = arduino.readline().strip().decode('ascii')
                humidity = arduino.readline().strip().decode('ascii')
                pressure = arduino.readline().strip().decode('ascii')
                datetime = (time.strftime("%Y-%m-%d ") + time.strftime("%H:%M:%S"))

                query = """INSERT INTO sensor_data (GMT,decidegrees,pressure,humidity) VALUES(%s,%s,%s,%s)""",(datetime,temp,pressure,humidity)

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

    except:
        pass
    time.sleep(1)

