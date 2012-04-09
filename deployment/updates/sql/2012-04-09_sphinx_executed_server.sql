ALTER TABLE sphinx_log 
ADD executed_server_id	INT				NOT NULL AFTER id,
ADD object_type			VARCHAR (255)	NOT NULL AFTER executed_server_id,
ADD object_id			VARCHAR (20)	NOT NULL AFTER object_type;
