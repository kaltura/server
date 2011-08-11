ALTER TABLE  `upload_token` 
ADD  `object_type` VARCHAR( 127 ) NULL AFTER  `dc` ,
ADD  `object_id` VARCHAR( 31 ) NULL AFTER  `object_type`;