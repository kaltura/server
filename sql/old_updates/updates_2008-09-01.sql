# --------------------------------------------------------------
# add min_send_date to mail_job table
alter table mail_job add min_send_date date;

# --------------------------------------------------------------
# add entry description
alter table entry add column description TEXT;
