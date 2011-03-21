<?php

$dryRun = true; //TODO: change for real run
$stopFile = dirname(__FILE__).'/stop_flavor_migration'; // creating this file will stop the script
$entryLimitEachLoop = 1000;

//------------------------------------------------------

require_once(dirname(__FILE__).'/../../bootstrap.php');

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
	
$join = new Join();	
$join->setJoinType(Criteria::INNER_JOIN);
$join->addCondition(assetParamsPeer::ID, flavorParamsConversionProfilePeer::FLAVOR_PARAMS_ID);
$join->addCondition(assetParamsPeer::READY_BEHAVIOR, flavorParamsConversionProfilePeer::READY_BEHAVIOR, Criteria::NOT_EQUAL);

$c = new Criteria();
$c->addJoinObject($join);
$c->addSelectColumn(assetParamsPeer::READY_BEHAVIOR);
$c->addSelectColumn(flavorParamsConversionProfilePeer::ID);
$c->add(assetParamsPeer::READY_BEHAVIOR, 0, Criteria::GREATER_THAN);
$c->setLimit($entryLimitEachLoop);

$stmt = flavorParamsConversionProfilePeer::doSelectStmt($c, $con);
$links = $stmt->fetchAll(PDO::FETCH_ASSOC);

while(count($links))
{
	$updates = array(
		flavorParamsConversionProfile::READY_BEHAVIOR_OPTIONAL => array(),
		flavorParamsConversionProfile::READY_BEHAVIOR_REQUIRED => array(),
	);
	
	foreach($links as $link)
		$updates[$link['READY_BEHAVIOR']][] = $link['ID'];
		
	foreach($updates as $readyBehavior => $update)
	{
		if(!count($update))
			continue;
			
		$conditionCriteria = new Criteria();
		$conditionCriteria->add(flavorParamsConversionProfilePeer::ID, $update, Criteria::IN);
		
		$updateCriteria = new Criteria();
		$updateCriteria->add(flavorParamsConversionProfilePeer::READY_BEHAVIOR, $readyBehavior);
		
		$affectedRows = BasePeer::doUpdate($conditionCriteria, $updateCriteria);
		KalturaLog::log("Updated [$affectedRows] rows to [$readyBehavior] ready behavior");
	}
		
	$stmt = flavorParamsConversionProfilePeer::doSelectStmt($c, $con);
	$links = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
KalturaLog::log("Done");