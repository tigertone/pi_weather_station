from bme280 import *
sensor = BME280(t_mode=BME280_OSAMPLE_16, p_mode=BME280_OSAMPLE_16, h_mode=BME280_OSAMPLE_16)
degrees = sensor.read_temperature()
pascals = sensor.read_pressure()
hectopascals = pascals / 100
humidity = sensor.read_humidity()
print(degrees)
print(pascals)
print(humidity)
