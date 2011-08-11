
ALTER TABLE bulk_upload_result
CHANGE entry_id object_id VARCHAR(20) NULL,
ADD object_type INTEGER(11) NULL DEFAULT '1' AFTER object_id,
ADD `action` INTEGER(11) NULL DEFAULT '1' AFTER object_type,
ADD custom_data TEXT NULL AFTER plugins_data;

