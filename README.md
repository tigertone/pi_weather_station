# arduino serial connection
turn off serial in sudo raspi-config to prevent serial data being sent to arduino during startup <br>

# Pi webserver
sudo apt-get install mysql-server <br>
sudo apt-get install python3-mysqldb <br>

sudo apt-get install apache2 libapache2-mod-php7.0 php-mysqli <br>


# Clone git repo

then change working directory for apache2 (/var/www/html to php folder of repo) in 2 locations... <br>
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



Install latest version of node.js
npm install chart.js --save
