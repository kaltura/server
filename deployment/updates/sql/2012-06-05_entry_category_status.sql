ALTER TABLE category_entry
ADD `status` INTEGER DEFAULT 2;

ALTER TABLE category
ADD `moderation` TINYINT default 0,
ADD `pending_entries_count` INTEGER;