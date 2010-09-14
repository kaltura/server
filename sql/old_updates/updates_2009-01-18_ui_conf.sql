# add to ui_conf - column custom_data
alter table ui_conf add `status` INTEGER DEFAULT 2;
update ui_conf set status=2;
#alter table ui_conf alter `status` SET DEFAULT 2;

