ALTER TABLE  `media_info` CHANGE  `flavor_asset_id`  `flavor_asset_id` VARCHAR( 20 ) NULL DEFAULT NULL;
ALTER TABLE  `flavor_params_output` ADD  `flavor_asset_version` VARCHAR( 20 ) NOT NULL AFTER  `flavor_asset_id`;

UPDATE	media_info mi,  flavor_asset fa
SET		mi.flavor_asset_id = fa.id
WHERE	mi.flavor_asset_id = fa.int_id;

UPDATE	flavor_params_output fpo,  flavor_asset fa
SET		fpo.flavor_asset_id = fa.id
WHERE	fpo.flavor_asset_id = fa.int_id;

UPDATE	flavor_params_output fpo,  flavor_asset fa
SET		fpo.flavor_asset_version = fa.version
WHERE	fpo.flavor_asset_id = fa.id;