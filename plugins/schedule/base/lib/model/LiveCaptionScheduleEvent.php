<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
class LiveCaptionScheduleEvent extends BaseLiveStreamScheduleEvent
{
	const MEDIA_STREAM_URL = 'media_stream_url';
	const CAPTION_STREAM_URL = 'caption_stream_url'; // TODO: we probably need to add more fields (like tokens etc...)

	/**
	 * @param string $v
	 */
	public function setMediaStreamUrl($v)
	{
		$this->putInCustomData(self::MEDIA_STREAM_URL, $v);
	}

	/**
	 * @param string $v
	 */
	public function setCaptionStreamUrl($v)
	{
		$this->putInCustomData(self::CAPTION_STREAM_URL, $v);
	}

	/**
	 * @return string
	 */
	public function getMediaStreamUrl()
	{
		return $this->getFromCustomData(self::MEDIA_STREAM_URL);
	}

	/**
	 * @return string
	 */
	public function getCaptionStreamUrl()
	{
		return $this->getFromCustomData(self::CAPTION_STREAM_URL);
	}
	
	protected function addCapabilityToTemplateEntry($con)
	{
		$liveEntry = entryPeer::retrieveByPK($this->getTemplateEntryId());
		if ($liveEntry)
		{
			$liveEntry->addCapability('LIVE_CAPTION_CAPABILITY'); // TODO, SAVE ENTRY
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
