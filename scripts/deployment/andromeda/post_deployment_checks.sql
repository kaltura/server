/*
Search for partners without access_control
*/
SELECT 
	p.id
FROM
	partner p LEFT JOIN access_control ac ON(p.id=ac.partner_id AND ac.partner_id IS NULL);
	
	
/*
Search for partners without conversion_profile_2 
*/
SELECT 
	p.id
FROM
	partner p LEFT JOIN conversion_profile_2 cp2 ON(p.id=cp2.partner_id AND cp2.partner_id IS NULL) ;	
	
	
/*
Search for conversion_profile_2 without flavor_params
*/	
SELECT 
	cp2.id,
	fpcp.conversion_profile_id,
	IF(fpcp.conversion_profile_id IS NULL,0,COUNT(1)) _cnt_flavor_params_for_profile
FROM 
	conversion_profile_2 cp2 LEFT JOIN flavor_params_conversion_profile fpcp ON (cp2.id=fpcp.conversion_profile_id)
GROUP BY cp2.id;