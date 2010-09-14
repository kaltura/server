<?php
chdir(dirname(__FILE__));
require_once("..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."bootstrap.php");
$limit = 100;
$sleep = 1;

$c = new Criteria();
$c->setOffset(0);
$c->setLimit($limit);
$entries = null;

while(true)
{
	$entries = entryPeer::doSelect($c);
	if (count($entries) <= 0)
		break;
		
	foreach($entries as $entry)
	{
		KalturaLog::info($entry->getId());
		KalturaLog::info('Old: ' . $entry->getSearchTextDiscrete());
		mySearchUtils::setSearchTextDiscreteForEntry($entry);
		KalturaLog::info('New: ' . $entry->getSearchTextDiscrete());
		$entry->justSave();
		KalturaLog::info('Saved!');
	}
	$c->setOffset($c->getOffset() + $limit);
	sleep($sleep);
}
