/*
  Take readings using a Sensiron SHT31 sensor and tranmit using nrf24l01+ wireless chip
*/

#include <SPI.h>
#include "RF24.h"
#include <Arduino.h>
#include <Wire.h>
#include "LowPower.h"

#define RF24_POWERUP_DELAY 250;

bool tempSensorActive = false;
bool tempReadingValid = false;
unsigned int counter = 0;
float humidity;
float temp;
uint8_t dataArray[3];
uint8_t readbuffer[6];


// Hardware configuration
const long InternalReferenceVoltage = 1062;

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


  Wire.begin(0x44);
  if (readStatus() != 0xFFFF)
  {
    tempSensorActive = true;
  }

  radio.setPALevel(RF24_PA_MAX);
  radio.setDataRate(RF24_250KBPS);
  radio.setRetries(2, 4);
  radio.setChannel(80);
  radio.setPayloadSize(5);

  radio.openWritingPipe(pipes[0]);

}

void loop(void)
{


  if (tempSensorActive)
  {

    // add 10% to delay from datasheet
    writeCommand(0x2400);  delay(17); // High repeatability
    // writeCommand(0x240B);  delay(7);   // Med repeatability
    // writeCommand(0x2416);  delay(5);   // Low repeatability


    size_t recv = Wire.requestFrom(0x44, sizeof(readbuffer));
    if (recv != sizeof(readbuffer))
    {
      tempReadingValid = false;
    }
    else
    {
      for (uint16_t i = 0; i < sizeof(readbuffer); i++)
      {
        readbuffer[i] = Wire.read();
      }

      if (readbuffer[2] != crc8(readbuffer, 2) || readbuffer[5] != crc8(readbuffer + 3, 2))
      {
        tempReadingValid = false;
      } else {

        int32_t stemp = (int32_t)(((uint32_t)readbuffer[0] << 8) | readbuffer[1]);
        stemp = ((4375 * stemp) >> 14) - 4500;
        temp = (float)stemp / 100.0f;

        uint32_t shum = ((uint32_t)readbuffer[3] << 8) | readbuffer[4];
        shum = (625 * shum) >> 12;
        humidity = (float)shum / 100.0f;

        // Add 0.5 as casting always rounds down
        payload.temp = int((temp * 10) + 0.5);
        payload.humidity = byte(humidity + 0.5);
        tempReadingValid = true;
      }
    }
  }
  if (!tempSensorActive || !tempReadingValid)
  {
    payload.temp = int(100);
    payload.humidity = byte(255);
  }

  if (counter == 0)
  {
    payload.batteryLevel = getBandgap();
    counter = 719;
  }
  else
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



// Code adapted from Adafruit_SHT31 library (v2.0.0)
/**
   Gets the current status register contents.

   @return The 16-bit status register.
*/
uint16_t readStatus(void) {
  writeCommand(0xF32D); /**< Read Out of Status Register */


  size_t recv = Wire.requestFrom(0x44, sizeof(dataArray));
  if (recv != sizeof(dataArray)) {
  }

  for (uint16_t i = 0; i < sizeof(dataArray); i++) {
    dataArray[i] = Wire.read();
  }

  uint16_t stat = dataArray[0];
  stat <<= 8;
  stat |= dataArray[1];
  // Serial.println(stat, HEX);
  return stat;
}


/**
   Internal function to perform and I2C write.

   @param cmd   The 16-bit command ID to send.
*/
bool writeCommand(uint16_t command) {
  uint8_t cmd[2];

  cmd[0] = command >> 8;
  cmd[1] = command & 0xFF;

  Wire.beginTransmission(0x44);



  // Write the data
  if (Wire.write(cmd, 2) != 2) {
    return false;
  }

  if (Wire.endTransmission() == 0) {
    return true;
  } else {
    return false;
  }
}









/**
   Performs a CRC8 calculation on the supplied values.

   @param data  Pointer to the data to use when calculating the CRC8.
   @param len   The number of bytes in 'data'.

   @return The computed CRC8 value.
*/
static uint8_t crc8(const uint8_t *data, int len) {
  /*

     CRC-8 formula from page 14 of SHT spec pdf

     Test data 0xBE, 0xEF should yield 0x92

     Initialization data 0xFF
     Polynomial 0x31 (x8 + x5 +x4 +1)
     Final XOR 0x00
  */

  const uint8_t POLYNOMIAL(0x31);
  uint8_t crc(0xFF);

  for (int j = len; j; --j) {
    crc ^= *data++;

    for (int i = 8; i; --i) {
      crc = (crc & 0x80) ? (crc << 1) ^ POLYNOMIAL : (crc << 1);
    }
  }
  return crc;
}
