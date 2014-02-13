ALTER TABLE `category_entry` ADD `privacy_context` VARCHAR(255) AFTER `status`;
ALTER TABLE `category_entry` ADD KEY `partner_id_privacy_context_index`(`partner_id`, `privacy_context`);