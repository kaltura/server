<?php
require_once(__DIR__ . '/../bootstrap.php');

if ( $argc == 3)
{	
	$flavor_param_id = $argv[1];
	$conversion_profile_id = $argv[2];
}
else
{
	die ( 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . " [flavor_param_id] [conversion profile id]" . PHP_EOL );
}

$conversion_flavor = flavorParamsConversionProfilePeer::retrieveByFlavorParamsAndConversionProfile($flavor_param_id, $conversion_profile_id);
if(!$conversion_flavor)
{
        die('no such flavor param id and conversion profile id.'.PHP_EOL);
}

$conversion = conversionProfile2Peer::retrieveByPK($conversion_profile_id);
$input_tags_maps = $conversion->getInputTagsMap();

if (strpos($input_tags_maps, ",mbr") === false)
{
	$input_tags_maps .= ",mbr";
	
	$conversion->setInputTagsMap($input_tags_maps);
	$conversion->save();
}

$conversion_flavor->setReadyBehavior(flavorParamsConversionProfile::READY_BEHAVIOR_OPTIONAL);
$conversion_flavor->save();

echo "Done.";