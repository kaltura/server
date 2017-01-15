<?php
/**
 * @package Core
 * @subpackage model
 */
class LiveChannel extends LiveEntry
{
	const CUSTOM_DATA_LIVE_CHANNEL_STATUS = 'live_channel_status';

	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(entryType::LIVE_CHANNEL);
	}
	
	public function updateStatus()
	{
		if($this->getStatus() == entryStatus::READY)
			return;
			
		if($this->getPlaylistId() || LiveChannelSegmentPeer::countByChannelId($this->getId()))
		{
			$this->setStatus(entryStatus::READY);
		}
		else
		{
			$this->setStatus(entryStatus::NO_CONTENT);	
		}
	}

	public function preSave(PropelPDO $con = null)
	{
		$this->updateStatus();
			
		return parent::preSave($con);
	}
	
	/**
	 * @param string $playlistId
	 */
	public function setPlaylistId($playlistId)
	{
		$this->putInCustomData('playlist_id', $playlistId);
	}
	
	/**
	 * @return string
	 */
	public function getPlaylistId()
	{
		return $this->getFromCustomData('playlist_id');
	}
	
	/**
	 * @param boolean $repeat
	 */
	public function setRepeat($repeat)
	{
		$this->putInCustomData('repeat', $repeat);
	}
	
	/**
	 * @return boolean
	 */
	public function getRepeat()
	{
		return $this->getFromCustomData('repeat');
	}

	public function setLiveChannelStatus($v)
	{
		return $this->putInCustomData(self::CUSTOM_DATA_LIVE_CHANNEL_STATUS, $v);
	}

	public function getLiveChannelStatus()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_LIVE_CHANNEL_STATUS);
	}
}
