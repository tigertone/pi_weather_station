/*
Take readings using a Sensiron SHT31 sensor and tranmit using nrf24l01+ wireless chip
*/

#include <SPI.h>
#include "RF24.h"
#include <Arduino.h>
#include <Wire.h>
#include "LowPower.h"
#include "Adafruit_SHT31.h"

#define RF24_POWERUP_DELAY 500;

bool tempValid;
unsigned int counter = 0; 


// Hardware configuration
const long InternalReferenceVoltage = 1062;

Adafruit_SHT31 sht31 = Adafruit_SHT31();
RF24 radio(9, 10);




const uint64_t pipes[1] = { 0xF0F0F0F0E1LL };

struct dataStruct {
  int temp;
  byte humidity;
  int batteryLevel;
} payload;

void setup(void)
{
  
  radio.begin();
  tempValid = sht31.begin(0x44);

  radio.setPALevel(RF24_PA_MAX);
  radio.setDataRate(RF24_250KBPS);
  radio.setRetries(2, 4);
  radio.setChannel(80);
  radio.setPayloadSize(5);

  radio.openWritingPipe(pipes[0]);

}

void loop(void)
{
  
  
  if (tempValid)
  {
    // Add 0.5 as casting always rounds down
    payload.temp = int((sht31.readTemperature() * 10) + 0.5);
    payload.humidity = byte(sht31.readHumidity() + 0.5);
  }
  else
  {
    payload.temp = int(100);
    payload.humidity = byte(255);
  }

  if (counter == 0)
  {
    payload.batteryLevel = getBandgap();
    counter = 719;
  } 
  else if (counter==720)
  {
    counter = counter - 1;
  }
  
  radio.powerUp();
  // Send payload. This will block until complete
  radio.write( &payload, sizeof(payload) );
  radio.powerDown();

  LowPower.powerDown(SLEEP_8S, ADC_OFF, BOD_OFF);  
  LowPower.powerDown(SLEEP_8S, ADC_OFF, BOD_OFF);  
  LowPower.powerDown(SLEEP_8S, ADC_OFF, BOD_OFF);  
  LowPower.powerDown(SLEEP_8S, ADC_OFF, BOD_OFF);  
  LowPower.powerDown(SLEEP_8S, ADC_OFF, BOD_OFF);  
  LowPower.powerDown(SLEEP_8S, ADC_OFF, BOD_OFF);  
  LowPower.powerDown(SLEEP_8S, ADC_OFF, BOD_OFF);
  LowPower.powerDown(SLEEP_2S, ADC_OFF, BOD_OFF);  
  LowPower.powerDown(SLEEP_1S, ADC_OFF, BOD_OFF);  

}



// Code courtesy of "Coding Badly" and "Retrolefty" from the Arduino forum
// results are Vcc * 100
// So for example, 5V would be 500.
int getBandgap () 
  {
  // REFS0 : Selects AVcc external reference
  // MUX3 MUX2 MUX1 : Selects 1.1V (VBG)  
   ADMUX = bit (REFS0) | bit (MUX3) | bit (MUX2) | bit (MUX1);
   ADCSRA |= bit( ADSC );  // start conversion
   while (ADCSRA & bit (ADSC))
     { }  // wait for conversion to complete
   int results = (((InternalReferenceVoltage * 1024) / ADC) + 5) / 10; 
   return results;
  } // end of getBandgap
