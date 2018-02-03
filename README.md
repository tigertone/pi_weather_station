# arduino serial connection
turn off serial in sudo raspi-config to prevent serial data being sent to arduino during startup <br>

# Pi webserver
sudo apt-get install mysql-server <br>
sudo apt-get install python3-mysqldb <br>

sudo apt-get install apache2 libapache2-mod-php7.0 <br>

# Open MySQL
sudo mysql -p <br>
CREATE DATABASE weather_records; <br>
USE weather_records; <br>

CREATE TABLE sensor_data(ID MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT, GMT DATETIME NOT NULL, decidegrees SMALLINT, pressure SMALLINT, humidity TINYINT UNSIGNED, PRIMARY KEY (ID)); <br>

create index by_GMT on sensor_data (GMT);<br>

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
