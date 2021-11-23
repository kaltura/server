<?php
require_once (dirname(__FILE__) . '/../bootstrap.php');

define('MAX_FLAVORS_IN_INTERVAL', 1000);
define('SLEEP_INTERVAL_IN_SEC', 15);

if ($argc < 2)
{
	KalturaLog::info(' ---- Update Flavor Asset File Ext ---- ');
	die (' Error execute script, Usage: php updateFlavorAssetFileExt.php
	 < /path/to/flavor_id_list || flavorId_1,flavorId_2,.. || flavor_id > 
	 < realrun / dryrun >' . PHP_EOL);
}

$dryRun = true;
if (isset($argv[2]) && $argv[2] === 'realrun')
{
	$dryRun = false;
}

KalturaStatement::setDryRun($dryRun);
KalturaLog::info($dryRun ? 'DRY RUN' : 'REAL RUN');

$flavorAssetIdsArr = isValidInput($argv[1]);
$totalFlavors = count($flavorAssetIdsArr);
$successFlavors = array();
$failedFlavors = array();
$skippedFlavors = array();
$notFoundFlavors = array();
$counter = 0;

foreach ($flavorAssetIdsArr as $flavorAssetId)
{
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
				$successFlavors[] = $flavorAssetId;
			}
			catch (Exception $e)
			{
				KalturaLog::debug(' Asset-ID: ' . $flavorAsset->getId() . ' save-error');
				KalturaLog::debug($e->getMessage());
				$failedFlavors[] = $flavorAssetId;
			}
		}
		else
		{
			KalturaLog::debug(' Asset-ID: ' . $flavorAsset->getId() . ' skipping [' . $oldExt . '] = [' . $newExt . ']');
			$skippedFlavors[] = $flavorAssetId;
		}
	}
	else
	{
		KalturaLog::debug(' Asset-ID: ' . $flavorAssetId . ' not-found');
		$notFoundFlavors[] = $flavorAssetId;
	}

	$counter++;

	if ($counter % MAX_FLAVORS_IN_INTERVAL === 0)
	{
		kMemoryManager::clearMemory();
		KalturaLog::debug(' Sleeping for 15 sec [' . $counter . ' / ' . $totalFlavors . ']');
		sleep(SLEEP_INTERVAL_IN_SEC);
	}
}

echoResults($totalFlavors, $successFlavors, $failedFlavors, $skippedFlavors, $notFoundFlavors);


function isValidInput($flavorInput)
{
	if (is_file($flavorInput))
	{
		$entries = file($flavorInput) or die (' Error: cannot open file at: "' . $flavorInput .'"' . PHP_EOL);;
		return $entries;
	}
	elseif (strpos($flavorInput, ','))
	{
		return explode(',', $flavorInput);
	}
	elseif (strpos($flavorInput,'_'))
	{
		return array($flavorInput);
	}
	else
	{
		die (' Error: invalid input supplied at: "' . $flavorInput . '"' . PHP_EOL);
	}
}


function echoResults($totalFlavors, $successFlavors, $failedFlavors, $skippedFlavors, $notFoundFlavors)
{
	KalturaLog::info('Script Finished');

	/* Display Entries Result */
	KalturaLog::info(' ---- Flavors Count ---- ');
	KalturaLog::info('Number of flavors received ' . $totalFlavors);

	if ($successFlavors)
	{
		KalturaLog::info(' ---- Success Flavors ---- ');
		KalturaLog::info('Number of successful flavors ' . count($successFlavors));
		foreach ($successFlavors as $successFlavor)
		{
			KalturaLog::info($successFlavor);
		}
	}

	if ($failedFlavors)
	{
		KalturaLog::info(' ---- Failed flavors ---- ');
		KalturaLog::info('Number of failed flavors ' . count($failedFlavors));
		foreach ($failedFlavors as $failedFlavor)
		{
			KalturaLog::info($failedFlavor);
		}
	}

	if ($skippedFlavors)
	{
		KalturaLog::info(' ---- Skipped flavors ---- ');
		KalturaLog::info('Number of skipped flavors ' . count($skippedFlavors));
		foreach ($skippedFlavors as $skippedFlavor)
		{
			KalturaLog::info($skippedFlavor);
		}
	}

	if ($notFoundFlavors)
	{
		KalturaLog::info(' ---- Not Found flavors ---- ');
		KalturaLog::info('Number of not found flavors ' . count($notFoundFlavors));
		foreach ($notFoundFlavors as $notFoundFlavor)
		{
			KalturaLog::info($notFoundFlavor);
		}
	}
}