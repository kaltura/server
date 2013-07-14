<?php

/*partner's limelight live params*/
$limelightPrimaryPublishUrl = 'rtmp://ingest01.pri.svenska.fmspush.llnw.net/svenska';
$limelightSecondaryPublishUrl = 'rtmp://ingest01.bak.svenska.fmspush.llnw.net/svenska';
$limelightStreamUrl = 'rtmp://svenska.fc.llnwd.net/svenska';

$limeLightParams = array(
						"limelightPrimaryPublishUrl" => $limelightPrimaryPublishUrl, 
						"limelightSecondaryPublishUrl" => $limelightSecondaryPublishUrl,
						"limelightStreamUrl" => $limelightStreamUrl,
						);
						
$limeLightParamsContainer = array("Limelight" => $limeLightParams); 

$json = json_encode($limeLightParamsContainer);

echo "$json \n";

$llParams = json_decode($json);

print_r($llParams);
