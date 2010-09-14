ALTER TABLE  `media_info` 
ADD  `flavor_asset_version` VARCHAR( 20 ) NOT NULL ,
ADD  `scan_type` INT NOT NULL ,
ADD  `multi_stream` VARCHAR( 255 ) NOT NULL;

ALTER TABLE  `flavor_params` 
ADD  `deinterlice` INT NOT NULL ,
ADD  `rotate` INT NOT NULL;

ALTER TABLE  `flavor_params_output` 
ADD  `deinterlice` INT NOT NULL ,
ADD  `rotate` INT NOT NULL;