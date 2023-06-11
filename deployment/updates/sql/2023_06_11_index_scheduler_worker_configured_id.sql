ALTER TABLE scheduler_worker DROP KEY configured_id, ADD INDEX configured_id(`configured_id`,`scheduler_configured_id`);
