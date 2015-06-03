CREATE TABLE edge_server
(
        id INTEGER  NOT NULL AUTO_INCREMENT,
        created_at DATETIME,
        updated_at DATETIME,
        partner_id INTEGER,
        name VARCHAR(31),
        system_name VARCHAR(128),
        desciption VARCHAR(127),
        status INTEGER,
        tags TEXT,
        host_name VARCHAR(127),
        playback_host_name VARCHAR(127),
        parent_id INTEGER default 0,
        custom_data TEXT,
        PRIMARY KEY (id),
        KEY partner_id_status(partner_id, status, system_name)
) Engine=InnoDB DEFAULT CHARSET=utf8;