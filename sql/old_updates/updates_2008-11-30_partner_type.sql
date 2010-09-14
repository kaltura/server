# add columns to the partner table
alter table partner add column (`status` TINYINT default 1, `content_categories` VARCHAR(1024) , `type` TINYINT default 1 );
