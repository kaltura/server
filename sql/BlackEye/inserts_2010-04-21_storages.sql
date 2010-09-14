
INSERT INTO `storage_profile` (`id`, `created_at`, `updated_at`, `partner_id`, `name`, `desciption`, `status`, `protocol`, `storage_url`, `storage_base_dir`, `storage_username`, `storage_password`, `storage_ftp_passive_mode`, `delivery_http_base_url`, `delivery_rmp_base_url`, `delivery_iis_base_url`, `min_file_size`, `max_file_size`, `flavor_params_ids`, `max_concurrent_connections`, `custom_data`, `path_manager_class`) 
VALUES(1,NOW(),NOW(),'0','pa','Palo Alto','3','0',NULL,'/web',NULL,NULL,NULL,'http://cdn.kaltura.com','rtmp://rtmpakmi.kaltura.com:1935/ondemand','http://smooth.kaltura.com',NULL,NULL,NULL,NULL,NULL,NULL);

UPDATE storage_profile SET id = 0 WHERE id = 1;

INSERT INTO `storage_profile` (`id`, `created_at`, `updated_at`, `partner_id`, `name`, `desciption`, `status`, `protocol`, `storage_url`, `storage_base_dir`, `storage_username`, `storage_password`, `storage_ftp_passive_mode`, `delivery_http_base_url`, `delivery_rmp_base_url`, `delivery_iis_base_url`, `min_file_size`, `max_file_size`, `flavor_params_ids`, `max_concurrent_connections`, `custom_data`, `path_manager_class`) 
VALUES(2,NOW(),NOW(),'0','ny','New-York','3','0',NULL,'/web',NULL,NULL,NULL,'http://cdn.kaltura.com','rtmp://rtmpakmi.kaltura.com:1935/ondemand','http://smooth.kaltura.com',NULL,NULL,NULL,NULL,NULL,NULL);

UPDATE storage_profile SET id = 1 WHERE id = 2;

