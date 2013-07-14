<?php

chdir(dirname(__FILE__));
require_once(dirname(__FILE__) . '/../bootstrap.php');

if (count($argv) !== 2)
{
	die('please provide a partner id' . PHP_EOL . 
		'to run script: ' . basename(__FILE__) . ' X' . PHP_EOL . 
		'whereas X is partner id' . PHP_EOL);
}
$partner_id = @$argv[1];

$partner = PartnerPeer::retrieveByPK($partner_id);
if(!$partner)
{
	die('no such partner.'.PHP_EOL);
}
$partner->setAppearInSearch(mySearchUtils::DISPLAY_IN_SEARCH_PARTNER_ONLY);
$partner->save();

$c = new Criteria();
$c->add(entryPeer::PARTNER_ID, $partner_id);
$c->add(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK);
$c->addOr(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_NONE);
$c->setLimit(20);

$entries = entryPeer::doSelect($c);
$changedEntriesCounter = 0;
while(count($entries))
{
	$changedEntriesCounter += count($entries);
	foreach($entries as $entry)
	{
		$entry->setDisplayInSearch(mySearchUtils::DISPLAY_IN_SEARCH_PARTNER_ONLY);
		$entry->save();	
	}
	$entries = entryPeer::doSelect($c);
}

echo "Done. {$changedEntriesCounter} entries where changed";
