ALTER TABLE kvote
ADD `partner_id` INTEGER,
ADD `kvote_type` INTEGER default 1,
ADD `custom_data` TEXT
;