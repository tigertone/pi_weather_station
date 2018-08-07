/***************************************************************************
  This is a library for the BME280 humidity, temperature & pressure sensor

  Designed specifically to work with the Adafruit BME280 Breakout
  ----> http://www.adafruit.com/products/2650

  These sensors use I2C or SPI to communicate, 2 or 4 pins are required
  to interface. The device's I2C address is either 0x76 or 0x77.

  Adafruit invests time and resources providing this open source code,
  please support Adafruit andopen-source hardware by purchasing products
  from Adafruit!

  Written by Limor Fried & Kevin Townsend for Adafruit Industries.
  BSD license, all text above must be included in any redistribution
 ***************************************************************************/

#include <Wire.h>
#include <Adafruit_Sensor.h>
#include <Adafruit_BME280.h>



Adafruit_BME280 bme; // I2C

void setup() {
    Serial.begin(9600);

    while (! bme.begin()) {
        Serial.println("Could not find a valid BME280 sensor, check wiring!");
        Adafruit_BME280 bme; // I2C
        delay(1000);
    }
  
    pinMode(A3,OUTPUT);
    // weather monitoring
    bme.setSampling(Adafruit_BME280::MODE_FORCED,
                    Adafruit_BME280::SAMPLING_X1, // temperature
                    Adafruit_BME280::SAMPLING_X1, // pressure
                    Adafruit_BME280::SAMPLING_X1, // humidity
                    Adafruit_BME280::FILTER_OFF   );

}



void loop() {
    // Only needed in forced mode! In normal mode, you can remove the next line.
    bme.takeForcedMeasurement(); // has no effect in normal mode
  

  // print out the state of the button:
  Serial.println("start");
  Serial.println(bme.readTemperature()*10.0F,0);
  Serial.println(bme.readHumidity(),0);
  Serial.println(bme.readPressure() / 100.0F, 0);

  Serial.flush();
  digitalWrite(A3,HIGH);
  delay(5000);

  
}

    

