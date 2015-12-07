CREATE TABLE edge_server
(
        id INTEGER  NOT NULL AUTO_INCREMENT,
        created_at DATETIME,
        updated_at DATETIME,
        partner_id INTEGER,
        name VARCHAR(256),
        system_name VARCHAR(256),
        desciption VARCHAR(256),
        status INTEGER,
        type INTEGER default 0 NOT NULL,
        tags TEXT,
        host_name VARCHAR(256) NOT NULL,
        playback_host_name VARCHAR(256),
        parent_id INTEGER default 0,
        custom_data TEXT,
        PRIMARY KEY (id),
        KEY partner_id_status_system_name(partner_id, status, system_name),
        KEY host_name(host_name)
) Engine=InnoDB DEFAULT CHARSET=utf8;