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

CREATE TABLE sensorData(ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, GMT DATETIME NOT NULL, de
cidegreesInternal SMALLINT, pressureInternal SMALLINT, humidityInternal TINYINT UNSIGNED, decidegreesExternal SMALLINT,
humidityExternal TINYINT UNSIGNED, PRIMARY KEY (ID)); <br>

create index by_GMT on sensorData (GMT);<br>

CREATE TABLE dailyExtremes(ID MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT, sampledDate DATE NOT NULL, decidegreesInternalLow SMALLINT, decidegreesInternalHigh SMALLINT, pressureInternalLow SMALLINT UNSIGNED, pressureInternalHigh SMALLINT UNSIGNED, humidityInternalLow TINYINT UNSIGNED, humidityInternalHigh TINYINT UNSIGNED, decidegreesExternalLow SMALLINT, decidegreesExternalHigh SMALLINT, humidityExternalLow TINYINT UNSIGNED, humidityExternalHigh TINYINT UNSIGNED, voltageTempSensor SMALLINT, PRIMARY KEY(ID));

create index by_date on dailyExtremes (sampledDate);

quit <br>



# Secure database. Accept all suggestions.
sudo mysql_secure_installation



# Start service to run sampling script on boot

sudo cp weatherStation.service /etc/systemd/system/weatherStation.service
sudo systemctl start weatherStation.service






# If reverting to holding some variables in RAM (e.g. current voltage) rather than writing to DB
Goal is to have a 16KB directory in RAM for temporarily storage.
First create the tmp dir:

 sudo mkdir /var/weatherTmp

then edit the fstab file by

 sudo nano /etc/fstab

and add the line

 tmpfs /home/pi/pi_weather_station/weatherTmp tmpfs nodev,nosuid,size=16K 0 0 

save and close the file. Now issue

 sudo mount -a

To check if your operation succeeded issue
 df



# Git
Git add .
Git commit -m "Commit description "
Git add -u      # for deleted files 
