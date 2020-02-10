alter table reach_profile add last_sync_time VARCHAR(100) after used_credit;
alter table reach_profile add synced_credit INTEGER default 0 after used_credit;
alter table reach_profile add add_on INTEGER default 0 after used_credit;