# change min_send_date from date to datetime
alter table mail_job modify min_send_date DATETIME;

# --------------------------------------------------------------
# add entry media_date
alter table entry add column media_date DATETIME;
