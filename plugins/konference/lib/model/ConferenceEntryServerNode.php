<?php
/**
 * @package Core
 * @subpackage model
 */
class ConferenceEntryServerNode extends EntryServerNode
{
	const OM_CLASS = 'ConferenceEntryServerNode';

	const CUSTOM_DATA_CONFERENCE_STATUS = 'conf_status';
	const CUSTOM_DATA_CONFERENCE_REGISTERED = 'registered';
	const CUSTOM_DATA_LAST_ALLOCATE_TIME = 'last_allocate';

	public function getConfRoomStatus()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_CONFERENCE_STATUS, null, ConferenceRoomStatus::CREATED);
	}

	public function setConfRoomStatus($v)
	{
		return $this->putInCustomData(self::CUSTOM_DATA_CONFERENCE_STATUS, $v);
	}

	public function validateEntryServerNode()
	{
		// TODO: Implement validateEntryServerNode() method.
	}

	public function buildRoomURL($partnerId = null)
	{
		$liveStreamEntry = entryPeer::retrieveByPK($this->getEntryId());
		/** @var LiveStreamEntry $liveStreamEntry */
		if (!$liveStreamEntry)
		{
			throw new kCoreException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->getEntryId());
		}
		$conferenceServerNode = ServerNodePeer::retrieveByPK($this->getServerNodeId());
		if (!$conferenceServerNode)
		{
			throw new kCoreException(KalturaErrors::SERVER_NODE_NOT_FOUND, $this->getServerNodeId());
		}
		/**
		 * @var ConferenceServerNode $conferenceServerNode
		 */
		if ($this->getConfRoomStatus() != ConferenceRoomStatus::READY)
		{
			throw new kCoreException(KalturaKonferenceErrors::ROOM_NOT_READY, $this->getId());
		}

		$hostname = $conferenceServerNode->getHostName();
		$manager = kBroadcastUrlManager::getInstance($partnerId);
		$url = $manager->getRTCBroadcastingUrl($liveStreamEntry, 'https', $hostname, $conferenceServerNode->getExternalPort());
		return $url;
	}

	public function getRegistered()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_CONFERENCE_REGISTERED, null, 0);
	}

	public function setRegistered($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_CONFERENCE_REGISTERED, $v);
	}

	public function incRegistered()
	{
		$this->setRegistered($this->getRegistered() + 1);
	}

	public function getLastAllocationTime()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_LAST_ALLOCATE_TIME);
	}

	public function setLastAllocationTime($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_LAST_ALLOCATE_TIME, $v);
	}

}
