import serial
import MySQLdb
import time

arduino = serial.Serial('/dev/ttyACM0', 115200, timeout = 1)

# Connect to MySQL
db = MySQLdb.connect(host="localhost", user="root",passwd="password", db="weather_records")
cur = db.cursor()

while 1:

    if(arduino.readline().strip() == 'start'):
        
        temp = arduino.readline().strip()
        pressure = arduino.readline().strip()
        humidity = arduino.readline().strip()
        
        datetime = (time.strftime("%Y-%m-%d ") + time.strftime("%H:%M:%S"))
        
        query = """INSERT INTO sensor_data (datetime,decidegrees,pressure,humidity) VALUES (%s,%s,%s,%s)""",(datetime,temp,pressure,humidity)
        print query
        
        try:
            cur.execute(query)
            db.commit()

        except:
            # Rollback if there is an error
            db.rollback()

        cur.close()
        db.close()

# mysql -u root -p
# USE weather_records;
# mysql> SELECT * FROM sensor_readings;
