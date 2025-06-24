<?php
chdir(dirname(__FILE__));

require_once(__DIR__ . '/../../../bootstrap.php');

SphinxCriteria::enableForceSkipSphinx();
$c = new Criteria();
if ($argc > 1 && is_numeric($argv[1]))
	$c->add(categoryPeer::UPDATED_AT, $argv[1], Criteria::GREATER_EQUAL);
if($argc > 2 && is_numeric($argv[2]))
	$c->add(categoryPeer::PARTNER_ID, $argv[2], Criteria::EQUAL);
if($argc > 3)
{
	if(is_numeric($argv[3]))
	{
		$c->add(categoryPeer::ID, $argv[3], Criteria::GREATER_EQUAL);
	}
	elseif(strpos($argv[3], '-') !== false) 
	{
		list($minId, $maxId) = explode('-', $argv[3], 2);
		if(is_numeric($minId) && is_numeric($maxId)) {
			$c->add(categoryPeer::ID, $minId, Criteria::GREATER_EQUAL);
			$c->addAnd(categoryPeer::ID, $maxId, Criteria::LESS_THAN);
		}
	}
}
if($argc > 4)
	categoryPeer::setUseCriteriaFilter((bool)$argv[4]);

$c->addAscendingOrderByColumn(categoryPeer::UPDATED_AT);
$c->addAscendingOrderByColumn(categoryPeer::ID);
$c->setLimit(1000);

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);

$categories = categoryPeer::doSelect($c, $con);
$elasticManager = new kElasticSearchManager();
while(count($categories))
{
	foreach($categories as $category)
	{
		KalturaLog::log('category id ' . $category->getId() . ' int id[' . $category->getIntId() . '] last updated at ['. $category->getUpdatedAt(null) .']');
		
		try
		{
			$elasticManager->saveToElastic($category);
		}
		catch(Exception $e){
			KalturaLog::err($e->getMessage());
			exit -1;
		}
	}
	
	$c->setOffset($c->getOffset() + count($categories));
	kMemoryManager::clearMemory();
	$categories = categoryPeer::doSelect($c, $con);
}

KalturaLog::log('Done. Current time: ' . time());
exit(0);
