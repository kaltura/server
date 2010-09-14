# add fields to the partner table
alter table partner add	(`phone` VARCHAR(64),`describe_yourself` VARCHAR(64),`adult_content` TINYINT default 0);