# add columns to entry for admin_tags and moderation
alter table entry add column (`admin_tags` TEXT,`moderation_status` INTEGER,`moderation_count` INTEGER);
alter table entry add KEY `partner_status_index` (`partner_id`, `status`) ;
alter table entry add KEY `partner_moderation_status` (`partner_id`, `moderation_status`);

# add tags to ui_conf	
alter table ui_conf add column  `tags` TEXT;
