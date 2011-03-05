ALTER TABLE  `syndication_feed`
ADD `custom_data` TEXT AFTER `created_at`,
ADD `display_in_search` TINYINT after `custom_data`;
