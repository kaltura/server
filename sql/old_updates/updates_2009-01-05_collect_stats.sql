# creating collect_stats table
DROP TABLE IF EXISTS collect_stats;
CREATE TABLE collect_stats (ip integer, date datetime, partner_id integer, entry_id varchar(10), widget_id varchar(10), command varchar(10)) Type=MyISAM;
alter table collect_stats add index partner_command (partner_id,command);
alter table collect_stats add index entry_command (partner_id,command);
alter table collect_stats add index widget_command (widget_id,command);
