# Setup for pi zero W

# Enable ssh
    If running headless, add empty file named "ssh" to boot directory (no file extension)
    
# Add wifi details
    If running headless, create file named "wpa_supplicant.conf" in the boot directory. It must contain the following...
    
ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev
update_config=1
country=GB

network={
 ssid="<Name of your wireless LAN>"
 psk="<Password for your wireless LAN>"
}

# Modify /boot/config.txt

Disable sound: dtparam=audio=off
Disable bluetooth: dtoverlay=disable-bt
Disable on board LED: dtparam=act_led_trigger=none
                      dtparam=act_led_active=on
Set gpu memory to 16mb as running headless: gpu_mem=16
Enable SPI for nrf chip: dtparam=spi=on
Enable I2C for bme280 chip: dtparam=i2c_arm=on


# Create systemd file to disable HDMI port on boot
sudo nano /etc/systemd/system/disableHDMI.service

Add...


*******************************************************

[Unit]
Description=Disable Raspberry Pi HDMI port

[Service]
Type=oneshot
ExecStart=/opt/vc/bin/tvservice -o
ExecStop=/opt/vc/bin/tvservice -p
RemainAfterExit=yes

[Install]
WantedBy=default.target

*******************************************************

Enable...

sudo systemctl enable /etc/systemd/system/disableHDMI.service

Can be tested with...

tvservice -s    (after reboot)




 



# Setup webserver
sudo apt-get install mariadb-server <br>
sudo apt-get install python3-mysqldb <br>
sudo apt-get install apache2 libapache2-mod-php php7.3-mysqli <br>

then change working directory for apache2 (/var/www/html to html folder of repo i.e. /home/pi/pi_weather_station/html) in 2 locations... <br>
sudo nano /etc/apache2/sites-available/000-default.conf <br>
sudo nano /etc/apache2/apache2.conf <br>
 

# Open MySQL
sudo mariadb <br>
CREATE DATABASE weatherLog; <br>
USE weatherLog; <br>

CREATE TABLE sensorData(ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, GMT DATETIME NOT NULL, decidegreesInternal SMALLINT, pressureInternal SMALLINT, humidityInternal TINYINT UNSIGNED, decidegreesExternal SMALLINT,
humidityExternal TINYINT UNSIGNED, PRIMARY KEY (ID)); <br>

create index by_GMT on sensorData (GMT);<br>

CREATE TABLE dailyExtremes(ID MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT, sampledDate DATE NOT NULL, decidegreesInternalLow SMALLINT, decidegreesInternalHigh SMALLINT, pressureInternalLow SMALLINT UNSIGNED, pressureInternalHigh SMALLINT UNSIGNED, humidityInternalLow TINYINT UNSIGNED, humidityInternalHigh TINYINT UNSIGNED, decidegreesExternalLow SMALLINT, decidegreesExternalHigh SMALLINT, humidityExternalLow TINYINT UNSIGNED, humidityExternalHigh TINYINT UNSIGNED, voltageTempSensor SMALLINT, PRIMARY KEY(ID));

create index by_date on dailyExtremes (sampledDate);

CREATE USER 'database_writer'@'localhost';<br>	
GRANT INSERT ON weather_records.* TO 'database_writer'@'localhost'; <br>	
CREATE USER 'database_reader'@'localhost'; <br>		
GRANT SELECT ON weather_records.* TO 'database_reader'@'localhost'; <br>
GRANT SELECT ON weather_records.* TO 'database_writer'@'localhost'; <br>
FLUSH PRIVILEGES; <br>

quit; <br>



# Secure database. Accept all suggestions.
sudo mysql_secure_installation

# Install RF24 code
If updating etc get new version of code
e.g. wget https://github.com/nRF24/RF24/archive/v1.3.9.zip
unzip v1.3.9.zip
rm v1.3.9.zip

...otherwise can just use folder included here.

cd RF24-1.3.9/
./configure
make
sudo make install

sudo apt-get install python3-dev libboost-python-dev python3-setuptools python3-rpi.gpio 
sudo ln -s $(ls /usr/lib/arm-linux-gnueabihf/libboost_python3-py3*.so | tail -1) /usr/lib/arm-linux-gnueabihf/libboost_python3.so 
cd RF24-1.3.9/pyRF24/
python3 setup.py build
sudo python3 setup.py install


# bme280
sudo apt install python3-pip
sudo pip3 install pimoroni-bme280 smbus



# ToDo
Need to remove code from getStatus referencing the temporary file voltage.txt
Check if still used elsewhere
Don't initially display status page under current data
Show error when there is no data, rather than NaNs/undefined's

Start service to run sampling script on boot
-  sudo cp weatherStation.service /etc/systemd/system/weatherStation.service
-  sudo systemctl enable weatherStation.service


# Git
Git add .
Git commit -m "Commit description "
Git add -u      # for deleted files 
