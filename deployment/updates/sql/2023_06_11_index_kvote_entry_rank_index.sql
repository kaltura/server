ALTER TABLE kvote DROP KEY entry_rank_index, ADD INDEX entry_rank_index(`entry_id`,`rank`);
