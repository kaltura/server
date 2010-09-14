INSERT INTO `mysql`.`user` SET `Host`='%', `User`='kaltura', `Password`=PASSWORD('kaltura'), `max_questions`=0, `max_updates`=0, `max_connections`=0, `max_user_connections`=0, `ssl_cipher`='', `x509_issuer`='', `x509_subject`='' ;
/* Applying privilege changes... */
/* Applying privilege to account kaltura@% for Server privileges. */
INSERT INTO `mysql`.`user` SET `Host`='%', `User`='kaltura', `Reload_priv`='Y', `Shutdown_priv`='Y', `Process_priv`='Y', `File_priv`='Y', `Show_db_priv`='Y', `Super_priv`='N', `Repl_slave_priv`='Y', `Repl_client_priv`='Y', `Create_user_priv`='Y', `ssl_cipher`='', `x509_issuer`='', `x509_subject`='' ON DUPLICATE KEY UPDATE `Reload_priv`='Y', `Shutdown_priv`='Y', `Process_priv`='Y', `File_priv`='Y', `Show_db_priv`='Y', `Super_priv`='N', `Repl_slave_priv`='Y', `Repl_client_priv`='Y', `Create_user_priv`='Y' ;
/* Applying privilege to account kaltura@% for All databases. */
UPDATE `mysql`.`user` SET `Select_priv`='N', `Insert_priv`='N', `Update_priv`='N', `Delete_priv`='N', `Create_priv`='N', `Drop_priv`='N', `Grant_priv`='N', `References_priv`='N', `Index_priv`='N', `Alter_priv`='N', `Create_tmp_table_priv`='N', `Lock_tables_priv`='N', `Execute_priv`='N', `Create_view_priv`='N', `Show_view_priv`='N', `Create_routine_priv`='N', `Alter_routine_priv`='N', `Event_priv`='N', `Trigger_priv`='N' WHERE `User`='kaltura' AND (`Host`='%' OR `Host`='') ;
DELETE FROM `mysql`.`db` WHERE `User`='kaltura' AND (`Host`='%' OR `Host`='') AND `Db`='%' ;
INSERT INTO `mysql`.`db` SET `Host`='%', `User`='kaltura', `Db`='%', `Select_priv`='Y', `Insert_priv`='Y', `Update_priv`='Y', `Delete_priv`='Y', `Create_priv`='Y', `Drop_priv`='Y', `Grant_priv`='Y', `References_priv`='Y', `Index_priv`='Y', `Alter_priv`='Y', `Create_tmp_table_priv`='Y', `Lock_tables_priv`='Y', `Execute_priv`='Y', `Create_view_priv`='Y', `Show_view_priv`='Y', `Create_routine_priv`='Y', `Alter_routine_priv`='Y', `Event_priv`='Y', `Trigger_priv`='Y' ;
FLUSH PRIVILEGES ;



INSERT INTO `mysql`.`user` SET `Host`='localhost', `User`='kaltura', `Password`=PASSWORD(''), `max_questions`=0, `max_updates`=0, `max_connections`=0, `max_user_connections`=0, `ssl_cipher`='', `x509_issuer`='', `x509_subject`='' ;
/* Applying privilege changes... */
/* Applying privilege to account kaltura@localhost for Server privileges. */
INSERT INTO `mysql`.`user` SET `Host`='localhost', `User`='kaltura', `Reload_priv`='Y', `Shutdown_priv`='Y', `Process_priv`='Y', `File_priv`='Y', `Show_db_priv`='Y', `Super_priv`='N', `Repl_slave_priv`='Y', `Repl_client_priv`='Y', `Create_user_priv`='Y', `ssl_cipher`='', `x509_issuer`='', `x509_subject`='' ON DUPLICATE KEY UPDATE `Reload_priv`='Y', `Shutdown_priv`='Y', `Process_priv`='Y', `File_priv`='Y', `Show_db_priv`='Y', `Super_priv`='N', `Repl_slave_priv`='Y', `Repl_client_priv`='Y', `Create_user_priv`='Y' ;
/* Applying privilege to account kaltura@localhost for All databases. */
UPDATE `mysql`.`user` SET `Select_priv`='N', `Insert_priv`='N', `Update_priv`='N', `Delete_priv`='N', `Create_priv`='N', `Drop_priv`='N', `Grant_priv`='N', `References_priv`='N', `Index_priv`='N', `Alter_priv`='N', `Create_tmp_table_priv`='N', `Lock_tables_priv`='N', `Execute_priv`='N', `Create_view_priv`='N', `Show_view_priv`='N', `Create_routine_priv`='N', `Alter_routine_priv`='N', `Event_priv`='N', `Trigger_priv`='N' WHERE `User`='kaltura' AND (`Host`='localhost') ;
DELETE FROM `mysql`.`db` WHERE `User`='kaltura' AND (`Host`='localhost') AND `Db`='%' ;
INSERT INTO `mysql`.`db` SET `Host`='localhost', `User`='kaltura', `Db`='%', `Select_priv`='Y', `Insert_priv`='Y', `Update_priv`='Y', `Delete_priv`='Y', `Create_priv`='Y', `Drop_priv`='Y', `Grant_priv`='Y', `References_priv`='Y', `Index_priv`='Y', `Alter_priv`='Y', `Create_tmp_table_priv`='Y', `Lock_tables_priv`='Y', `Execute_priv`='Y', `Create_view_priv`='Y', `Show_view_priv`='Y', `Create_routine_priv`='Y', `Alter_routine_priv`='Y', `Event_priv`='Y', `Trigger_priv`='Y' ;
FLUSH PRIVILEGES ;

GRANT SELECT ON *.* TO 'kaltura_read'@'%' IDENTIFIED BY PASSWORD '26c58c9d1f00319c';
flush privileges ;