alter table entry add column `indexed_custom_data_1` integer;
alter table entry add KEY `partner_kuser_indexed_custom_data_index`(`partner_id`, `kuser_id`,`indexed_custom_data_1`);
