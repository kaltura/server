SELECT count(*)
INTO @exist
FROM information_schema.columns 
WHERE table_schema = database()
AND COLUMN_NAME = 'custom_data'
AND table_name = 'upload_token';

set @query = IF(@exist <= 0, 'ALTER TABLE upload_token ADD custom_data TEXT AFTER object_id', 'select \'Column Exists\' status');

prepare stmt from @query;

EXECUTE stmt;
