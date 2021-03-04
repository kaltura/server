<?php
require_once (dirname(__FILE__) . '../bootstrap.php');

if ($argc < 2)
{
	echo PHP_EOL . ' ---- Update Flavor Asset File Ext ---- ' . PHP_EOL;
	echo ' Execute: php ' . $argv[0] . ' [ /path/to/flavor_assets_id_list ] [realrun / dryrun]' . PHP_EOL;
	die(' Error: missing flavor_assets_id_list file path' . PHP_EOL . PHP_EOL);
}

$flavorAssetIdsPath = $argv[1];
$flavorAssetIdsArr = file($flavorAssetIdsPath) or die(' Error: Cannot open file at path "' . $flavorAssetIdsPath . '"' . PHP_EOL);

$dryRun = true;
if (isset($argv[2]) && $argv[2] === 'realrun')
{
	$dryRun = false;
}
KalturaStatement::setDryRun($dryRun);
KalturaLog::info($dryRun ? 'DRY RUN' : 'REAL RUN');

$total = count($flavorAssetIdsArr);
$counter = 1;

foreach ($flavorAssetIdsArr as $flavorAssetId)
{
	if (empty($flavorAssetId))
	{
		continue;
	}

	$flavorAssetId = trim($flavorAssetId);
	$flavorAsset = assetPeer::retrieveById($flavorAssetId);

	if ($flavorAsset)
	{
		$oldExt = $flavorAsset->getFileExt();
		$newExt = kAssetUtils::getFileExtension($flavorAsset->getContainerFormat());
		if ($newExt !== $oldExt)
		{
			try
			{
				$flavorAsset->setFileExt($newExt);
				$flavorAsset->save();

				KalturaLog::debug(' Asset-ID: ' . $flavorAsset->getId() . ' success [' . $oldExt . '] -> [' . $newExt . ']');
			}
			catch (Exception $e)
			{
				KalturaLog::debug(' Asset-ID: ' . $flavorAsset->getId() . ' save-error');
				KalturaLog::debug($e->getMessage());
			}
		}
		else
		{
			KalturaLog::debug(' Asset-ID: ' . $flavorAsset->getId() . ' skipping [' . $oldExt . '] = [' . $newExt . ']');
		}
	}
	else
	{
		KalturaLog::debug(' Asset-ID: ' . $flavorAssetId . ' not-found');
	}

	if ($counter % 1000 === 0)
	{
		kMemoryManager::clearMemory();
		KalturaLog::debug(' Sleeping for 15 sec [' . $counter . ' / ' . $total . ']');
		sleep(15);
	}

	$counter++;
}

KalturaLog::debug(' Script Finished');
?>

