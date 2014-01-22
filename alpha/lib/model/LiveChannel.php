<?php
/**
 * @package Core
 * @subpackage model
 */
class LiveChannel extends LiveEntry
{
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(EntryType::LIVE_CHANNEL);
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
}
