# added column modified_at to the entry table
alter table entry add  `modified_at` DATETIME ;
alter table entry add KEY `partner_modified_at_index`(`partner_id`, `modified_at`);