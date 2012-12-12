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
$limit = null;
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

if(isset($argv[3]))
{
	$limit = $argv[3];
}

$criteria = new Criteria();
$criteria->addAscendingOrderByColumn(syndicationFeedPeer::UPDATED_AT);
if ($partnerId)
	$criteria->addAscendingOrderByColumn(syndicationFeedPeer::PARTNER_ID, $partnerId);

if($limit)
	$criteria->setLimit(min($page, $limit));
else
	$criteria->setLimit($page);
	
$results = syndicationFeedPeer::doSelect($criteria);
$migrated = 0;
while (count($results) && (!$limit || $migrated < $limit))
{
	$migrated += count($results);
	foreach ($results as $result)
	{
		/* @var $result syndicationFeed */
		$mrssParams = $result->getMrssParameters();
		if ($mrssParams)
		{
			/* @var $mrssParams kMrssParameters */
			$migrationArray = array();
			$itemXPathsToExtend = $mrssParams->getItemXpathsToExtend();
			if ($itemXPathsToExtend)
			foreach ($itemXPathsToExtend as $itemXPath)
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
			
			$mrssParams->setItemXpathsToExtend($migrationArray);
			$result->setMrssParameters($mrssParams);
			$result->save();
		}
	}
	
	kMemoryManager::clearMemory();

	$nextCriteria = clone $criteria;
	$nextCriteria->setOffset($migrated);
	$results = syndicationFeedPeer::doSelect($nextCriteria);
}

KalturaLog::info("Done - migrated ". count($results) ." items");