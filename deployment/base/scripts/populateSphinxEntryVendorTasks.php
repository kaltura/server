<?php
chdir(dirname(__FILE__));

require_once(__DIR__ . '/../../bootstrap.php');


$c = new Criteria();

if($argc > 1 && is_numeric($argv[1]))
	$c->add(EntryVendorTaskPeer::ID, $argv[1], Criteria::GREATER_EQUAL);

if($argc > 2 && is_numeric($argv[2]))
	$c->add(EntryVendorTaskPeer::PARTNER_ID, $argv[2], Criteria::EQUAL);
	
if($argc > 3)
	EntryVendorTaskPeer::setUseCriteriaFilter((bool)$argv[3]);
	
$c->addAscendingOrderByColumn(EntryVendorTaskPeer::ID);
$c->setLimit(10000);

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);

$entryVendorTasks = EntryVendorTaskPeer::doSelect($c, $con);
$sphinx = new kSphinxSearchManager();
while(count($entryVendorTasks))
{
	foreach($entryVendorTasks as $entryVendorTask)
	{
	    /* @var $entryVendorTask EntryVendorTask */
		KalturaLog::log('entryVendorTask id ' . '[' . $entryVendorTask->getId() . '] crc id [' . $sphinx->getSphinxId($entryVendorTask) . ']');
		
		try {
			$ret = $sphinx->saveToSphinx($entryVendorTask, true);
		}
		catch(Exception $e){
			KalturaLog::err($e->getMessage());
			exit -1;
		}
	}
	
	$c->setOffset($c->getOffset() + count($entryVendorTasks));
	kMemoryManager::clearMemory();
	$entryVendorTasks = EntryVendorTaskPeer::doSelect($c, $con);
}

KalturaLog::log('Done');
exit(0);
