<?php
/**
 * Call flavor assete setters to migrate from old columns to new custom data fields.
 * After all flavors will be migrated we can remove the columns from the db.
 *
 * @package Deployment
 * @subpackage updates
 */ 


$dryRun = true; //TODO: change for real run
if(in_array('realrun', $argv))
	$dryRun = false;
	
$stopFile = dirname(__FILE__).'/stop_flavor_migration'; // creating this file will stop the script
$countLimitEachLoop = 500;

//------------------------------------------------------

require_once(dirname(__FILE__).'/../../bootstrap.php');

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
KalturaStatement::setDryRun($dryRun);
	
$c = new Criteria();
$c->add(assetPeer::CUSTOM_DATA, '%FlavorBitrate%', Criteria::NOT_LIKE);
$c->add(assetPeer::STATUS, asset::FLAVOR_ASSET_STATUS_DELETED, Criteria::NOT_EQUAL);
$c->add(assetPeer::TYPE, assetType::FLAVOR);
$c->setLimit($countLimitEachLoop);

$flavors = assetPeer::doSelect($c, $con);

while($flavors && count($flavors))
{
	foreach($flavors as $flavor)
	{
		/* @var $flavor flavorAsset */
		$flavor->setBitrate($flavor->getBitrate());
		$flavor->setFrameRate($flavor->getFrameRate());
		$flavor->setVideoCodecId($flavor->getVideoCodecId());
		$flavor->save();
	}
	$flavors = assetPeer::doSelect($c, $con);
	sleep(1);
}
