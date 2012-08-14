ALTER TABLE distribution_profile MODIFY optional_flavor_params_ids text DEFAULT NULL, MODIFY required_flavor_params_ids text DEFAULT NULL;
ALTER TABLE entry_distribution MODIFY thumb_asset_ids text DEFAULT NULL, MODIFY flavor_asset_ids text DEFAULT NULL, ADD asset_ids text NULL DEFAULT NULL AFTER flavor_asset_ids;
