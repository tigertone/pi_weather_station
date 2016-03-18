# arduino serial connection

# turn off serial in sudo raspi-config to prevent serial data being sent to arduino during startup

# pi_webserver

# sudo apt-get install mysql-server
# Create password
# sudo apt-get install python-mysqldb
# read difference between Apache and nginx

# If using Apache
# sudo apt-get install apache2 php5 libapache2-mod-php5
# sudo apt-get install phpmyadmin
#    Select apache2 webserver

# Open MySQL
# sudo mysql –u –p
# CREATE DATABASE weather_records;
# USE weather_records;

# CREATE TABLE sensor_data(ID MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT, GMT DATETIME NOT NULL, decidegrees SMALLINT, pressure SMALLINT, humidity TINYINT UNSIGNED, PRIMARY KEY (ID));

# CREATE USER 'database_writer'@'localhost' IDENTIFIED BY 'PASSWORD';
# GRANT INSERT ON weather_records.* TO 'database_writer'@'localhost';
# FLUSH PRIVILEGES;
#quit

# to view table
# select * from sensor_data;
