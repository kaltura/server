# add index for modified_at 
alter table entry add index modified_at_index (`modified_at`)
# change source_id column to varchar
alter table entry modify source_id varchar(48)
