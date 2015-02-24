
CREATE TABLE IF NOT EXISTS business_process_server
(
	id INTEGER  NOT NULL AUTO_INCREMENT,
	created_at DATETIME,
	updated_at DATETIME,
	partner_id INTEGER NOT NULL,
	name VARCHAR(31),
	system_name VARCHAR(127),
	description VARCHAR(255),
	status TINYINT,
	type INTEGER,
	custom_data TEXT,
	PRIMARY KEY (id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
