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
	const CUSTOM_DATA_SIP_ROOM_SECONDARY_ADP = 'sip_secondary_adp_id';

	public function getSipRoomId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_SIP_ROOM_ID, null);
	}

	public function setSipRoomId($v)
	{
		return $this->putInCustomData(self::CUSTOM_DATA_SIP_ROOM_ID, $v);
	}

	public function getSipPrimaryAdpId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_SIP_ROOM_PRIMARY_ADP, null);
	}

	public function setSipPrimaryAdpId($v)
	{
		return $this->putInCustomData(self::CUSTOM_DATA_SIP_ROOM_PRIMARY_ADP, $v);
	}

	public function getSipSecondaryAdpId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_SIP_ROOM_SECONDARY_ADP, null);
	}

	public function setSipSecondaryAdpId($v)
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
			KalturaLog::info('Entry [' . $this->getEntryId() . '] is Live and Active.');
			if ($this->getStatus() != SipEntryServerNodeStatus::ACTIVE)
			{
				$this->setStatus(SipEntryServerNodeStatus::ACTIVE);
				$this->save();
			}
		}
		elseif ($this->getStatus() == SipEntryServerNodeStatus::ACTIVE )
		{
			$this->setStatus(SipEntryServerNodeStatus::ENDED);
			$this->save();
		}
		else
		{
			parent::validateEntryServerNode();
		}
		return true;
	}
}