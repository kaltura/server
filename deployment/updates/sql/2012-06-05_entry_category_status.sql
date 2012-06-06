ALTER TABLE category_entry
ADD `status` INTEGER;

ALTER TABLE category
ADD `moderation` TINYINT default 0,
ADD `pending_entries_count` INTEGER;