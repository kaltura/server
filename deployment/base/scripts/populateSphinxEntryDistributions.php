<?php
chdir(dirname(__FILE__));

require_once(__DIR__ . '/../../bootstrap.php');


$c = new Criteria();

if($argc > 1 && is_numeric($argv[1]))
	$c->add(EntryDistributionPeer::UPDATED_AT, $argv[1], Criteria::GREATER_EQUAL);
if($argc > 2 && is_numeric($argv[2]))
	$c->add(EntryDistributionPeer::PARTNER_ID, $argv[2], Criteria::EQUAL);
if($argc > 3 && is_numeric($argv[3]))
	$c->add(EntryDistributionPeer::ID, $argv[3], Criteria::GREATER_EQUAL);
if($argc > 4)
	EntryDistributionPeer::setUseCriteriaFilter((bool)$argv[4]);
	
$c->addAscendingOrderByColumn(EntryDistributionPeer::UPDATED_AT);
$c->addAscendingOrderByColumn(EntryDistributionPeer::ID);
$c->setLimit(10000);

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
//$sphinxCon = DbManager::getSphinxConnection();

$entries = EntryDistributionPeer::doSelect($c, $con);
$sphinx = new kSphinxSearchManager();
while(count($entries))
{
	foreach($entries as $entry)
	{
		KalturaLog::log('entry distribution id ' . $entry->getId() .' updated at ' . $entry->getUpdatedAt(null));
		
		try {
			$ret = $sphinx->saveToSphinx($entry, true);
		}
		catch(Exception $e){
			KalturaLog::err($e->getMessage());
			exit -1;
		}
	}
	
	$c->setOffset($c->getOffset() + count($entries));
	kMemoryManager::clearMemory();
	$entries = EntryDistributionPeer::doSelect($c, $con);
}

KalturaLog::log('Done. Cureent time: ' . time());
exit(0);
