DROP TABLE IF EXISTS delivery;
CREATE TABLE delivery
(
        id BIGINT  NOT NULL AUTO_INCREMENT,
        partner_id INTEGER,
        created_at DATETIME,
        updated_at DATETIME,
        name VARCHAR(255),
		type smallint(6) DEFAULT NULL,
		system_name VARCHAR(255),
        description TEXT,
		url VARCHAR(256),
		host_name VARCHAR(256),
		recognizer TEXT,
		tokenizer TEXT,
		delivery_status INTEGER,
		media_protocols VARCHAR(255),
		streamer_type VARCHAR(30),
		is_default tinyint(4),
		is_secure tinyint(4),
		parent_id bigint(20),
		custom_data text,
        PRIMARY KEY (id),
        KEY partner_index(partner_id)
)Engine=InnoDB DEFAULT CHARSET=utf8;