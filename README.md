Setup
Goal is to have a 1MB directory in RAM for temporarily storage.
First create the tmp dir:

 sudo mkdir /var/weatherTmp

then edit the fstab file by

 sudo nano /etc/fstab

and add the line

 tmpfs /var/weatherTmp tmpfs nodev,nosuid,size=1M 0 0 

save and close the file. Now issue

 sudo mount -a

To check if your operation succeeded issue
 df

# arduino serial connection
turn off serial in sudo raspi-config to prevent serial data being sent to arduino during startup <br>

# Pi webserver
sudo apt-get install mysql-server <br>
sudo apt-get install python3-mysqldb <br>

sudo apt-get install apache2 libapache2-mod-php7.0 php-mysqli <br>


# Clone git repo

then change working directory for apache2 (/var/www/html to html folder of repo) in 2 locations... <br>
sudo nano /etc/apache2/sites-available/000-default.conf <br>
udo nano /etc/apache2/apache2.conf <br>
 

# Open MySQL
sudo mysql -p <br>
CREATE DATABASE weather_records; <br>
USE weather_records; <br>

CREATE TABLE sensor_data(ID MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT, GMT DATETIME NOT NULL, decidegrees SMALLINT, pressure SMALLINT, humidity TINYINT UNSIGNED, PRIMARY KEY (ID)); <br>

create index by_GMT on sensor_data (GMT);<br>

CREATE TABLE dailyExtremes(ID MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT, sampledDate DATE NOT NULL, decidegreesLow SMALLINT, decidegreesHigh SMALLINT, pressureLow SMALLINT UNSIGNED, pressureHigh SMALLINT UNSIGNED, humidityLow TINYINT UNSIGNED, humidityHigh TINYINT UNSIGNED, PRIMARY KEY (ID));

create index by_date on dailyExtremes (sampledDate);

# If adding data at a later date
INSERT INTO dailyExtremes (sampledDate, decidegreesHigh, decidegreesLow,pressureHigh,pressureLow,humidityHigh,humidityLow) SELECT DATE(GMT) as day, max(decidegrees) as maxTemp, MIN(decidegrees) as minTemp, max(Pressure) as maxPressure, MIN(Pressure) as minPressure, max(Humidity) as maxHumidity, MIN(Humidity) as minHumidity from sensor_data WHERE GMT > DATE_SUB(NOW(), INTERVAL 1 YEAR) group by day;



CREATE USER 'database_writer'@'localhost' IDENTIFIED BY 'PASSWORD'; <br>
GRANT INSERT ON weather_records.* TO 'database_writer'@'localhost'; <br>
CREATE USER 'database_reader'@'localhost' IDENTIFIED BY 'PASSWORD'; <br>
GRANT SELECT ON weather_records.* TO 'database_reader'@'localhost'; <br>
FLUSH PRIVILEGES; <br>
quit <br>

# To view table
select * from sensor_data; <br>

# Copy php file so that it can be found by apache
cd /var/www/html <br>
sudo rm index.html <br>
sudo nano display_current_data.php <br>



# Install latest version of node.js
npm install chart.js --save<br>
cp ./node_modules/chart.js/dist/Chart.bundle.js ./pi_weather_station/php/chartJs_2.7.2		% Copy to php folder<br>
cp ./node_modules/chart.js/dist/Chart.bundle.min.js ./pi_weather_station/php/chartJs_2.7.2<br>



# Start service to run sampling script on boot

sudo cp weatherStation.service /etc/systemd/system/weatherStation.service
sudo systemctl start weatherStation.service

# Git
Git add .
Git commit -m "Commit description "
Git add -u      # for deleted files 
