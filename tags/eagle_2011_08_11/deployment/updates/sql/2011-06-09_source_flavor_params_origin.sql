UPDATE flavor_params_conversion_profile 
SET origin = 1,
system_name = 'Source'
WHERE flavor_params_id = 0 
AND origin = 0;
