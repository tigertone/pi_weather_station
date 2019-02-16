# chmod +x read_serial_data.py

from __future__ import print_function
from bme280 import *
import MySQLdb
import time
import math
from datetime import datetime
from RF24 import *
import RPi.GPIO as GPIO
from struct import *

sensor = BME280(t_mode=BME280_OSAMPLE_1, p_mode=BME280_OSAMPLE_1, h_mode=BME280_OSAMPLE_1)

# Setup for GPIO 22 CE and CE0 CSN with SPI Speed @ 8Mhz
radio = RF24(RPI_V2_GPIO_P1_15, BCM2835_SPI_CS0, BCM2835_SPI_SPEED_8MHZ)

##########################################
pipes = [0xF0F0F0F0E1, 0xF0F0F0F0D2]

radio.begin()
radio.setPALevel(RF24_PA_MAX);
radio.setDataRate(RF24_250KBPS)
radio.printDetails()
radio.enableDynamicPayloads();
radio.setCRCLength(RF24_CRC_8);


radio.openWritingPipe(pipes[1])
radio.openReadingPipe(1,pipes[0])
radio.startListening()

starttime=time.time()
count = 0
newExternalData = False
while True:
    count = count + 1
    decidegreesInternal = int(sensor.read_temperature()*10)
    pressureInternal = int(sensor.read_pressure()/100)
    humidityInternal = int(sensor.read_humidity())
    local_datetime = datetime.now()
    local_date = local_datetime.strftime("%Y-%m-%d ")
    local_datetime_str = local_date + local_datetime.strftime("%H:%M:%S")

    try:
        # Connect to MySQL
        db = MySQLdb.connect(host="localhost", user="database_writer",passwd="PASSWORD", db="weather_records")
        cur = db.cursor()

        try:
            if (newExternalData == True):
                query = """INSERT INTO sensor_data (GMT,decidegreesInternal,pressureInternal,humidityInternal, decidegreesExternal, humidityExternal) VALUES(%s,%s,%s,%s,%s,%s)""",(local_datetime_str,decidegreesInternal,pressureInternal,humidityInternal, decidegreesExternal, humidityExternal)
            elif (newExternalData == False):
                query = """INSERT INTO sensor_data (GMT,decidegreesInternal,pressureInternal,humidityInternal) VALUES(%s,%s,%s,%s)""",(local_datetime_str,decidegreesInternal,pressureInternal,humidityInternal)
            cur.execute(*query)
            db.commit()
        except Exception as e:
            print(e)
            db.rollback()

        try:
            if (newExternalData == True):
                query = """SELECT ID, sampledDate, decidegreesInternalHigh, decidegreesInternalLow, humidityInternalHigh, humidityInternalLow, pressureInternalHigh, pressureInternalLow, decidegreesExternalHigh, decidegreesExternalLow, humidityExternalHigh, humidityExternalLow, voltageExternal1 FROM dailyExtremes WHERE sampledDate = CURDATE();"""
            elif (newExternalData == False):
                query = """SELECT ID, sampledDate, decidegreesInternalHigh, decidegreesInternalLow, humidityInternalHigh, humidityInternalLow, pressureInternalHigh, pressureInternalLow FROM dailyExtremes WHERE sampledDate = CURDATE();"""
            cur.execute(query)

            if cur.rowcount>0:
                results = cur.fetchone()
                id = results[0]
                sampledDate = results[1]
                decidegreesInternalHigh = results[2]
                decidegreesInternalLow = results[3]
                humidityInternalHigh = results[4]
                humidityInternalLow = results[5]
                pressureInternalHigh = results[6]
                pressureInternalLow = results[7]
                if newExternalData == True:
                    decidegreesExternalHigh = results[8]
                    decidegreesExternalLow = results[9]
                    humidityExternalHigh = results[10]
                    humidityExternalLow = results[11]
                    voltageLow = results[12]

                    if decidegreesExternalHigh is None:
                        query = """UPDATE dailyExtremes SET decidegreesExternalHigh=%s,decidegreesExternalLow=%s,humidityExternalHigh=%s,humidityExternalLow=%s, voltageExternal1=%s WHERE ID=%s""",(decidegreesExternal,decidegreesExternal,humidityExternal,humidityExternal,voltage,id)
                        cur.execute(*query)
                        db.commit()
                    if (decidegreesInternal>decidegreesInternalHigh or decidegreesInternal<decidegreesInternalLow or humidityInternal>humidityInternalHigh or humidityInternal<humidityInternalLow or pressureInternal>pressureInternalHigh or pressureInternal<pressureInternalLow or decidegreesExternal>decidegreesExternalHigh or decidegreesExternal<decidegreesExternalLow or humidityExternal>humidityExternalHigh or humidityExternal<humidityExternalLow or voltage<voltageLow):
                        query = """UPDATE dailyExtremes SET decidegreesInternalHigh=GREATEST(%s,%s),decidegreesInternalLow=LEAST(%s,%s),pressureInternalHigh=GREATEST(%s,%s),pressureInternalLow=LEAST(%s,%s),humidityInternalHigh=GREATEST(%s,%s),humidityInternalLow=LEAST(%s,%s),decidegreesExternalHigh=GREATEST(%s,%s),decidegreesExternalLow=LEAST(%s,%s),humidityExternalHigh=GREATEST(%s,%s),humidityExternalLow=LEAST(%s,%s) WHERE ID=%s""",(decidegreesInternal,decidegreesInternalHigh,decidegreesInternal,decidegreesInternalLow,pressureInternal,pressureInternalHigh,pressureInternal,pressureInternalLow,humidityInternal,humidityInternalHigh,humidityInternal,humidityInternalLow,decidegreesExternal,decidegreesExternalHigh,decidegreesExternal,decidegreesExternalLow,humidityExternal,humidityExternalHigh,humidityExternal,humidityExternalLow,id)
                        cur.execute(*query)
                        db.commit()

                elif (newExternalData == False and (decidegreesInternal>decidegreesInternalHigh or decidegreesInternal<decidegreesInternalLow or humidityInternal>humidityInternalHigh or humidityInternal<humidityInternalLow or pressureInternal>pressureInternalHigh or pressureInternal<pressureInternalLow)):
                        query = """UPDATE dailyExtremes SET decidegreesInternalHigh=GREATEST(%s,%s),decidegreesInternalLow=LEAST(%s,%s),pressureInternalHigh=GREATEST(%s,%s),pressureInternalLow=LEAST(%s,%s),humidityInternalHigh=GREATEST(%s,%s),humidityInternalLow=LEAST(%s,%s) WHERE ID=%s""",(decidegreesInternal,decidegreesInternalHigh,decidegreesInternal,decidegreesInternalLow,pressureInternal,pressureInternalHigh,pressureInternal,pressureInternalLow,humidityInternal,humidityInternalHigh,humidityInternal,humidityInternalLow,id)
                        cur.execute(*query)
                        db.commit()
            else:
                if (newExternalData == True):
                    query = """INSERT INTO dailyExtremes (sampledDate,decidegreesInternalHigh,decidegreesInternalLow,pressureInternalHigh,pressureInternalLow,humidityInternalHigh,humidityInternalLow, decidegreesExternalHigh, decidegreesExternalLow, humidityExternalHigh,humidityExternalLow,voltageExternal1) VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)""",(local_date,decidegreesInternal,decidegreesInternal,pressureInternal,pressureInternal,humidityInternal,humidityInternal, decidegreesExternal, decidegreesExternal, humidityExternal, humidityExternal, voltage)
                if (newExternalData == False):
                    query = """INSERT INTO dailyExtremes (sampledDate,decidegreesInternalHigh,decidegreesInternalLow,pressureInternalHigh,pressureInternalLow,humidityInternalHigh,humidityInternalLow) VALUES(%s,%s,%s,%s,%s,%s,%s)""",(local_date,decidegreesInternal,decidegreesInternal,pressureInternal,pressureInternal,humidityInternal,humidityInternal)
                cur.execute(*query)
                db.commit()

        except Exception as e:
            print(e)
            db.rollback()

    except Exception as e:
        print(e)

    cur.close()
    db.close()

    newExternalData = False


    while time.time() < (starttime + (60*count)):
        try:
            if radio.available():
                while radio.available():
                    len = radio.getDynamicPayloadSize()
                    receive_payload = radio.read(len)
                    voltage = receive_payload[0]
                    decidegreesExternal = unpack('h',receive_payload[1:3])
                    decidegreesExternal = int(decidegreesExternal[0])
                    humidityExternal = receive_payload[3]
                    print(voltage)
                    print(decidegreesExternal)
                    print(humidityExternal)
                newExternalData = True
            time.sleep(1)

        except Exception as e:
            print(e)

 
    print("new loop")
#    decidegreesExternal = int(5)
#    humidityExternal = int(33)
#    newExternalData = True
