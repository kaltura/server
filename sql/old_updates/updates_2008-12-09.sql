# creating unique_visitors_cookie table
DROP TABLE IF EXISTS unique_visitors_cookie;
CREATE TABLE unique_visitors_cookie (uv_id varchar(32), date DATE) Type=MyISAM;
alter table unique_visitors_cookie add unique index date_uv_id (date,uv_id);

# creating unique_visitors_ip table
DROP TABLE IF EXISTS unique_visitors_ip;
CREATE TABLE unique_visitors_ip (ip integer, date DATE) Type=MyISAM;
alter table unique_visitors_ip add unique index date_ip (date,ip);


