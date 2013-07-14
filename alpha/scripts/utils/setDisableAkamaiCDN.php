<?php

chdir(dirname(__FILE__));
require_once(dirname(__FILE__) . '/../bootstrap.php');

if ( $argc == 2)
{	
	$partner_id = $argv[1];
}
else
{
	die ( 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . " [partner id]" . PHP_EOL );
}

$partner = PartnerPeer::retrieveByPK($partner_id);
if(!$partner)
{
        die('no such partner.'.PHP_EOL);
}

$partner->setDisableAkamaiHDNetwork(true);
$partner->save();

echo "Done.";
