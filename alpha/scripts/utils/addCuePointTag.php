<?php

require_once(__DIR__ . '/../bootstrap.php');

if ($argc < 4)
	die("Usage: " . basename(__FILE__) . " <partner ids, comma separated or file name> <tag> <type of cue point to update- annotation.Annotation/adCuePoint.Ad/codeCuePoint.Code/all>[realrun / dryrun]\n");

// input parameters
$partnerIds = $argv[1];
$tag = trim($argv[2]);
$type = null;

try
{
	$type = $argv[3] != 'all' ? kPluginableEnumsManager::apiToCore ('CuePointType', $argv[3]) : null;
}
catch (kCoreException $e)
{
	if ($argv[3] != 'all')
		die ('Unrecognized cue point type');
}

$dryRun = (!isset($argv[4]) || $argv[4] != 'realrun');

KalturaStatement::setDryRun($dryRun);

if(file_exists($partnerIds))
	$partnerIds = file($partnerIds);
else
	$partnerIds = explode(',', $partnerIds);
	
foreach($partnerIds as $partnerId)
{
	$partnerId = trim($partnerId);
	if(!$partnerId || !is_numeric($partnerId))
		continue;
		
	$criteria = new Criteria();
	$criteria->add(CuePointPeer::PARTNER_ID, $partnerId);
	$criteria->add(CuePointPeer::STATUS, CuePointStatus::DELETED, Criteria::NOT_EQUAL);
	if ($type)
	{
		$criteria->add(CuePointPeer::TYPE, $type);
	}
	$criteria->addAscendingOrderByColumn(CuePointPeer::INT_ID);
	$criteria->setLimit(50);
	
	$partnerCuePoints = 0;
	$cuePoints = CuePointPeer::doSelect($criteria);
	while(count($cuePoints))
	{
		foreach($cuePoints as $cuePoint)
		{
			/* @var $cuePoint CuePoint */
			$tags = $cuePoint->getTags() ? explode(',', $cuePoint->getTags()) : array();
			array_walk($tags, 'trim');
			if(!in_array($tag, $tags))
			{
				$tags[] = $tag;
				$cuePoint->setTags(implode(',', $tags));
				$cuePoint->save();
				
				kEventsManager::flushEvents();
			}
		}
		kMemoryManager::clearMemory();
		
		$partnerCuePoints += count($cuePoints);
		$criteria->setOffset($partnerCuePoints);
		$cuePoints = CuePointPeer::doSelect($criteria);
	}
	KalturaLog::debug("Added tag [$tag] to $partnerCuePoints cue-points for partner $partnerId");
}
KalturaLog::debug("Done!!!");
