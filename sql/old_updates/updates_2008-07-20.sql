# --------------------------------------------------------------
# fix kshow episode_id - should be the same as the id
alter table kshow modify column episode_id varchar(10) 