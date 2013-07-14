<?php
chdir(__DIR__.'/../');
require_once(__DIR__ . '/../bootstrap.php');

if ( $argc == 3)
{	
	$partner_id = $argv[1];
	$language = $argv[2];
}
else
{
	die ( 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . " [partner id] [language]" . PHP_EOL );
}

$partner = PartnerPeer::retrieveByPK($partner_id);
if(!$partner)
{
        die('no such partner.'.PHP_EOL);
}

$partner->setKMCLanguage($language);
$partner->save();

echo "Done.";