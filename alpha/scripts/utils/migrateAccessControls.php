<?php
require_once(dirname(__FILE__).'/../bootstrap.php');

$limit = 200;
if($argc > 1)
	$limit = $argv[1];
	
$criteria = new Criteria();
if($argc < 3 || intval($argv[2]))
	$criteria->add(accessControlPeer::RULES, null, Criteria::ISNULL);
	
$criteria->addAscendingOrderByColumn(accessControlPeer::ID);
$criteria->setLimit($limit);

$accessControls = accessControlPeer::doSelect($criteria);
while(count($accessControls))
{
	echo "Migrating [" . count($accessControls) . "] access control profiles." . PHP_EOL;
	$lastId = null;
	foreach($accessControls as $accessControl)
	{
		/* @var $accessControl accessControl */
		$accessControl->setRulesArray($accessControl->getRulesArray(true));
		$accessControl->save();
		
		$lastId = $accessControl->getId();
		echo "Migrated access control profile [$lastId]." . PHP_EOL;
	}
	kMemoryManager::clearMemory();

	$nextCriteria = clone $criteria;
	$nextCriteria->add(accessControlPeer::ID, $lastId, Criteria::GREATER_THAN);
	
	$accessControls = accessControlPeer::doSelect($nextCriteria);
}

echo "Done." . PHP_EOL;


