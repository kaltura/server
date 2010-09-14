alter table notification add column `object_type` SMALLINT;
update notification set type=1 where type<10;
update notification set type=2 where type>10;

alter table ui_conf add column `conf_vars` VARCHAR(4096), add column `use_cdn` TINYINT ;
alter table ui_conf modify column `use_cdn` TINYINT default 0;


