<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

$limit = 200;
if($argc > 1)
	$limit = $argv[1];
$offset = 0;
	
$criteria = new Criteria();
$criteria->add(accessControlPeer::RULES, null, Criteria::ISNULL);
$criteria->setLimit($limit);

$accessControls = accessControlPeer::doSelect($criteria);
while(count($accessControls))
{
	echo "Migrating [" . count($accessControls) . "] access control profiles." . PHP_EOL;
	foreach($accessControls as $accessControl)
	{
		/* @var $accessControl accessControl */
		echo "Migrating access control profile [" . $accessControl->getId() . "]." . PHP_EOL;
		$accessControl->setRulesArray($accessControl->getRulesArray());
		$accessControl->save();
	}
	kMemoryManager::clearMemory();

	$offset += $limit;
	$criteria->setOffset($offset);
	$accessControls = accessControlPeer::doSelect($criteria);
}

echo "Done." . PHP_EOL;


