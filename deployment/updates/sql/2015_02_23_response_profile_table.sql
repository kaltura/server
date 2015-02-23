
CREATE TABLE response_profile
(
	id BIGINT  NOT NULL AUTO_INCREMENT,
	created_at DATETIME,
	updated_at DATETIME,
	partner_id INTEGER,
	status INTEGER,
	name VARCHAR(255),
	system_name VARCHAR(255),
	type INTEGER,
	custom_data TEXT,
	PRIMARY KEY (id),
	KEY partner_status(partner_id, status)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
