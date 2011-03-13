GRANT ALL ON *.* TO 'etl'@'@DB1_HOST@' IDENTIFIED BY 'etl';
GRANT ALL ON *.* TO 'etl'@'%' IDENTIFIED BY 'etl';
GRANT SELECT ON *.* TO 'kaltura_read'@'@DB1_HOST@' IDENTIFIED BY 'kaltura_read';
FLUSH privileges;
COMMIT;