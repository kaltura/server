ALTER TABLE kaltura.batch_job ADD COLUMN dc VARCHAR(2);

ALTER TABLE kaltura.notification ADD COLUMN dc VARCHAR(2);

ALTER TABLE kaltura.mail_job ADD COLUMN dc VARCHAR(2);

# add index for partner & type - appears many time in the slow-query log
ALTER TABLE batch_job ADD INDEX partner_type_index (partner_id,job_type);