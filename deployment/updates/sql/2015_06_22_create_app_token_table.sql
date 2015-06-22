
CREATE TABLE app_token
(
	id VARCHAR(20)  NOT NULL,
	int_id INTEGER  NOT NULL AUTO_INCREMENT,
	partner_id INTEGER,
	created_at DATETIME,
	updated_at DATETIME,
	deleted_at DATETIME,
	status INTEGER,
	expiry INTEGER,
	session_type INTEGER,
	session_user_id VARCHAR(100),
	session_duration INTEGER,
	session_privileges TEXT,
	token TEXT,
	custom_data TEXT,
	PRIMARY KEY (id)
)Type=InnoDB DEFAULT CHARSET=utf8;
