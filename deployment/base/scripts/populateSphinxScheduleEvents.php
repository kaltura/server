<?php
chdir(dirname(__FILE__));

require_once(__DIR__ . '/../../bootstrap.php');


$c = new Criteria();

if($argc > 1 && is_numeric($argv[1]))
	$c->add(ScheduleEventPeer::UPDATED_AT, $argv[1], Criteria::GREATER_EQUAL);
if($argc > 2 && is_numeric($argv[2]))
	$c->add(ScheduleEventPeer::PARTNER_ID, $argv[2], Criteria::EQUAL);
if($argc > 3 && is_numeric($argv[3]))
	$c->add(ScheduleEventPeer::ID, $argv[3], Criteria::GREATER_EQUAL);
if($argc > 4)
	ScheduleEventPeer::setUseCriteriaFilter((bool)$argv[4]);

$c->addAscendingOrderByColumn(ScheduleEventPeer::UPDATED_AT);
$c->addAscendingOrderByColumn(ScheduleEventPeer::ID);
$c->setLimit(10000);

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
//$sphinxCon = DbManager::getSphinxConnection();

$entries = ScheduleEventPeer::doSelect($c, $con);
$sphinx = new kSphinxSearchManager();
while(count($entries))
{
	foreach($entries as $entry)
	{
	    /* @var $entry ScheduleEvent */
		KalturaLog::log('Schedule-event id ' . $entry->getId() . ' updated at '. $entry->getUpdatedAt(null));
		
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
	$entries = ScheduleEventPeer::doSelect($c, $con);
}

KalturaLog::log('Done. Cureent time: ' . time());
exit(0);
