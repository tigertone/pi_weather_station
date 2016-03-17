import serial

arduino = serial.Serial('/dev/ttyACM0', 115200, timeout = 1)

while 1:
    
    tmp =arduino.readline()

    if(tmp == 'start'):
        
        print(arduino.readline())
        print(arduino.readline())
        print(arduino.readline())

