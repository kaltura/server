ALTER TABLE category
drop `privacy_context`,
drop `privacy_contexts`;

ALTER TABLE category
ADD	`privacy_context` VARCHAR(255),
ADD `privacy_contexts` VARCHAR(255);
