<?php
chdir(dirname(__FILE__));

require_once(__DIR__ . '/../../bootstrap.php');


$c = new Criteria();

if($argc > 1 && is_numeric($argv[1]))
	$c->add(kuserPeer::UPDATED_AT, $argv[1], Criteria::GREATER_EQUAL);
if($argc > 2 && is_numeric($argv[2]))
	$c->add(kuserPeer::PARTNER_ID, $argv[2], Criteria::EQUAL);
if($argc > 3)
{
	if(is_numeric($argv[3]))
	{
		$c->add(kuserPeer::ID, $argv[3], Criteria::GREATER_EQUAL);
	}
	elseif(strpos($argv[3], '-') !== false) 
	{
		list($minId, $maxId) = explode('-', $argv[3], 2);
		if(is_numeric($minId) && is_numeric($maxId)) {
			$c->add(kuserPeer::ID, $minId, Criteria::GREATER_EQUAL);
			$c->addAnd(kuserPeer::ID, $maxId, Criteria::LESS_THAN);
		}
	}
}
if($argc > 4)
	kuserPeer::setUseCriteriaFilter((bool)$argv[4]);

$c->addAscendingOrderByColumn(kuserPeer::UPDATED_AT);
$c->addAscendingOrderByColumn(kuserPeer::ID);
$c->setLimit(10000);

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
//$sphinxCon = DbManager::getSphinxConnection();

$entries = kuserPeer::doSelect($c, $con);
$sphinx = new kSphinxSearchManager();
while(count($entries))
{
	foreach($entries as $entry)
	{
	    /* @var $entry kuser */
		KalturaLog::log('kuser id ' . $entry->getId() . ' updated at '. $entry->getUpdatedAt(null));
		
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
	$entries = kuserPeer::doSelect($c, $con);
}

KalturaLog::log('Done. Current time: ' . time());
exit(0);
