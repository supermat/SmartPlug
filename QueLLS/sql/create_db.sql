CREATE DATABASE smartplug;
USE smartplug;
CREATE USER 'sp_admin'@'localhost' IDENTIFIED BY 'sp_admin';
GRANT ALL PRIVILEGES ON smartplug.* TO 'sp_admin'@'localhost';
FLUSH PRIVILEGES;
