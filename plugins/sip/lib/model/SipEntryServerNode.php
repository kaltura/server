<?php

/**
 * @package sip
 * @subpackage model
 */
class SipEntryServerNode extends EntryServerNode
{
	const OM_CLASS = 'SipEntryServerNode';

	const CUSTOM_DATA_SIP_STATUS = 'sip_status';
	const CUSTOM_DATA_SIP_ROOM_ID = 'sip_room_id';
	const CUSTOM_DATA_SIP_ROOM_PRIMARY_ADP = 'sip_primary_adp_id';
	const CUSTOM_DATA_SIP_ROOM_SECONDARY_ADP = 'sip_primary_secondary_id';

	public function getSipRoomStatus()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_SIP_STATUS, null, SipEntryServerNodeStatus::CREATED);
	}

	public function setSipRoomStatus($v)
	{
		return $this->putInCustomData(self::CUSTOM_DATA_SIP_STATUS, $v);
	}

	public function getSipRoomId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_SIP_ROOM_ID, null);
	}

	public function setSipRoomId($v)
	{
		return $this->putInCustomData(self::CUSTOM_DATA_SIP_ROOM_ID, $v);
	}

	public function getSipRoomPrimaryADP()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_SIP_ROOM_PRIMARY_ADP, null);
	}

	public function setSipRoomPrimaryADP($v)
	{
		return $this->putInCustomData(self::CUSTOM_DATA_SIP_ROOM_PRIMARY_ADP, $v);
	}

	public function getSipRoomSecondaryADP()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_SIP_ROOM_SECONDARY_ADP, null);
	}

	public function setSipRoomSecondaryADP($v)
	{
		return $this->putInCustomData(self::CUSTOM_DATA_SIP_ROOM_SECONDARY_ADP, $v);
	}

	/**
	 * @return bool
	 * @throws PropelException
	 */
	public function validateEntryServerNode()
	{
		$connectedEntryServerNodes = EntryServerNodePeer::retrieveByEntryIdAndStatuses($this->getEntryId(), EntryServerNodePeer::$connectedServerNodeStatuses);
		if(count($connectedEntryServerNodes))
		{
			KalturaLog::info("Entry [". $this->getEntryId() ."] is Live and Active.");
			if ($this->getSipRoomStatus() != SipEntryServerNodeStatus::ACTIVE)
			{
				$this->setSipRoomStatus(SipEntryServerNodeStatus::ACTIVE);
				$this->save();
			}
		}
		elseif ($this->getSipRoomStatus() == SipEntryServerNodeStatus::ACTIVE )
		{
			$this->setSipRoomStatus(SipEntryServerNodeStatus::ENDED);
			$this->save();
		}
		else
		{
			parent::validateEntryServerNode();
		}
		return true;
	}

	public function postInsert(PropelPDO $con = null)
	{
		$this->addTrackEntryInfo(TrackEntry::TRACK_ENTRY_EVENT_TYPE_ENTRY_SREVER_NODE_SIP, "serverNodeId=".$this->getServerNodeId().":action=created");
		parent::postInsert($con);
	}

	public function postDelete(PropelPDO $con = null)
	{
		$this->addTrackEntryInfo(TrackEntry::TRACK_ENTRY_EVENT_TYPE_ENTRY_SREVER_NODE_SIP, "serverNodeId=".$this->getServerNodeId().":action=deleted");
		KalturaLog::debug("Deleting SipEntryServerNode with id ".$this->getId());

		$liveEntry = entryPeer::retrieveByPK($this->getEntryId());
		if ($liveEntry)
		{
			/** @var LiveEntry $liveEntry * */
			$liveEntry->setIsSipEnabled(false);
			$liveEntry->save();
		}

		$pexipConfig = PexipUtils::initAndValidateConfig();
		if ($pexipConfig)
		{
			PexipHandler::deleteCallobjects($this->getSipRoomId(),$this->getSipRoomPrimaryADP(), $this->getSipRoomSecondaryADP(), $pexipConfig);
		}

		parent::postDelete($con);
	}

}