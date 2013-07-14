<?php

chdir(dirname(__FILE__));
require_once(dirname(__FILE__) . '/../bootstrap.php');

if ( $argc == 3)
{	
	$partner_id = $argv[1];
	$date_format = $argv[2];
	
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
	$storage->setDateFormat($date_format);
	$storage->save();
}
echo "Done.";
