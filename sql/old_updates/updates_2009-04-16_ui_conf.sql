# add to ui_conf - column display_in_search
alter table ui_conf add `creation_mode` TINYINT DEFAULT 1;
alter table ui_conf add KEY `partner_id_index`(`partner_id`);
alter table ui_conf add KEY `partner_id_creation_mode_index`(`partner_id`,`creation_mode`);