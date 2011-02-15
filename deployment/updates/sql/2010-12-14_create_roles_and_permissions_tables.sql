#-----------------------------------------------------------------------------
#-- permission
#-----------------------------------------------------------------------------

CREATE TABLE `permission`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`type` INTEGER  NOT NULL,
	`name` VARCHAR(100)  NOT NULL,
	`friendly_name` VARCHAR(100),
	`description` TEXT,
	`partner_id` INTEGER  NOT NULL,
	`status` INTEGER  NOT NULL,
	`depends_on_permission_names` TEXT,
	`tags` TEXT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id_index`(`partner_id`),
	KEY `name_index`(`name`),
	KEY `name_partner_id_index`(`name`, `partner_id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- user_role
#-----------------------------------------------------------------------------

CREATE TABLE `user_role`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`str_id` VARCHAR(100)  NOT NULL,
	`name` VARCHAR(100)  NOT NULL,
	`description` TEXT,
	`partner_id` INTEGER  NOT NULL,
	`status` INTEGER  NOT NULL,
	`permission_names` TEXT,
	`tags` TEXT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`custom_data` TEXT,
	PRIMARY KEY (`id`),
	KEY `partner_id_index`(`partner_id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- permission_item
#-----------------------------------------------------------------------------

CREATE TABLE `permission_item`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`type` VARCHAR(100)  NOT NULL,
	`param_1` VARCHAR(100)  NOT NULL,
	`param_2` VARCHAR(100)  NOT NULL,
	`param_3` VARCHAR(100)  NOT NULL,
	`param_4` VARCHAR(100)  NOT NULL,
	`param_5` VARCHAR(100)  NOT NULL,
	`tags` TEXT,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	`custom_data` TEXT,
	PRIMARY KEY (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- permission_to_permission_item
#-----------------------------------------------------------------------------

CREATE TABLE `permission_to_permission_item`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`permission_id` INTEGER  NOT NULL,
	`permission_item_id` INTEGER  NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	INDEX `permission_to_permission_item_FI_1` (`permission_id`),
	CONSTRAINT `permission_to_permission_item_FK_1`
		FOREIGN KEY (`permission_id`)
		REFERENCES `permission` (`id`),
	INDEX `permission_to_permission_item_FI_2` (`permission_item_id`),
	CONSTRAINT `permission_to_permission_item_FK_2`
		FOREIGN KEY (`permission_item_id`)
		REFERENCES `permission_item` (`id`)
)Type=MyISAM;

#-----------------------------------------------------------------------------
#-- kuser_to_user_role
#-----------------------------------------------------------------------------

CREATE TABLE `kuser_to_user_role`
(
	`id` INTEGER  NOT NULL AUTO_INCREMENT,
	`kuser_id` INTEGER  NOT NULL,
	`user_role_id` INTEGER  NOT NULL,
	`created_at` DATETIME,
	`updated_at` DATETIME,
	PRIMARY KEY (`id`),
	INDEX `kuser_to_user_role_FI_1` (`kuser_id`),
	CONSTRAINT `kuser_to_user_role_FK_1`
		FOREIGN KEY (`kuser_id`)
		REFERENCES `kuser` (`id`),
	INDEX `kuser_to_user_role_FI_2` (`user_role_id`),
	CONSTRAINT `kuser_to_user_role_FK_2`
		FOREIGN KEY (`user_role_id`)
		REFERENCES `user_role` (`id`)
)Type=MyISAM;