<?php
require_once(__DIR__ . '/../bootstrap.php');

if ( $argc == 3)
{	
	$partner_id = $argv[1];
	$path_format = $argv[2];
	
}
else
{
	die ( 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . " [partner id] [date format]" . PHP_EOL );
}

$storages = StorageProfilePeer::retrieveExternalByPartnerId($partner_id);

if(!$storages)
{
        die('no such partner.'.PHP_EOL);
}

foreach ($storages as $storage)
{
	$storage->setPathFormat($path_format);
	$storage->save();
}
echo "Done.";
