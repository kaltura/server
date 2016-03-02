<?php
require_once (__DIR__ . '/../../bootstrap.php');

CONST LIMIT = 500;
$updatedAtValue = '2000-01-01 00:00:00';

//Script doesn't have to start at the beginning of time, just call it with the initial time you want.
if ($argc == 2)
{
	$updatedAtValue = $argv[1];
}

$c = new Criteria();
$c->add(entryPeer::TYPE, entryType::LIVE_STREAM);
$c->addAscendingOrderByColumn(entryPeer::UPDATED_AT);
$c->setLimit(LIMIT);

$liveEntries = array(1);
while(!empty($liveEntries))
{
	$c->add(entryPeer::UPDATED_AT, $updatedAtValue, Criteria::GREATER_THAN);
	$liveEntries = entryPeer::doSelect($c);
	foreach($liveEntries as $liveEntry)
	{
		
		/**
		 * @var LiveStreamEntry $liveEntry
		 */
		$mediaServerIds = array();
		$mediaServers = $liveEntry->getDeprecatedMediaServers();
		if(count($mediaServers))
		{
			foreach ($mediaServers as $key => $mediaServer)
			{
				$mediaServerIds[] = $mediaServer->getMediaServerId();
				/**
				 * @var kLiveMediaServer $mediaServer
				 */
				$liveEntryServerNode = new LiveEntryServerNode();
				$liveEntryServerNode->setEntryId($liveEntry->getId());
				$liveEntryServerNode->setServerNodeId($mediaServer->getMediaServerId());
				$liveEntryServerNode->setPartnerId($liveEntry->getPartnerId());
				$liveEntryServerNode->setStatus($liveEntry->getLiveStatus());
				/* @var EntryServerNodeType $entryServerNodeType*/
				$entryServerNodeType = EntryServerNodeType::LIVE_PRIMARY;
				if ($mediaServer->getIndex() == 1)
				{
					$entryServerNodeType = EntryServerNodeType::LIVE_BACKUP;
				}
				$liveEntryServerNode->setServerType($entryServerNodeType);
				$liveEntryServerNode->save();
				KalturaLog::debug("entryId [".$liveEntryServerNode->getEntryId()."] server-node [".$liveEntryServerNode->getServerNodeId()."] server-type [".$liveEntryServerNode->getServerType()."] ");
			}
		}

	}
	$updatedAtValue = $liveEntry->getUpdatedAt();
}
KalturaLog::debug("last updated at was ".$updatedAtValue);

?>