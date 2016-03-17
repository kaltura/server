
CREATE TABLE schedule_event
(
	id INTEGER  NOT NULL AUTO_INCREMENT,
	parent_id INTEGER  NOT NULL,
	partner_id INTEGER  NOT NULL,
	summary VARCHAR(256)  NOT NULL,
	description TEXT,
	type INTEGER  NOT NULL,
	status INTEGER  NOT NULL,
	original_start_date DATETIME  NOT NULL,
	start_date DATETIME  NOT NULL,
	end_date DATETIME  NOT NULL,
	reference_id VARCHAR(256)  NOT NULL,
	classification_type INTEGER  NOT NULL,
	geo_lat FLOAT  NOT NULL,
	geo_long FLOAT  NOT NULL,
	location VARCHAR(256)  NOT NULL,
	organizer_kuser_id INTEGER  NOT NULL,
	priority INTEGER  NOT NULL,
	sequence INTEGER  NOT NULL,
	recurance_type INTEGER  NOT NULL,
	duration INTEGER  NOT NULL,
	contact VARCHAR(1024)  NOT NULL,
	comment TEXT  NOT NULL,
	tags TEXT,
	created_at DATETIME,
	updated_at DATETIME,
	custom_data TEXT,
	PRIMARY KEY (id),
	KEY partner_status_recurance_index(partner_id, status, recurance_type)
)ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE schedule_resource
(
	id INTEGER  NOT NULL AUTO_INCREMENT,
	parent_id INTEGER  NOT NULL,
	partner_id INTEGER  NOT NULL,
	name VARCHAR(256)  NOT NULL,
	description TEXT,
	tags TEXT,
	type INTEGER  NOT NULL,
	status INTEGER  NOT NULL,
	created_at DATETIME,
	updated_at DATETIME,
	custom_data TEXT,
	PRIMARY KEY (id),
	KEY partner_status_type_index(partner_id, status, type)
)ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE schedule_event_resource
(
	id INTEGER  NOT NULL AUTO_INCREMENT,
	event_id INTEGER  NOT NULL,
	resource_id INTEGER  NOT NULL,
	partner_id INTEGER  NOT NULL,
	created_at DATETIME,
	updated_at DATETIME,
	custom_data TEXT,
	PRIMARY KEY (id),
	KEY partner_event_index(partner_id, event_id),
	KEY partner_resource_index(partner_id, resource_id)
)ENGINE=INNODB DEFAULT CHARSET=utf8;
