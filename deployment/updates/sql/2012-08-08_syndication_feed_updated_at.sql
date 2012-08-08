ALTER TABLE syndication_feed
ADD `updated_at` DATETIME;

update syndication_feed set updated_at=created_at;