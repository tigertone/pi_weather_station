## Setup for pi zero W

### Enable ssh
If running headless, add empty file named "ssh" to boot directory (no file extension)  
    
### Add wifi details
If running headless, create file named "wpa_supplicant.conf" in the boot directory. It must contain the following...
    
ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev
update_config=1
country=GB

network={
 ssid="<Name of your wireless LAN>"
 psk="<Password for your wireless LAN>"
}

### Change hostname 
`sudo raspi-config`  
Set to 'weather'  
Website will then be available at http://weather.local

### Modify boot confit

`sudo nano /boot/config.txt`  

Disable sound: dtparam=audio=off  
Disable bluetooth: dtoverlay=disable-bt  
Disable on board LED: dtparam=act_led_trigger=none  
                      dtparam=act_led_active=on  
Set gpu memory to 16mb as running headless: gpu_mem=16  
Enable SPI for nrf chip: dtparam=spi=on  
Enable I2C for bme280 chip: dtparam=i2c_arm=on  

### Install git
`sudo apt install git`  
`git clone http://github.com/tigertone/pi_weather_station`  


### Setup service to disable HDMI port on boot 
`sudo cp disableHDMI.service /etc/systemd/system/disableHDMI.service`  
`sudo systemctl enable disableHDMI.service`  

Can be tested with...

`tvservice -s`    (after reboot)


### Setup webserver
`sudo apt-get install mariadb-server`  
`sudo apt-get install python3-mysqldb`  
`sudo apt-get install apache2 libapache2-mod-php php7.3-mysqli`  

Enable http/2...

`sudo apt install php7.3-fpm`  
`sudo a2enmod proxy_fcgi setenvif`  
`sudo a2enconf php7.3-fpm`  
`sudo a2dismod php7.3`  
`sudo a2dismod mpm_prefork`   
`sudo a2enmod mpm_event`  
`sudo service apache2 restart && sudo service php7.3-fpm restart`  

then change working directory for apache2 (/var/www/html to html folder of repo i.e. /home/pi/pi_weather_station/html) in 2 locations...  
`sudo nano /etc/apache2/sites-available/000-default.conf`  
`sudo nano /etc/apache2/apache2.conf` 

`sudo nano /etc/apache2/sites-available/000-default.conf` contains the following...  

`<VirtualHost *:80>

        ServerAdmin webmaster@localhost
        DocumentRoot /home/pi/pi_weather_station/html
        Redirect permanent / https://weather.local/

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined

\</VirtualHost>

<VirtualHost *:443>

        ServerAdmin webmaster@localhost
        DocumentRoot /home/pi/pi_weather_station/html

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined

        SSLEngine on
        SSLCertificateKeyFile /home/pi/localhost-privkey.pem
        SSLCertificateFile /home/pi/localhost-cert.pem
        SSLProtocol all -SSLv3 -TLSv1 -TLSv1.1
        Protocols h2 http/1.1

\</VirtualHost>`

In `/etc/apache2/apache2.conf` also change AllowOverride none to AllowOverride all


Generate certificate 
`cd`  
`openssl req -x509 -nodes -new -sha256 -days 1024 -newkey rsa:2048 -keyout weather_CA.key -out weather_CA.pem -subj "/C=UK/CN=weather_CA"`  
`openssl x509 -outform pem -in weather_CA.pem -out weather_CA.crt`  

`openssl req -new -nodes -newkey rsa:2048 -keyout weather.key -out weather.csr -subj "/C=UK/CN=localhost.local"`  
`openssl x509 -req -sha256 -days 1024 -in localhost.csr -CA weather_CA.pem -CAkey weather_CA.key -CAcreateserial -extfile pi_weather_station/domains.ext -out localhost.crt`  

 

### Create databases
`sudo mariadb`  
`CREATE DATABASE weatherLog;`  
`USE weatherLog;`

`CREATE TABLE sensorData(ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, GMT DATETIME NOT NULL, decidegreesInternal SMALLINT, pressureInternal SMALLINT, humidityInternal TINYINT UNSIGNED, decidegreesExternal SMALLINT,
humidityExternal TINYINT UNSIGNED, PRIMARY KEY (ID));`

`create index by_GMT on sensorData (GMT);`

`CREATE TABLE dailyExtremes(ID MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT, sampledDate DATE NOT NULL, decidegreesInternalLow SMALLINT, decidegreesInternalHigh SMALLINT, pressureInternalLow SMALLINT UNSIGNED, pressureInternalHigh SMALLINT UNSIGNED, humidityInternalLow TINYINT UNSIGNED, humidityInternalHigh TINYINT UNSIGNED, decidegreesExternalLow SMALLINT, decidegreesExternalHigh SMALLINT, humidityExternalLow TINYINT UNSIGNED, humidityExternalHigh TINYINT UNSIGNED, voltageExternalTempSensor SMALLINT, PRIMARY KEY(ID));`

`create index by_date on dailyExtremes (sampledDate);`

`CREATE USER 'database_writer'@'localhost';`  	
`GRANT INSERT ON weatherLog.* TO 'database_writer'@'localhost';`  	
`CREATE USER 'database_reader'@'localhost';`  	
`GRANT SELECT ON weatherLog.* TO 'database_reader'@'localhost';`  
`GRANT SELECT ON weatherLog.* TO 'database_writer'@'localhost';`  
`FLUSH PRIVILEGES;`

`quit;`



### Secure database. Accept all suggestions.
`sudo mysql_secure_installation`

### Install RF24 code
If updating etc get new version of code
`e.g. wget https://github.com/nRF24/RF24/archive/v1.3.9.zip`  
`unzip v1.3.9.zip`  
`rm v1.3.9.zip` 

...otherwise can just use folder included here.

`cd RF24-1.3.9/`  
`./configure`  
`make`   
`sudo make install`  

`sudo apt-get install python3-dev libboost-python-dev python3-setuptools python3-rpi.gpio`  
`sudo ln -s $(ls /usr/lib/arm-linux-gnueabihf/libboost_python3-py3*.so | tail -1) /usr/lib/arm-linux-gnueabihf/libboost_python3.so`  
`cd pyRF24/`  
`python3 setup.py build`  
`sudo python3 setup.py install`  


### Install bme280 library
`sudo apt install python3-pip`  
`sudo pip3 install pimoroni-bme280 smbus`  


### Start service to run sampling script on boot
`sudo cp weatherStation.service /etc/systemd/system/weatherStation.service`  
`sudo systemctl enable weatherStation.service`  


### ToDo
Show error when there is no data, rather than NaNs/undefined's     
Align y axis on plots   
Update to python code to latest RF24 library  


### Git
`Git add .` (Add all files/folders)  
`Git commit -m "Commit description "`  
`Git add -u`  (Unmount deleted files)  
