<?php
chdir(dirname(__FILE__));

require_once(__DIR__ . '/../../bootstrap.php');


$c = new Criteria();

if($argc > 1 && is_numeric($argv[1]))
	$c->add(categoryKuserPeer::UPDATED_AT, $argv[1], Criteria::GREATER_EQUAL);
if($argc > 2 && is_numeric($argv[2]))
	$c->add(categoryKuserPeer::PARTNER_ID, $argv[2], Criteria::EQUAL);
if($argc > 3 && is_numeric($argv[3]))
	$c->add(categoryKuserPeer::ID, $argv[3], Criteria::GREATER_EQUAL);
if($argc > 4)
	categoryKuserPeer::setUseCriteriaFilter((bool)$argv[4]);

$c->addAscendingOrderByColumn(categoryKuserPeer::UPDATED_AT);
$c->addAscendingOrderByColumn(categoryKuserPeer::ID);
$c->setLimit(10000);

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
//$sphinxCon = DbManager::getSphinxConnection();
categoryKuserPeer::setUseCriteriaFilter(false);
$categoryKusers = categoryKuserPeer::doSelect($c, $con);
categoryKuserPeer::setUseCriteriaFilter(true);
$sphinx = new kSphinxSearchManager();
while(count($categoryKusers))
{
	foreach($categoryKusers as $categoryKuser)
	{
	    /* @var $categoryKuser categoryKuser */
		KalturaLog::log('$categoryKuser id ' . $categoryKuser->getId() . ' updated at '. $categoryKuser->getUpdatedAt(null));
		
		try {
			$ret = $sphinx->saveToSphinx($categoryKuser, true);
		}
		catch(Exception $e){
			KalturaLog::err($e->getMessage());
			exit -1;
		}
	}
	
	$c->setOffset($c->getOffset() + count($categoryKusers));
	kMemoryManager::clearMemory();
	$categoryKusers = categoryKuserPeer::doSelect($c, $con);
}

KalturaLog::log('Done. Current time: ' . time());
exit(0);
