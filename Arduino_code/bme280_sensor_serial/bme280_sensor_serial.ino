#include "stdint.h"
#include <math.h>
#include "Wire.h"

#define DIG_T1_LSB_REG 0x88
#define DIG_T1_MSB_REG 0x89
#define DIG_T2_LSB_REG 0x8A
#define DIG_T2_MSB_REG 0x8B
#define DIG_T3_LSB_REG 0x8C
#define DIG_T3_MSB_REG 0x8D
#define DIG_P1_LSB_REG 0x8E
#define DIG_P1_MSB_REG 0x8F
#define DIG_P2_LSB_REG 0x90
#define DIG_P2_MSB_REG 0x91
#define DIG_P3_LSB_REG 0x92
#define DIG_P3_MSB_REG 0x93
#define DIG_P4_LSB_REG 0x94
#define DIG_P4_MSB_REG 0x95
#define DIG_P5_LSB_REG 0x96
#define DIG_P5_MSB_REG 0x97
#define DIG_P6_LSB_REG 0x98
#define DIG_P6_MSB_REG 0x99
#define DIG_P7_LSB_REG 0x9A
#define DIG_P7_MSB_REG 0x9B
#define DIG_P8_LSB_REG 0x9C
#define DIG_P8_MSB_REG 0x9D
#define DIG_P9_LSB_REG 0x9E
#define DIG_P9_MSB_REG 0x9F
#define DIG_H1_REG 0xA1
#define CHIP_ID_REG 0xD0 //Chip ID
#define RST_REG 0xE0 //Softreset Reg
#define DIG_H2_LSB_REG 0xE1
#define DIG_H2_MSB_REG 0xE2
#define DIG_H3_REG 0xE3
#define DIG_H4_MSB_REG 0xE4
#define DIG_H4_LSB_REG 0xE5
#define DIG_H5_MSB_REG 0xE6
#define DIG_H6_REG 0xE7
#define CTRL_HUMIDITY_REG 0xF2 //Ctrl Humidity Reg
#define STAT_REG 0xF3 //Status Reg
#define CTRL_MEAS_REG 0xF4 //Ctrl Measure Reg
#define CONFIG_REG 0xF5 //Configuration Reg
#define PRESSURE_MSB_REG 0xF7 //Pressure MSB
#define PRESSURE_LSB_REG 0xF8 //Pressure LSB
#define PRESSURE_XLSB_REG 0xF9 //Pressure XLSB
#define TEMPERATURE_MSB_REG 0xFA //Temperature MSB
#define TEMPERATURE_LSB_REG 0xFB //Temperature LSB
#define TEMPERATURE_XLSB_REG 0xFC //Temperature XLSB
#define HUMIDITY_MSB_REG 0xFD //Humidity MSB
#define HUMIDITY_LSB_REG 0xFE //Humidity LSB

#define I2CAddress 0x77

uint16_t dig_T1;
int16_t dig_T2;
int16_t dig_T3;

uint16_t dig_P1;
int16_t dig_P2;
int16_t dig_P3;
int16_t dig_P4;
int16_t dig_P5;
int16_t dig_P6;
int16_t dig_P7;
int16_t dig_P8;
int16_t dig_P9;

uint8_t dig_H1;
int16_t dig_H2;
uint8_t dig_H3;
int16_t dig_H4;
int16_t dig_H5;
uint8_t dig_H6;

uint8_t dataToWrite;
int32_t t_fine;



void setup() 
{

  dataToWrite = 0;
  
  dig_T1 = ((uint16_t)((readRegister(DIG_T1_MSB_REG) << 8) + readRegister(DIG_T1_LSB_REG)));
  dig_T2 = ((int16_t)((readRegister(DIG_T2_MSB_REG) << 8) + readRegister(DIG_T2_LSB_REG)));
  dig_T3 = ((int16_t)((readRegister(DIG_T3_MSB_REG) << 8) + readRegister(DIG_T3_LSB_REG)));
  
  dig_P1 = ((uint16_t)((readRegister(DIG_P1_MSB_REG) << 8) + readRegister(DIG_P1_LSB_REG)));
  dig_P2 = ((int16_t)((readRegister(DIG_P2_MSB_REG) << 8) + readRegister(DIG_P2_LSB_REG)));
  dig_P3 = ((int16_t)((readRegister(DIG_P3_MSB_REG) << 8) + readRegister(DIG_P3_LSB_REG)));
  dig_P4 = ((int16_t)((readRegister(DIG_P4_MSB_REG) << 8) + readRegister(DIG_P4_LSB_REG)));
  dig_P5 = ((int16_t)((readRegister(DIG_P5_MSB_REG) << 8) + readRegister(DIG_P5_LSB_REG)));
  dig_P6 = ((int16_t)((readRegister(DIG_P6_MSB_REG) << 8) + readRegister(DIG_P6_LSB_REG)));
  dig_P7 = ((int16_t)((readRegister(DIG_P7_MSB_REG) << 8) + readRegister(DIG_P7_LSB_REG)));
  dig_P8 = ((int16_t)((readRegister(DIG_P8_MSB_REG) << 8) + readRegister(DIG_P8_LSB_REG)));
  dig_P9 = ((int16_t)((readRegister(DIG_P9_MSB_REG) << 8) + readRegister(DIG_P9_LSB_REG)));
  
  dig_H1 = ((uint8_t)(readRegister(DIG_H1_REG)));
  dig_H2 = ((int16_t)((readRegister(DIG_H2_MSB_REG) << 8) + readRegister(DIG_H2_LSB_REG)));
  dig_H3 = ((uint8_t)(readRegister(DIG_H3_REG)));
  dig_H4 = ((int16_t)((readRegister(DIG_H4_MSB_REG) << 4) + (readRegister(DIG_H4_LSB_REG) & 0x0F)));
  dig_H5 = ((int16_t)((readRegister(DIG_H5_MSB_REG) << 4) + ((readRegister(DIG_H4_LSB_REG) >> 4) & 0x0F)));
  dig_H6 = ((uint8_t)readRegister(DIG_H6_REG));
  


  //Set the oversampling control words.
  //config will only be writeable in sleep mode, so first insure that.
  writeRegister(CTRL_MEAS_REG, 0x00);
  
  //Set the config word
  dataToWrite = (0 << 0x5) & 0xE0;
  dataToWrite |= (0 << 0x02) & 0x1C;
  writeRegister(CONFIG_REG, dataToWrite);
  
  //Set ctrl_hum first, then ctrl_meas to activate ctrl_hum
  dataToWrite = 1 & 0x07; //all other bits can be ignored
  writeRegister(CTRL_HUMIDITY_REG, dataToWrite);
  
  //set ctrl_meas
  //First, set temp oversampling
  dataToWrite = (1 << 0x5) & 0xE0;
  //Next, pressure oversampling
  dataToWrite |= (1 << 0x02) & 0x1C;
  
  Serial.begin(115200);
  Wire.begin();
}

void loop() 
{
  // need to calc temp first so can be used to compensate other values
  int temp = readTempC();
  byte humidity = readFloatHumidity();
  int pressure = readFloatPressure();
  
  // print data:
  Serial.println("start");
  Serial.println(temp);
  Serial.println(humidity);
  Serial.println(pressure);
  delay(900);    
}

//****************************************************************************//
//
//  Pressure Section
//
//****************************************************************************//
int readFloatPressure( void )
{

  // Returns pressure in Pa as unsigned 32 bit integer in Q24.8 format (24 integer bits and 8 fractional bits).
  // Output value of “24674867” represents 24674867/256 = 96386.2 Pa = 963.862 hPa
  int32_t adc_P = ((uint32_t)readRegister(PRESSURE_MSB_REG) << 12) | ((uint32_t)readRegister(PRESSURE_LSB_REG) << 4) | ((readRegister(PRESSURE_XLSB_REG) >> 4) & 0x0F);
  
  int64_t var1, var2, p_acc;
  var1 = ((int64_t)t_fine) - 128000;
  var2 = var1 * var1 * (int64_t)dig_P6;
  var2 = var2 + ((var1 * (int64_t)dig_P5)<<17);
  var2 = var2 + (((int64_t)dig_P4)<<35);
  var1 = ((var1 * var1 * (int64_t)dig_P3)>>8) + ((var1 * (int64_t)dig_P2)<<12);
  var1 = (((((int64_t)1)<<47)+var1))*((int64_t)dig_P1)>>33;
  if (var1 == 0)
  {
    return 0; // avoid exception caused by division by zero
  }
  p_acc = 1048576 - adc_P;
  p_acc = (((p_acc<<31) - var2)*3125)/var1;
  var1 = (((int64_t)dig_P9) * (p_acc>>13) * (p_acc>>13)) >> 25;
  var2 = (((int64_t)dig_P8) * p_acc) >> 19;
  p_acc = ((p_acc + var1 + var2) >> 8) + (((int64_t)dig_P7)<<4);
  
  p_acc = p_acc >> 8; // 
  (float)p_acc / 256 / 100;
  return int(p_acc);
  
}

//****************************************************************************//
//
//  Humidity Section
//
//****************************************************************************//
byte readFloatHumidity( void )
{
  
  // Returns humidity in %RH as unsigned 32 bit integer in Q22. 10 format (22 integer and 10 fractional bits).
  // Output value of “47445” represents 47445/1024 = 46. 333 %RH
  int32_t adc_H = ((uint32_t)readRegister(HUMIDITY_MSB_REG) << 8) | ((uint32_t)readRegister(HUMIDITY_LSB_REG));
  
  int32_t var1;
  var1 = (t_fine - ((int32_t)76800));
  var1 = (((((adc_H << 14) - (((int32_t)dig_H4) << 20) - (((int32_t)dig_H5) * var1)) +
  ((int32_t)16384)) >> 15) * (((((((var1 * ((int32_t)dig_H6)) >> 10) * (((var1 * ((int32_t)dig_H3)) >> 11) + ((int32_t)32768))) >> 10) + ((int32_t)2097152)) *
  ((int32_t)dig_H2) + 8192) >> 14));
  var1 = (var1 - (((((var1 >> 15) * (var1 >> 15)) >> 7) * ((int32_t)dig_H1)) >> 4));
  var1 = (var1 < 0 ? 0 : var1);
  var1 = (var1 > 419430400 ? 419430400 : var1);
  float humidity = ((var1>>12) >> 10) / 1024;
  return (byte)humidity;

}



//****************************************************************************//
//
//  Temperature Section
//
//****************************************************************************//

int readTempC( void )
{
  // Returns temperature in DegC, resolution is 0.01 DegC. Output value of “5123” equals 51.23 DegC.

  //get the reading (adc_T);
  int32_t adc_T = ((uint32_t)readRegister(TEMPERATURE_MSB_REG) << 12) | ((uint32_t)readRegister(TEMPERATURE_LSB_REG) << 4) | ((readRegister(TEMPERATURE_XLSB_REG) >> 4) & 0x0F);

  //By datasheet, calibrate
  int64_t var1, var2;

  var1 = ((((adc_T>>3) - ((int32_t)dig_T1<<1))) * ((int32_t)dig_T2)) >> 11;
  var2 = (((((adc_T>>4) - ((int32_t)dig_T1)) * ((adc_T>>4) - ((int32_t)dig_T1))) >> 12) *
  ((int32_t)dig_T3)) >> 14;
  t_fine = var1 + var2;
  float output = (t_fine * 5 + 128) >> 8;

  output = output / 100;
  
  return (int)output;
}

//****************************************************************************//
//
//  Utility
//
//****************************************************************************//
void readRegisterRegion(uint8_t *outputPointer , uint8_t offset, uint8_t length)
{
  //define pointer that will point to the external space
  uint8_t i = 0;
  char c = 0;

    Wire.beginTransmission(I2CAddress);
    Wire.write(offset);
    Wire.endTransmission();

    // request bytes from slave device
    Wire.requestFrom(I2CAddress, length);
    while ( (Wire.available()) && (i < length))  // slave may send less than requested
    {
      c = Wire.read(); // receive a byte as character
      *outputPointer = c;
      outputPointer++;
      i++;
    }
}



uint8_t readRegister(uint8_t offset)
{
  //Return value
  uint8_t result;
  uint8_t numBytes = 1;
    Wire.beginTransmission(I2CAddress);
    Wire.write(offset);
    Wire.endTransmission();

    Wire.requestFrom(I2CAddress, numBytes);
    while ( Wire.available() ) // slave may send less than requested
    {
      result = Wire.read(); // receive a byte as a proper uint8_t
    }
  return result;
}

int16_t readRegisterInt16( uint8_t offset )
{
  uint8_t myBuffer[2];
  readRegisterRegion(myBuffer, offset, 2);  //Does memory transfer
  int16_t output = (int16_t)myBuffer[0] | int16_t(myBuffer[1] << 8);
  return output;
}

void writeRegister(uint8_t offset, uint8_t dataToWrite)
{
    //Write the byte
    Wire.beginTransmission(I2CAddress);
    Wire.write(offset);
    Wire.write(dataToWrite);
    Wire.endTransmission();
}
