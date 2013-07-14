<?php

chdir(dirname(__FILE__));
require_once(dirname(__FILE__) . '/../bootstrap.php');

if (count($argv) !== 2)
{
	die('pleas provide partner id as input' . PHP_EOL . 
		'to run script: ' . basename(__FILE__) . ' X' . PHP_EOL . 
		'whereas X is partner id' . PHP_EOL);
}

$partner_id = @$argv[1];
$partner = PartnerPeer::retrieveByPK($partner_id);
if(!$partner)
{
        die('no such partner.'.PHP_EOL);
}

$c = new Criteria();
$c->add(assetPeer::PARTNER_ID, $partner_id);
$c->add(assetPeer::IS_ORIGINAL, true);
$c->add(assetPeer::STATUS, flavorAsset::FLAVOR_ASSET_STATUS_READY);

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
$flavorAssets = assetPeer::doSelect($c, $con);
$changedEntriesCounter = 0;

foreach ($flavorAssets as $flavorAsset)
{
	$flavorSyncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
	try
	{
		if (!kFileSyncUtils::file_exists($flavorSyncKey, true))
		{
			echo 'changed source flavor asset to status deleted for entry: ' . $flavorAsset->getEntryId() .
				' and for flavor id ' . $flavorAsset->getId() . PHP_EOL;
			// set the status of the flavor asset to deleted and set deleted time (taken from flavorAssetService)
			$entry = $flavorAsset->getEntry();
			if ($entry)
			{
				$entry->removeFlavorParamsId($flavorAsset->getFlavorParamsId());
				$entry->save();
			}
			$flavorAsset->setStatus(flavorAsset::FLAVOR_ASSET_STATUS_DELETED);
			$flavorAsset->setDeletedAt(time());
			$flavorAsset->save();
			
			$changedEntriesCounter++;
		}
	}
	catch (Exception $e)
	{
	}
}

echo "Done. {$changedEntriesCounter} unreachable source flavor asset where deleted" . PHP_EOL;
