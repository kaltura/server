# add fields to the partner table
ALTER TABLE `partner` ADD COLUMN (`usage_percent` INTEGER default 0, `storage_usage` INTEGER default 0, `eighty_percent_warning` INTEGER, `usage_limit_warning` INTEGER);
