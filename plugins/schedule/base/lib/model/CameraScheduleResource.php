<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
class CameraScheduleResource extends ScheduleResource
{
	const CUSTOM_DATA_FIELD_STREAM_URL = 'streamUrl';

	/* (non-PHPdoc)
	 * @see ScheduleResource::applyDefaultValues()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(ScheduleResourceType::CAMERA);
	}
	
	/**
	 * @param string $v
	 */
	public function setStreamUrl($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_STREAM_URL, $v);
	}
	
	/**
	 * @return string
	 */
	public function getStreamUrl()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_STREAM_URL);
	}
}