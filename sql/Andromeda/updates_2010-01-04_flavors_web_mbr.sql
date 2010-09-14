UPDATE flavor_asset SET tags = CONCAT(tags, ",mbr") WHERE tags LIKE '%web%';
UPDATE flavor_params_output SET tags = CONCAT(tags, ",mbr") WHERE tags LIKE '%web%';
