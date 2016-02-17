<?php
require_once (__DIR__ . '/../../bootstrap.php');

CONST LIMIT = 500;
const INITIAL_CREATED_AT_VALUE = '2000-01-01 00:00:00';

$c = new Criteria();
$c->add(entryPeer::TYPE, entryType::LIVE_STREAM);
$c->addAscendingOrderByColumn(entryPeer::UPDATED_AT);
$c->setLimit(LIMIT);

$updatedAtValue = INITIAL_CREATED_AT_VALUE;
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
		$mediaServers = $liveEntry->getMediaServers();
		if(count($mediaServers))
		{
			foreach ($mediaServers as $key => $mediaServer)
			{
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
	}
	$updatedAtValue = $liveEntry->getUpdatedAt();
}


?>