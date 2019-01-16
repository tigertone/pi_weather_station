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

//
// Hardware configuration
//

// Set up nRF24L01 radio on SPI bus plus pins 7 & 8

RF24 radio(9,10);



//
// Topology
//

// Radio pipe addresses for the 2 nodes to communicate.
const uint64_t pipes[2] = { 0xF0F0F0F0E1LL, 0xF0F0F0F0D2LL };




//
// Payload
//

const int min_payload_size = 4;
const int max_payload_size = 32;
const int payload_size_increments_by = 1;
int next_payload_size = min_payload_size;

char receive_payload[max_payload_size+1]; // +1 to allow room for a terminating NULL char

void setup(void)
{


  Serial.begin(115200);


  //
  // Setup and configure rf radio
  //

  radio.begin();

  // enable dynamic payloads
  radio.enableDynamicPayloads();
  radio.setPALevel(RF24_PA_LOW);
  radio.setDataRate(RF24_250KBPS);




  //
  // Open pipes to other nodes for communication
  //

  // This simple sketch opens two pipes for these two nodes to communicate
  // back and forth.
  // Open 'our' pipe for writing
  // Open the 'other' pipe for reading, in position #1 (we can have up to 5 pipes open for reading)

    radio.openWritingPipe(pipes[0]);
    radio.openReadingPipe(1,pipes[1]);


  //
  // Start listening
  //

  radio.startListening();

  //
  // Dump the configuration of the rf unit for debugging
  //

  radio.printDetails();
}

void loop(void)
{

    // The payload will always be the same, what will change is how much of it we send.
    static char send_payload[] = "ABCDEFGHIJKLMNOPQRSTUVWXYZ789012";


    // Take the time, and send it.  This will block until complete
    Serial.print(F("Now sending length "));
    Serial.println(next_payload_size);
    radio.write( send_payload, next_payload_size );


    // Try again 1s later
    delay(1000);
}

