delimiter ';;'
DROP PROCEDURE IF EXISTS version_management_schema_change;
CREATE PROCEDURE version_management_schema_change() BEGIN

        SET @schema_name = 'kaltura';
        SET @table_name = 'version_management';
        IF EXISTS (SELECT * from information_schema.columns where table_schema = @schema_name and table_name = @table_name and column_name = 'version') THEN
                ALTER TABLE kaltura.version_management DROP column `version`;
        END IF;
        IF NOT EXISTS (SELECT * from information_schema.columns where table_schema = @schema_name and table_name = @table_name and column_name = 'id') THEN
                ALTER TABLE kaltura.version_management DROP PRIMARY KEY;
                ALTER TABLE kaltura.version_management ADD COLUMN  id INT(11) NOT NULL AUTO_INCREMENT primary key first;
        END IF;
        IF NOT EXISTS (SELECT * from information_schema.columns where table_schema = @schema_name and table_name = @table_name and column_name = 'status') THEN
                ALTER TABLE kaltura.version_management ADD status TINYINT(4) DEFAULT NULL;
        END IF;
        IF NOT EXISTS (SELECT * from information_schema.columns where table_schema = @schema_name and table_name = @table_name and column_name = 'server_version') THEN
                ALTER TABLE kaltura.version_management ADD server_version VARCHAR(20) DEFAULT NULL;
        END IF;

END;;

call version_management_schema_change();
DROP PROCEDURE IF EXISTS version_management_schema_change;
