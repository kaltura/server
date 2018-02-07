<?php

/**
 *
 * @service conference
 * @package plugins.konference
 * @subpackage api.services
 */
class ConferenceService extends KalturaBaseService {

	/**
	 * Allocates a conference room or returns ones that has already been allocated
	 *
	 * @action allocateConferenceRoom
	 * @actionAlias liveStream.allocateConferenceRoom
	 * @param string $entryId
	 * @return KalturaConferenceEntryServerNode
	 * @throws KalturaAPIException
	 */
	public function allocateConferenceRoomAction($entryId)
	{
		$existingConfRoom = $this->findExistingConferenceRoom($entryId);
		if ($existingConfRoom)
			return $existingConfRoom;
		KalturaLog::log('Could not find existing conference room');

		$liveEntryService = new LiveStreamService();
		$liveEntryService->dumpApiRequest($entryId);
		$lockKey = "allocate_conference_room_" . $entryId;
		$conference = kLock::runLocked($lockKey, array($this, 'allocateConferenceRoomImpl'), array($entryId));
		return $conference;
	}

	public function allocateConferenceRoomImpl($entryId)
	{
		//In case until this method is run under lock another process already created the conf room.
		$existingConfRoom = $this->findExistingConferenceRoom($entryId);
		if ($existingConfRoom)
			return $existingConfRoom;

		$serverNodes = ServerNodePeer::retrieveActiveUnoccupiedServerNodesByType(KonferencePlugin::getCoreValue('serverNodeType',ConferenceServerNodeType::CONFERENCE_SERVER));
		if (!$serverNodes)
		{
			KalturaLog::debug("Could not find avaialable conference server node in pool");
			if (kConf::get('CONFERNCE_SERVER_NODE_DYNAMIC_ALLOCATION', null, null) === true)
			{
				throw new KalturaAPIException(KalturaErrors::INTERNAL_SERVERL_ERROR);
			}
			throw new KalturaAPIException(KalturaKonferenceErrors::CONFERENCE_ROOMS_UNAVAILABLE);
		}
		$confEntryServerNode = new ConferenceEntryServerNode();
		$confEntryServerNode->setEntryId($entryId);
		$confEntryServerNode->setServerNodeId($serverNodes[0]->getId());
		$confEntryServerNode->setServerType($serverNodes[0]->getType());
		$confEntryServerNode->setConfRoomStatus(ConferenceRoomStatus::READY);
		$confEntryServerNode->save();

		$outObj = new KalturaConferenceEntryServerNode();
		$outObj->fromObject($confEntryServerNode);
		return $outObj;
	}


	protected function findExistingConferenceRoom($entryId)
	{
		$existingConfRoom = EntryServerNodePeer::retrieveByEntryIdAndServerType($entryId, KonferencePlugin::getCoreValue('serverNodeType',ConferenceServerNodeType::CONFERENCE_SERVER));
		if ($existingConfRoom)
		{
			$outObj = new KalturaConferenceEntryServerNode();
			$outObj->fromObject($existingConfRoom);
			return $outObj;
		}
		return null;
	}

	/**
	 * Returns a url to broadcast to the specified room
	 *
	 * @action getRoomUrl
	 * @actionAlias liveStream.getRoomUrl
	 * @param string $confRoomId
	 * @return string
	 * @throws KalturaAPIException
	 */
	public function getRoomUrl($confRoomId)
	{
		$room = EntryServerNodePeer::retrieveByPK($confRoomId);
		if (!$room)
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_SERVER_NODE_NOT_FOUND, $confRoomId);
		}
		/** @var ConferenceEntryServerNode $room */
		try
		{
			$url = $room->buildRoomUrl($this->getPartnerId());
		}
		catch (kCoreException $e)
		{
			throw new KalturaAPIException($e->getMessage());
		}
		return $url;
	}

}
