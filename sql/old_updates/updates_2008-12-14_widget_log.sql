# changed widget_type field to varchar(10) as it stores widget_id
alter table widget_log modify widget_type varchar(10);
