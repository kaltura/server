update partner set partner_package=2 where partner_package>=2 ;
update partner set partner_package=1 where partner_package not in (1,2) ;
