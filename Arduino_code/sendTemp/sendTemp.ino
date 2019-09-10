/*
 Copyright (C) 2011 J. Coliz <maniacbug@ymail.com>

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 version 2 as published by the Free Software Foundation.
 */

/**
 * Example using Dynamic Payloads 
 *
 * This is an example of how to use payloads of a varying (dynamic) size. 
 */

#include <SPI.h>
#include "nRF24L01.h"
#include "RF24.h"
#include <Arduino.h>
#include <Wire.h>
#include "Adafruit_SHT31.h"




const long InternalReferenceVoltage = 1056L;  // Adjust this value to your boards specific internal BG voltage x1000



Adafruit_SHT31 sht31 = Adafruit_SHT31();

//
// Hardware configuration
//

// Set up nRF24L01 radio on SPI bus plus pins 7 & 8


RF24 radio(9,10);



// Radio pipe addresses for the 2 nodes to communicate.
const uint64_t pipes[2] = { 0xF0F0F0F0E1LL, 0xF0F0F0F0D2LL };


struct dataStruct{
  byte voltage;
  int temp;
  byte humidity;
}payload;

void setup(void)
{
  pinMode(A0, OUTPUT);

  ADMUX = (0<<REFS1) | (1<<REFS0) | (0<<ADLAR) | (1<<MUX3) | (1<<MUX2) | (1<<MUX1) | (0<<MUX0);


  //Serial.begin(115200);
  //Serial.println("Ready...");
  
  sht31.begin(0x44);


  
  radio.begin();

  // enable dynamic payloads
  radio.enableDynamicPayloads();
  radio.setCRCLength(RF24_CRC_8);
  radio.setPALevel(RF24_PA_MAX);
  radio.setDataRate(RF24_250KBPS);
  radio.setRetries(3,5);
  radio.setChannel(38);




    radio.openWritingPipe(pipes[0]);
    //radio.openReadingPipe(1,pipes[1]);


  //
  // Start listening
  //

  //radio.startListening();


}

void loop(void)
{
//Serial.println("Start");

  // delay(50);  // Let mux settle a little to get a more stable A/D conversion
        // Start a conversion  
     ADCSRA |= _BV( ADSC );
        // Wait for it to complete
     while( ( (ADCSRA & (1<<ADSC)) != 0 ) );

payload.voltage = (((InternalReferenceVoltage * 1023L) / ADC) + 5L) / 100L; // calculates for straight line value   //Determins what actual Vcc is, (X 100), based on known bandgap voltage;
payload.temp=int(sht31.readTemperature()*10);
payload.humidity=byte(sht31.readHumidity());

//Serial.println("trySend");

    // Take the time, and send it.  This will block until complete
    radio.write( &payload, sizeof(payload) );
 //Serial.println(tmp);
//Serial.println(millis());
   //Serial.println("FinishSend");

    digitalWrite(A0,HIGH);
    // Try again 1s  later
    delay(60000);
    //Serial.println("delayEnd");

  
}



