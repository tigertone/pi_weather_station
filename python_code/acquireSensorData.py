
# chmod +x read_serial_data.py

from __future__ import print_function
import MySQLdb
import time
import math
from datetime import date, datetime, timedelta
from RF24 import *
import RPi.GPIO as GPIO
from struct import *
from smbus import SMBus
from bme280 import BME280

# Print information to command line as the program executes
verbose = True

# Configure Internal sensor (BME280)
try:
	bus = SMBus(1)
	sensorInternal = BME280(i2c_dev=bus, i2c_addr=0x77)
	sensorInternal.setup(mode="forced")
except Exception as e:
	print('Internal sensor malfuntion')
	sensorInternal = False

# Setup for GPIO 
radio = RF24(22, 0)


##########################################
pipes = [0xF0F0F0F0E1, 0xF0F0F0F0D2]

# Set parameters for nrf radio 
sensorExternal = radio.begin();
radio.enableDynamicPayloads();
radio.setPALevel(RF24_PA_MAX,1);
radio.setDataRate(RF24_250KBPS);
radio.setCRCLength(RF24_CRC_8);
radio.setChannel(80);
radio.setAutoAck(True);
if verbose:
	radio.printDetails()

# Begin to listen for new external data
radio.openReadingPipe(1,pipes[0])
radio.startListening()

# Identify start time so that we can loop the code every 60s
starttime=time.time()

# Hardcode list of sensor variables we have
sensorVariableNames = ["decidegreesInternal","pressureInternal","humidityInternal","decidegreesExternal","humidityExternal"]

# Infinite loop
while True:

	if verbose:
		print("\nNewLoop...")

	# Get current date/time - working in UTC (i.e assume true date/time for UK and ignore daylight savings)
	local_datetime = datetime.utcnow()
	local_date = local_datetime.strftime("%Y-%m-%d ")
	local_datetime_str = local_date + local_datetime.strftime("%H:%M:%S")

	# Set all sensor variables to null values
	decidegreesInternal = None
	pressureInternal = None
	humidityInternal = None
	decidegreesExternal = None
	humidityExternal = None
	voltageExternal1 = None
	
	# If there is a functional sensor, then read the current values
	if sensorInternal:
		if verbose:
			print("\nReading internal sensor")
		decidegreesInternal = int(sensorInternal.get_temperature()*10)
		pressureInternal = int(sensorInternal.get_pressure())
		humidityInternal = int(sensorInternal.get_humidity())
		if verbose:
			print(decidegreesInternal)
			print(pressureInternal)
			print(humidityInternal)
	# If there is any new external data read it all
	if sensorExternal and radio.available():
		if verbose:
			print("\nReading external sensor")
	
		while radio.available():
			len = radio.getDynamicPayloadSize()
			receive_payload = radio.read(len)
			#voltageExternal1 = receive_payload[0]
			voltageExternal1 = None
			decidegreesExternal = unpack('h',receive_payload[0:2])
			decidegreesExternal = int(decidegreesExternal[0])
			humidityExternal = receive_payload[2]
			#if 'voltageCurrent' in locals():
				#if voltageCurrent != voltageExternal1:
					#with open('/home/pi/pi_weather_station/weatherTmp/voltage.txt',"w") as fileToWrite:
						#fileToWrite.write(str(voltageCurrent)+"\n")
			#else:
				#voltageCurrent = voltageExternal1
				#with open('/home/pi/pi_weather_station/weatherTmp/voltage.txt',"w") as fileToWrite:
					#fileToWrite.write(str(voltageCurrent)+"\n")
			
			# These values are sent when the external sensor is malfunctioning - Reset variables to None
			if (decidegreesExternal == 100) and (humidityExternal == 255):
				decidegreesExternal = None
				humidityExternal = None

	# Connect to MySQL
	try:
		db = MySQLdb.connect(host="localhost", user="root",passwd="", db="weatherLog")
		cur = db.cursor()
		if verbose:
			print("\nConnected to database")
	
	# If connection is not successful 
	except: 
		print("\nCan't connect to database") 

	# If there has been a gap since the last data point, insert a row with null values in both tables so that chartjs won't join the gaps
	try:

		query = """SELECT GMT FROM sensorData order by id desc limit 1;"""
		cur.execute(query)

		if cur.rowcount>0:
			results = cur.fetchone()
			if (local_datetime - results[0]).seconds > 120:
				newDatetime = local_datetime - timedelta(minutes=1)
				query = "INSERT INTO sensorData (GMT) VALUES('{0}')".format(newDatetime.strftime("%Y-%m-%d %H:%M:%S"))
				if verbose:
					print("\nInserting dummy row into sensorData...")
					print(query)
				cur.execute(query)
				db.commit()


		query = "SELECT sampledDate FROM dailyExtremes order by id desc limit 1"
		cur.execute(query)

		if cur.rowcount>0:
			results = cur.fetchone()
			if (local_datetime.date() - results[0]).days >= 2:
				newDatetime = local_datetime - timedelta(days=1)
				query = "INSERT INTO dailyExtremes (sampledDate) VALUES('{0}')".format(newDatetime.strftime("%Y-%m-%d"))
				if verbose:
					print("\nInserting dummy row into dailyExtremes...")
					print(query)
				cur.execute(query)
				db.commit()
				
	except Exception as e:
		print("Failed to insert dummy rows into database!")
		print(e)
		db.rollback()

	# Add a new row into the database with the current data
	try:
		query = """INSERT INTO sensorData (GMT,decidegreesInternal,pressureInternal,humidityInternal, decidegreesExternal, humidityExternal) VALUES(%s,%s,%s,%s,%s,%s)""", (local_datetime_str,decidegreesInternal,pressureInternal,humidityInternal, decidegreesExternal, humidityExternal)
		if verbose:
			print("\nInserting current data into sensorData...")
			print(*query)
		cur.execute(*query)
		db.commit()
		
	except Exception as e:
		print("Failed to insert new row into sensorData!")
		print(e)
		db.rollback()

		
	# Test if current values are new extremes. If so update db
	# Get current extreme values
	query = "SELECT ID, sampledDate, decidegreesInternalHigh, decidegreesInternalLow, humidityInternalHigh, humidityInternalLow, pressureInternalHigh, pressureInternalLow, decidegreesExternalHigh, decidegreesExternalLow, humidityExternalHigh, humidityExternalLow, voltageTempSensor FROM dailyExtremes WHERE sampledDate = '{0}'".format(local_date)
	if verbose:
		print("\nExtracting current data from dailyExtremes...")
		print(query)
	cur.execute(query)
	
	# If there is any data, extract the variables
	try:
		if cur.rowcount>0:
			results = cur.fetchone()
			ID = results[0]
			sampledDate = results[1]
			decidegreesInternalHigh = results[2]
			decidegreesInternalLow = results[3]
			humidityInternalHigh = results[4]
			humidityInternalLow = results[5]
			pressureInternalHigh = results[6]
			pressureInternalLow = results[7]
			decidegreesExternalHigh = results[8]
			decidegreesExternalLow = results[9]
			humidityExternalHigh = results[10]
			humidityExternalLow = results[11]
			voltageExternal1High = results[12]
			
			# Create a new query and append any values where there are new extremes
			query = ""
			for sensorVariable in sensorVariableNames:
				if eval(sensorVariable) is not None	:					
					if eval(sensorVariable + "Low") is None or eval(sensorVariable) < eval(sensorVariable + "Low"):
						query = query + sensorVariable + "Low = " + str(eval(sensorVariable)) + ", "
					if eval(sensorVariable + "High") is None or eval(sensorVariable) > eval(sensorVariable + "High"):
						query = query + sensorVariable + "High = " + str(eval(sensorVariable)) + ", "
			#if voltageExternal1 is None or voltageExternal1 > voltageExternal1High:
				#query = query + "voltageExternal1 = " + str(voltageExternal1) + " "
			
			# If there is any new data...
			if query:
				# Complete & execute query
				query = "UPDATE dailyExtremes SET " + query.rstrip(", ") + " WHERE ID = " + str(ID)
				if verbose:
					print("\nUpdating dailyExtremes...")
					print(query)
				cur.execute(query)
				db.commit()	
				
		# Otherwise must be a new day, so insert all the current values
		else:
			query = """INSERT INTO dailyExtremes (sampledDate,decidegreesInternalHigh,decidegreesInternalLow,pressureInternalHigh,pressureInternalLow,humidityInternalHigh,humidityInternalLow, decidegreesExternalHigh, decidegreesExternalLow, humidityExternalHigh,humidityExternalLow,voltageTempSensor) VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)""",(local_date,decidegreesInternal,decidegreesInternal,pressureInternal,pressureInternal,humidityInternal,humidityInternal, decidegreesExternal, decidegreesExternal, humidityExternal, humidityExternal, voltageExternal1)
			if verbose:
				print("\nNew day - inserting new extremes...")
				print(query)
			cur.execute(*query)
			db.commit()
			
	except Exception as e:
		print("Failed to update dailyExtremes!")
		print(e)
		db.rollback()

	# Close db connection until next time
	db.close()
	
	# Sleep until 60s have elapsed since beginning of last loop
	time.sleep(60.0 - ((time.time() - starttime)) %60.0)


