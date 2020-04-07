/*
  Copyright (C) 2011 J. Coliz <maniacbug@ymail.com>

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  version 2 as published by the Free Software Foundation.
*/

/**
   Example using Dynamic Payloads

   This is an example of how to use payloads of a varying (dynamic) size.
*/

#include <SPI.h>
#include "nRF24L01.h"
#include "RF24.h"
#include <Arduino.h>
#include <Wire.h>
#include "Adafruit_SHT31.h"
#include "LowPower.h"




const long InternalReferenceVoltage = 1056L;  // Adjust this value to your boards specific internal BG voltage x1000
bool tempValid;


//
// Hardware configuration
//

Adafruit_SHT31 sht31 = Adafruit_SHT31();
RF24 radio(9, 10);



// Radio pipe addresses for the 2 nodes to communicate.
const uint64_t pipes[2] = { 0xF0F0F0F0E1LL, 0xF0F0F0F0D2LL };


struct dataStruct {
  byte voltage;
  int temp;
  byte humidity;
} payload;

void setup(void)
{
  pinMode(A0, OUTPUT);

  ADMUX = (0 << REFS1) | (1 << REFS0) | (0 << ADLAR) | (1 << MUX3) | (1 << MUX2) | (1 << MUX1) | (0 << MUX0);

  tempValid = sht31.begin(0x44);
  radio.begin();

  // enable dynamic payloads
  radio.enableDynamicPayloads();
  radio.setCRCLength(RF24_CRC_8);
  radio.setPALevel(RF24_PA_MAX);
  radio.setDataRate(RF24_250KBPS);
  radio.setRetries(2, 4);
  radio.setAutoAck(true);
  radio.setChannel(80);

  radio.openWritingPipe(pipes[0]);

}

void loop(void)
{
  // Start a conversion
  ADCSRA |= _BV( ADSC );
  
  // Wait for it to complete
  while ( ( (ADCSRA & (1 << ADSC)) != 0 ) );

  payload.voltage = (((InternalReferenceVoltage * 1023L) / ADC) + 5L) / 100L; // calculates for straight line value   //Determins what actual Vcc is, (X 100), based on known bandgap voltage;


  if (tempValid)
  {
    payload.temp = int(sht31.readTemperature() * 10);
    payload.humidity = byte(sht31.readHumidity());
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
  LowPower.powerDown(SLEEP_4S, ADC_OFF, BOD_OFF);  


}



