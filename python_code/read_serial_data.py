import serial

arduino = serial.Serial('/dev/ttyACM0', 115200, timeout = 1)

while 1:

    if(arduino.readline().strip() == 'start'):
        
        print(arduino.readline().strip())
        print(arduino.readline().strip())
        print(arduino.readline().strip())

