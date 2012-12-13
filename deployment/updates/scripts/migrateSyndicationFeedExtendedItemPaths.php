<?php
/**
 * Usage:
 * php migrateAnnotationsDepthAndChildren.php [realrun/dryrun] [feed IntId] [partnerId][limit]
 * 
 * Defaults are: dryrun, startUpdatedAt is zero, no limit
 * 
 * @package deploy
 * @subpackage update
 */

require_once(dirname(__FILE__).'/../../bootstrap.php');

$startUpdatedAt = null;
$limit = null;
$page = 500;

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

$lastFeedId = null;
if (isset($argv[2]))
{
	$lastFeedId = $argv[2];
}

$partnerId = null;
if(isset($argv[3]))
{
	$partnerId = $argv[3];
}

if(isset($argv[4]))
{
	$limit = $argv[4];
}

$criteria = new Criteria();
$criteria->add(syndicationFeedPeer::TYPE, syndicationFeedType::KALTURA_XSLT);
$criteria->addAscendingOrderByColumn(syndicationFeedPeer::INT_ID);
if ($partnerId)
	$criteria->add(syndicationFeedPeer::PARTNER_ID, $partnerId);
if ($lastFeedId)
	$criteria->add(syndicationFeedPeer::INT_ID, $lastFeedId, Criteria::GREATER_THAN);

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
			$lastFeedId = $result->getIntId();
			KalturaLog::debug("last int id handled: $lastFeedId");
		}
	}
	
	kMemoryManager::clearMemory();

	$nextCriteria = clone $criteria;
	$nextCriteria->setOffset($migrated);
	$results = syndicationFeedPeer::doSelect($nextCriteria);
}

KalturaLog::info("Done - migrated ". count($results) ." items");