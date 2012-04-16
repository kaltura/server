ALTER TABLE `category_entry`
ADD `updated_at` DATETIME,
ADD KEY `category_entry_updated_at`(`updated_at`);