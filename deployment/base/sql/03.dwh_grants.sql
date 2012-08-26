GRANT ALL ON *.* TO 'root'@'localhost' IDENTIFIED BY '';
GRANT ALL ON *.* TO 'root'@'%' IDENTIFIED BY '';
GRANT SELECT ON *.* TO 'kaltura_read'@'localhost' IDENTIFIED BY 'kaltura_read';
FLUSH privileges;
COMMIT;