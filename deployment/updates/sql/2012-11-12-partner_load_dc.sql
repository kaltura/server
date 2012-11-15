ALTER TABLE partner_load ADD `dc` int(11);
alter table partner_load drop PRIMARY KEY;
alter table partner_load ADD PRIMARY KEY(`partner_id`,`job_type`,`job_sub_type`,`dc`);
alter table partner_load ADD `quota` int(11);
alter table partner_load drop INDEX `quota_idx`;