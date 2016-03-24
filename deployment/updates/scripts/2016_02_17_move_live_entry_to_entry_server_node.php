<?php
require_once (__DIR__ . '/../../bootstrap.php');

CONST LIMIT = 1000;

$updatedAtValue = '2000-01-01 00:00:00';

//Script doesn't have to start at the beginning of time, just call it with the initial time you want.
if ($argc == 2)
{
	$updatedAtValue = $argv[1];
}

$c = new SphinxEntryCriteria();
$c->addCondition(entryIndex::DYNAMIC_ATTRIBUTES . '.' . LiveEntry::IS_LIVE . ' = 1');
$c->add(entryPeer::UPDATED_AT, $updatedAtValue, Criteria::GREATER_THAN);
$c->add(entryPeer::TYPE, entryType::LIVE_STREAM);
$c->addOrderBy('updated_at');
$c->setLimit(LIMIT);

$liveEntries = entryPeer::doSelect($c);
foreach($liveEntries as $liveEntry)
{
	/* @var $liveEntry LiveEntry */
	$mediaServers = $liveEntry->getFromCustomData(null, LiveEntry::CUSTOM_DATA_NAMESPACE_MEDIA_SERVERS, array());
	if(count($mediaServers))
	{
		foreach ($mediaServers as $key => $mediaServer)
		{
			if(!$mediaServer instanceof kLiveMediaServer)
				continue;
			
			/* @var $mediaServer kLiveMediaServer */
			$liveStatus = $liveEntry->getFromCustomData('live_status_'.$mediaServer->getIndex(), null, EntryServerNodeStatus::STOPPED);
			if($liveStatus === EntryServerNodeStatus::STOPPED)
				continue;
			
			$liveEntryServerNode = EntryServerNodePeer::retrieveByEntryIdAndServerType($liveEntry->getId(), $mediaServer->getIndex());
			if($liveEntryServerNode)
			{
				if($liveEntryServerNode->getStatus() !== $liveStatus)
				{
					$liveEntryServerNode->setStatus($liveStatus);
					$liveEntryServerNode->save();
				}
				
				continue;
			}
			
			$liveEntryServerNode = new LiveEntryServerNode();
			$liveEntryServerNode->setStatus($liveStatus);
			$liveEntryServerNode->setEntryId($liveEntry->getId());
			$liveEntryServerNode->setPartnerId($liveEntry->getPartnerId());
			
			$liveEntryServerNode->setDc($mediaServer->getDc());
			$liveEntryServerNode->setServerNodeId($mediaServer->getMediaServerId());
			$liveEntryServerNode->setServerType($mediaServer->getIndex());
			$liveEntryServerNode->save();
			
			KalturaLog::debug("entryId [".$liveEntryServerNode->getEntryId()."] server-node [".$liveEntryServerNode->getServerNodeId()."] server-type [".$liveEntryServerNode->getServerType()."] ");
		}
	}
}

if(count($liveEntries) === LIMIT && $liveEntry)
	KalturaLog::warning("Live entries count equals script limit, please run script again with latest updated at value = ", $liveEntry->getUpdatedAt());

?>