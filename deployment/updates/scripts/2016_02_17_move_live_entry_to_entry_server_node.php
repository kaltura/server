<?php
require_once (__DIR__ . '/../../bootstrap.php');

CONST LIMIT = 500;
$initialCreatedAtValue = '2000-01-01 00:00:00';

//Script doesn't have to start at the beginning of time, just call it with the initial time you want.
if ($argc == 2)
{
	$initialCreatedAtValue = $argv[1];
}

$c = new Criteria();
$c->add(entryPeer::TYPE, entryType::LIVE_STREAM);
$c->addAscendingOrderByColumn(entryPeer::UPDATED_AT);
$c->setLimit(LIMIT);

$updatedAtValue = $initialCreatedAtValue;
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
		$mediaServers = $liveEntry->getMediaServers();
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
				$entryServerNodeType = EntryServerNodeType::LIVE_PRIMARY;
				if ($mediaServer->getIndex() == 1)
				{
					$entryServerNodeType = EntryServerNodeType::LIVE_BACKUP;
				}
//				$liveEntryServerNode->save();
				KalturaLog::debug("I would like to save live-entry-server-node for entryId ["+$liveEntryServerNode->getEntryId()."] and server-node [".$liveEntryServerNode->getServerNodeId()."] ");
			}
		}
		$entryServerNodeCrit = new Criteria();
		$entryServerNodeCrit->add(EntryServerNodePeer::ENTRY_ID, $liveEntry->getId());
		if (count($mediaServerIds))
		{
			$entryServerNodeCrit->add(EntryServerNodePeer::SERVER_NODE_ID, $mediaServerIds, Criteria::NOT_IN );
		}
		EntryServerNodePeer::doDelete($entryServerNodeCrit);

	}
	$updatedAtValue = $liveEntry->getUpdatedAt();
}


?>