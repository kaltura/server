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
}