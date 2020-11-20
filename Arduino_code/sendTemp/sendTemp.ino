/*
Take readings using a Sensiron SHT31 sensor and tranmit using nrf24l01+ wireless chip
*/

#include <SPI.h>
#include "nRF24L01.h"
#include "RF24.h"
#include <Arduino.h>
#include <Wire.h>
#include "Adafruit_SHT31.h"
#include "LowPower.h"

bool tempValid;


//
// Hardware configuration
//

Adafruit_SHT31 sht31 = Adafruit_SHT31();
RF24 radio(9, 10);



const uint64_t pipes[1] = { 0xF0F0F0F0E1LL };

struct dataStruct {
  int temp;
  byte humidity;
} payload;

void setup(void)
{
  
  radio.begin();
  tempValid = sht31.begin(0x44);

  // enable dynamic payloads
  radio.setPALevel(RF24_PA_MAX);
  radio.setDataRate(RF24_250KBPS);
  radio.setRetries(2, 4);
  radio.setChannel(80);
  radio.setPayloadSize(3);

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


