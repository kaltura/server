# added unique index to partner_activity table (partner_id, activity_date, activity, sub_activity) 
alter table partner_activity add unique index (partner_id,activity_date,activity,sub_activity) 
