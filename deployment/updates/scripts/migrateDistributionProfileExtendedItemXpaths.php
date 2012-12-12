<?php
/**
 * Usage:
 * php migrateAnnotationsDepthAndChildren.php [realrun/dryrun] [startUpdatedAt] [limit]
 * 
 * Defaults are: dryrun, startUpdatedAt is zero, no limit
 * 
 * @package deploy
 * @subpackage update
 */

require_once(dirname(__FILE__).'/../../bootstrap.php');

$startUpdatedAt = null;
$page = 200;

$dryRun = true;
if(isset($argv[1]) && strtolower($argv[1]) == 'realrun')
{
	$dryRun = false;
}
else 
{
	KalturaLog::info('Using dry run mode');
}
KalturaStatement::setDryRun($dryRun);

$partnerId = null;
if(isset($argv[2]))
{
	$partnerId = $argv[2];
}

$limit = null;
if(isset($argv[3]))
{
	$limit = $argv[3];
}

$criteria = new Criteria();
$criteria->addAscendingOrderByColumn(DistributionProfilePeer::UPDATED_AT);

if($limit)
	$criteria->setLimit(min($page, $limit));
else
	$criteria->setLimit($page);
	
$results = DistributionProfilePeer::doSelect($criteria);
$migrated = 0;
while (count($results) && (!$limit || $migrated < $limit))
{
	$migrated += count($results);
	foreach ($results as $result)
	{
		/* @var $result ConfigurableDistributionProfile */
		if (method_exists($result, 'getItemXpathsToExtend')&& $result->getItemXpathsToExtend() && is_array($result->getItemXpathsToExtend()))
		{
			$migrationArray = array();
			foreach ($result->getItemXpathsToExtend() as $itemXPath)
			{
				if (is_string($itemXPath))
				{
					$itemXPath .= "/";
					$itemXPathItem = new kExtendingItemMrssParameter();
					$itemXPathItem->setXpath($itemXPath);
					$identifier = new kEntryIdentifier();
					$identifier->setIdentifier(EntryIdentifierField::ID);
					$identifier->setExtendedFeatures(1,10175);
					$itemXPathItem->setExtensionMode(MrssExtensionMode::APPEND);
					$itemXPathItem->setIdentifier($identifier);
					$migrationArray[] = $itemXPathItem;
				}
			}
			$result->setItemXpathsToExtend($migrationArray);
			$result->save();
		}
	}
	
	kMemoryManager::clearMemory();

	$nextCriteria = clone $criteria;
	$nextCriteria->setOffset($migrated);
	$results = DistributionProfilePeer::doSelect($nextCriteria);
	
}

KalturaLog::info("Done - migrated ". $migrated ." items");