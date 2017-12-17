<?php
ini_set("memory_limit","256M");

require_once(__DIR__ . '/bootstrap.php');

if($argc<2)
        die("Usage:php $argv[0] <partner id> [<category id>]");
	
$partnerId = $argv[1];
if(isset($argv[2]))
        $categoryId=$argv[2];

var_dump($partnerId);

if ( !PartnerPeer::retrieveByPK($partnerId) )
	die("Please enter a valid partner Id!");

$criteria = new Criteria();
$criteria->add(categoryPeer::PARTNER_ID,$partnerId,Criteria::EQUAL);
if(isset($categoryId))
        $criteria->add(categoryPeer::ID,$categoryId);
$criteria->setLimit(1000);
$allCats = categoryPeer::doSelect($criteria);

while(count($allCats))
{
	foreach ($allCats as $categoryDb)
	{
		/* @var $categoryDb category */
		
		$categoryDb->reSetEntriesCount();
		$categoryDb->reSetDirectSubCategoriesCount();
		$categoryDb->reSetDirectEntriesCount();	
		$categoryDb->reSetPendingEntriesCount();
		$categoryDb->reSetPendingMembersCount();	
		$categoryDb->save();
	}
	
	$criteria->setOffset($criteria->getOffset() + count($allCats));
	kMemoryManager::clearMemory();
	usleep(200);
	$allCats = categoryPeer::doSelect($criteria);
}

KalturaLog::log('Done.');
