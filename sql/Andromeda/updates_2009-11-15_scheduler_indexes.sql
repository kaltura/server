ALTER TABLE scheduler_status ADD INDEX status_created_at_index (created_at);
ALTER TABLE scheduler_config ADD INDEX status_variable_index (variable, variable_part);
ALTER TABLE scheduler_config ADD INDEX status_created_at_index (created_at);
ALTER TABLE scheduler_config ADD INDEX scheduler_id_index (scheduler_id);
ALTER TABLE scheduler_config ADD INDEX worker_id_index_type (worker_id);

ALTER TABLE scheduler ADD statuses VARCHAR( 255 ) NOT NULL ;
ALTER TABLE scheduler_worker ADD statuses VARCHAR( 255 ) NOT NULL ;