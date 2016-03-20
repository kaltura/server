CREATE TABLE entry_server_node
 (
 	id INTEGER  NOT NULL AUTO_INCREMENT,
 	entry_id VARCHAR(20),
 	server_node_id INTEGER,
 	partner_id INTEGER,
 	created_at DATETIME,
 	updated_at DATETIME,
 	status INTEGER,
 	server_type INTEGER,
 	custom_data TEXT,
 	PRIMARY KEY (id),
 	KEY entry_server_type(entry_id, server_type)
 )ENGINE=InnoDB COMMENT='Relationship between entry and server node' DEFAULT CHARSET=utf8;