alter table entry modify id VARCHAR(10)  NOT NULL ,add int_id INTEGER  NOT NULL ,modify kshow_id varchar(10);
update entry set int_id=id;
alter table entry add KEY `int_id_index`(`int_id`), modify int_id INTEGER AUTO_INCREMENT;


alter table kshow modify id VARCHAR(10)  NOT NULL ,add int_id INTEGER  NOT NULL ,modify show_entry_id varchar(10), modify intro_id varchar(10);
update kshow set int_id=id;
alter table kshow add KEY `int_id_index`(`int_id`), modify int_id INTEGER AUTO_INCREMENT;


#alter table entry modify kshow_id varchar(10);

#alter table kshow modify show_entry_id varchar(10), modify intro_id varchar(10);

alter table kvote modify kshow_id varchar(10),modify entry_id varchar(10);

alter table kshow_kuser modify kshow_id varchar(10);

alter table puser_role modify kshow_id varchar(10);

alter table batch_job modify entry_id varchar(10) default '';

alter table conversion modify entry_id varchar(10);

alter table widget_log modify kshow_id varchar(10),modify entry_id varchar(10);

alter table roughcut_entry modify roughcut_kshow_id varchar(10),modify entry_id varchar(10);

alter table widget modify kshow_id varchar(10), modify entry_id varchar(10);

alter table kwidget_log modify kshow_id varchar(10),modify entry_id varchar(10);
