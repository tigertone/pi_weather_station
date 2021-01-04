/*
  Take readings using a Sensiron SHT31 sensor and tranmit using nrf24l01+ wireless chip
*/

#include "RF24.h"
#include <Wire.h>
#include "LowPower.h"

#define RF24_POWERUP_DELAY 150;

bool tempSensorActive = false;
bool tempReadingValid = false;
unsigned int counter = 0;
uint8_t readbuffer[6];
int voltageArray[5];
byte voltageInd[3];
const uint64_t pipes[1] = { 0xF0F0F0F0E1LL };
const float adcToVolt = (3.0 / 1023.0f) * 2 * 100; // Multiply by 2 due to voltage divider

struct dataStruct {
  int temp;
  byte humidity;
  int batteryLevel;
} payload;

// Hardware configuration
RF24 radio(9, 10);


void setup(void)
{

  radio.begin();

  radio.setPALevel(RF24_PA_MAX);
  radio.setDataRate(RF24_250KBPS);
  radio.setRetries(2, 4);
  radio.setChannel(80);
  radio.setPayloadSize(5);

  radio.openWritingPipe(pipes[0]);



  Wire.begin(0x44);
  if (readStatus() != 0xFFFF)
  {
    tempSensorActive = true;
  }

}

void loop(void)
{


  if (tempSensorActive)
  {

    // add 10% to delay from datasheet
    // writeCommand(0x2400);  delay(17); // High repeatability
    writeCommand(0x240B);  delay(7);   // Med repeatability
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
      }
      else
      {

        int32_t stemp = (int32_t)(((uint32_t)readbuffer[0] << 8) | readbuffer[1]);
        stemp = ((4375 * stemp) >> 14) - 4500;
        payload.temp = int(((float)stemp / 10.0f) + 0.5);

        uint32_t shum = ((uint32_t)readbuffer[3] << 8) | readbuffer[4];
        shum = (625 * shum) >> 12;
        payload.humidity = byte(((float)shum / 100.0f) + 0.5);

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
    // Do 5 reads and use median
    voltageArray[0] = analogRead(A1);
    voltageArray[1] = analogRead(A1);
    voltageArray[2] = analogRead(A1);
    voltageArray[3] = analogRead(A1);
    voltageArray[4] = analogRead(A1);

    // With 5 values, only need to identify first 3 to know median
    for (byte i = 0; i <= 2; i++)
    {
      voltageInd[i] = 1;
      for (byte j = 1; j <= 4; j++)
      {

        if (i == 0 && voltageArray[j] > voltageArray[voltageInd[i]])
        {
          voltageInd[i] = j;
        }

        else if (i == 1 && j != voltageInd[0] && voltageArray[j] > voltageArray[voltageInd[i]])
        {
          voltageInd[i] = j;
        }

        else if (i == 2 && j != voltageInd[0] && j != voltageInd[1] && voltageArray[j] > voltageArray[voltageInd[i]])
        {
          voltageInd[i] = j;
        }
      }
    }


    payload.batteryLevel = voltageArray[voltageInd[2]] * adcToVolt;
    counter = 1439;
  }
  else
  {
    counter = counter - 1;
  }

  radio.powerUp();
  radio.write( &payload, sizeof(payload) );
  radio.powerDown();

  LowPower.powerDown(SLEEP_8S, ADC_OFF, BOD_OFF);
  LowPower.powerDown(SLEEP_8S, ADC_OFF, BOD_OFF);
  LowPower.powerDown(SLEEP_8S, ADC_OFF, BOD_OFF);
  LowPower.powerDown(SLEEP_8S, ADC_OFF, BOD_OFF);
  LowPower.powerDown(SLEEP_8S, ADC_OFF, BOD_OFF);
  LowPower.powerDown(SLEEP_8S, ADC_OFF, BOD_OFF);
  LowPower.powerDown(SLEEP_8S, ADC_OFF, BOD_OFF);
}







// Code adapted from Adafruit_SHT31 library (v2.0.0)
/**
   Gets the current status register contents.

   @return The 16-bit status register.
*/
uint16_t readStatus(void) {
  writeCommand(0xF32D); /**< Read Out of Status Register */

  uint8_t dataArray[3];

  size_t recv = Wire.requestFrom(0x44, sizeof(dataArray));
  if (recv != sizeof(dataArray)) {
  }

  for (uint16_t i = 0; i < sizeof(dataArray); i++) {
    dataArray[i] = Wire.read();
  }

  uint16_t stat = dataArray[0];
  stat <<= 8;
  stat |= dataArray[1];
  return stat;
}


/**
   Internal function to perform and I2C write.
   @param cmd   The 16-bit command ID to send.
*/
void writeCommand(uint16_t command) {
  uint8_t cmd[2];

  cmd[0] = command >> 8;
  cmd[1] = command & 0xFF;

  Wire.beginTransmission(0x44);
  Wire.write(cmd, 2);
  Wire.endTransmission();
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

