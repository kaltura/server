<?php

/**
 *
 * @service conference
 * @package plugins.konference
 * @subpackage api.services
 */
class ConferenceService extends KalturaBaseService {
	const CAN_REACH_EXPECTED_VALUE = 'kaltura';

	/**
	 * Allocates a conference room or returns ones that has already been allocated
	 *
	 * @action allocateConferenceRoom
	 * @actionAlias liveStream.allocateConferenceRoom
	 * @param string $entryId
	 * @return KalturaRoomDetails
	 * @throws KalturaAPIException
	 */
	public function allocateConferenceRoomAction($entryId)
	{
		$liveEntryDb = entryPeer::retrieveByPK($entryId);
		if (!$liveEntryDb)
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		}
		/**
		 * @var LiveStreamEntry $liveEntryDb
		 */
		if ($liveEntryDb->getType() != entryType::LIVE_STREAM)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_TYPE, $liveEntryDb->getName(), $liveEntryDb->getType(), entryType::LIVE_STREAM);
		}

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

		$serverNode = $this->findFreeServerNode();
		$confEntryServerNode = new ConferenceEntryServerNode();
		$confEntryServerNode->setEntryId($entryId);
		$confEntryServerNode->setServerNodeId($serverNode->getId());
		$confEntryServerNode->setServerType($serverNode->getType());
		$confEntryServerNode->setConfRoomStatus(ConferenceRoomStatus::READY);
		$confEntryServerNode->save();

		$outObj = new KalturaRoomDetails();
		$outObj->roomUrl = $confEntryServerNode->buildRoomUrl($this->getPartnerId());
		return $outObj;
	}


	protected function findExistingConferenceRoom($entryId)
	{
		$existingConfRoom = EntryServerNodePeer::retrieveByEntryIdAndServerType($entryId, KonferencePlugin::getCoreValue('serverNodeType',ConferenceServerNodeType::CONFERENCE_SERVER));
		if ($existingConfRoom)
		{
			/**
			 * @var EntryServerNode $existingConfRoom
			 */
			$serverNode = ServerNodePeer::retrieveByPK($existingConfRoom->getServerNodeId());
			if (!$this->canReach($serverNode))
			{
				$serverNode->setStatus(ServerNodeStatus::NOT_REGISTERED);
				$serverNode->save();
				return null;
			}
			$outObj = new KalturaRoomDetails();
			$outObj->roomUrl = $existingConfRoom->buildRoomUrl($this->getPartnerId());
			return $outObj;
		}
		return null;
	}

	protected function findFreeServerNode()
	{
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
		foreach ($serverNodes as $serverNode)
		{
			/**
			 * @var ConferenceServerNode $serverNode
			 */
			if ($this->canReach($serverNode))
				return $serverNode;
			$serverNode->setStatus(ServerNodeStatus::NOT_REGISTERED);
			$serverNode->save();
		}
		throw new KalturaAPIException(KalturaKonferenceErrors::CONFERENCE_ROOMS_UNREACHABLE);
	}

	protected function canReach(ConferenceServerNode $serverNode)
	{
		//TODO: make sure that HTTP protocol is available for RTC servers.
		$aliveUrl = "https://" . $serverNode->getHostName() . ":" . $serverNode->getExternalPort() . "/alive";
		$content = KCurlWrapper::getContent($aliveUrl);
		if (strtolower($content) === self::CAN_REACH_EXPECTED_VALUE)
			return true;
		return false;
	}

	/**
	 * Returns a url to broadcast to the specified room
	 *
	 * @action getRoomDetails
	 * @actionAlias liveStream.getRoomDetails
	 * @param string $confRoomId
	 * @return KalturaRoomDetails
	 * @throws KalturaAPIException
	 */
	public function getRoomDetailsAction($confRoomId)
	{
		$res = new KalturaRoomDetails();
		$room = EntryServerNodePeer::retrieveByPK($confRoomId);
		if (!$room)
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_SERVER_NODE_NOT_FOUND, $confRoomId);
		}
		/** @var ConferenceEntryServerNode $room */
		try
		{
			$res->roomUrl = $room->buildRoomUrl($this->getPartnerId());
		}
		catch (kCoreException $e)
		{
			throw new KalturaAPIException($e->getMessage());
		}

		return $res;
	}

	/**
	 * When the conf is finished this API should be called.
	 *
	 * @action finishConf
	 * @actionAlias liveStream.finishConf
	 * @param string $hostname
	 * @return KalturaServerNode
	 * @throws KalturaAPIException
	 */
	public function finishConfAction($hostname)
	{
		$serverNodeDb = ServerNodePeer::retrieveByHostname($hostname);
		if (!$serverNodeDb)
		{
			KalturaLog::info("Could not find server node with hostname [" . $hostname . "]");
			throw new KalturaAPIException(KalturaErrors::SERVER_NODE_NOT_FOUND, $hostname);
		}
		/**
		 * @var ConferenceServerNode $serverNodeDb
		 */
		if ($serverNodeDb->getType() != KonferencePlugin::getCoreValue('ServerNodeType', ConferenceServerNodeType::CONFERENCE_SERVER) )
		{
			KalturaLog::info("Wrong server node type received");
			throw new KalturaAPIException(KalturaErrors::SERVER_NODE_WRONG_TYPE);
		}
		$serverNodeDb->removeAttachedEntryServerNodes();
		$serverNode = new KalturaConferenceServerNode();
		$serverNode->fromObject($serverNodeDb);
		return $serverNode;
	}

}
