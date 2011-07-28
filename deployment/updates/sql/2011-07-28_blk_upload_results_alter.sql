
ALTER TABLE bulk_upload_result
CHANGE entry_id object_id VARCHAR(20) NULL,
ADD object_type INTEGER(11) NULL AFTER object_id DEFAULT '1',
ADD `action` INTEGER(11) NULL AFTER object_type DEFAULT '1',
ADD custom_data TEXT NULL AFTER plugins_data;

