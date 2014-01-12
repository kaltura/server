CREATE TABLE live_channel_segment
(
        id BIGINT  NOT NULL AUTO_INCREMENT,
        partner_id INTEGER,
        created_at DATETIME,
        updated_at DATETIME,
        name VARCHAR(255),
        description TEXT,
        tags TEXT,
        type INTEGER,
        status INTEGER,
        channel_id VARCHAR(20),
        entry_id VARCHAR(20),
        trigger_type INTEGER,
        trigger_segment_id BIGINT,
        start_time FLOAT,
        duration FLOAT,
        custom_data TEXT,
        PRIMARY KEY (id),
        KEY partner_index(partner_id),
        KEY live_channel_segment_FI_1 (trigger_segment_id),
        Key live_channel_segment_FI_3 (channel_id,status),
        Key live_channel_segment_FI_4 (entry_id,status)
)Engine=InnoDB DEFAULT CHARSET=utf8;
