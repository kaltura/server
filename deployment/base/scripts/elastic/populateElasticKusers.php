<?php
chdir(dirname(__FILE__));

require_once(__DIR__ . '/../../../bootstrap.php');


$c = new Criteria();

if($argc > 1 && is_numeric($argv[1]))
	$c->add(kuserPeer::UPDATED_AT, $argv[1], Criteria::GREATER_EQUAL);
if($argc > 2 && is_numeric($argv[2]))
	$c->add(kuserPeer::PARTNER_ID, $argv[2], Criteria::EQUAL);
if($argc > 3 && is_numeric($argv[3]))
	$c->add(kuserPeer::ID, $argv[3], Criteria::GREATER_EQUAL);
if($argc > 4)
	kuserPeer::setUseCriteriaFilter((bool)$argv[4]);

$c->addAscendingOrderByColumn(kuserPeer::UPDATED_AT);
$c->addAscendingOrderByColumn(kuserPeer::ID);
$c->setLimit(1000);

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);

$kusers = kuserPeer::doSelect($c, $con);
$elasticManager = new kElasticSearchManager();
while(count($kusers))
{
	foreach($kusers as $kuser)
	{
		KalturaLog::log('kuser id ' . $kuser->getId() . ' updated at '. $kuser->getUpdatedAt(null));

		try 
		{
			$elasticManager->saveToElastic($kuser);
		}
		catch(Exception $e){
			KalturaLog::err($e->getMessage());
			exit -1;
		}
	}

	$c->setOffset($c->getOffset() + count($kusers));
	kMemoryManager::clearMemory();
	$kusers = kuserPeer::doSelect($c, $con);
}

KalturaLog::log('Done. Current time: ' . time());
exit(0);
