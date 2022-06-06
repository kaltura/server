<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
class LiveCaptionScheduleEvent extends BaseLiveStreamScheduleEvent {
	const MEDIA_URL = 'media_url';
	const MEDIA_KEY = 'media_key';
	const CAPTION_URL = 'caption_url';
	const CAPTION_TOKEN = 'caption_token';

	/**
	 * @param string $v
	 */
	public function setMediaUrl($v)
	{
		$this->putInCustomData(self::MEDIA_URL, $v);
	}

	/**
	 * @return string
	 */
	public function getMediaUrl()
	{
		return $this->getFromCustomData(self::MEDIA_URL);
	}

	/**
	 * @param string $v
	 */
	public function setMediaKey($v)
	{
		$this->putInCustomData(self::MEDIA_KEY, $v);
	}

	/**
	 * @return string
	 */
	public function getMediaKey()
	{
		return $this->getFromCustomData(self::MEDIA_KEY);
	}

	/**
	 * @param string $v
	 */
	public function setCaptionUrl($v)
	{
		$this->putInCustomData(self::CAPTION_URL, $v);
	}

	/**
	 * @return string
	 */
	public function getCaptionUrl()
	{
		return $this->getFromCustomData(self::CAPTION_URL);
	}

	/**
	 * @param string $v
	 */
	public function setCaptionToken($v)
	{
		$this->putInCustomData(self::CAPTION_TOKEN, $v);
	}

	/**
	 * @return string
	 */
	public function getCaptionToken()
	{
		return $this->getFromCustomData(self::CAPTION_TOKEN);
	}

	protected function addCapabilityToTemplateEntry($con)
	{
		$liveEntry = entryPeer::retrieveByPK($this->getTemplateEntryId());
		if ($liveEntry)
		{
			$liveEntry->addCapability(LiveEntry::LIVE_CAPTION_CAPABILITY);
			$liveEntry->save($con);
		}
	}

	/* (non-PHPdoc)
	 * @see ScheduleEvent::applyDefaultValues()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(ScheduleEventType::LIVE_CAPTION);
	}
}
