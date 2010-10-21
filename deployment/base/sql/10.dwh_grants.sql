GRANT ALL ON *.* TO 'etl'@'localhost' IDENTIFIED BY 'etl';
GRANT ALL ON *.* TO 'etl'@'%' IDENTIFIED BY 'etl';
GRANT SELECT ON *.* TO 'kaltura_read'@'localhost' IDENTIFIED BY 'kaltura_read';
FLUSH privileges;