alter table entry alter moderation_status set default 2, alter moderation_count set default 0;

update entry set moderation_status=2, moderation_count=0 where status=2;
update entry set moderation_status=3, moderation_count=0 where status=3;