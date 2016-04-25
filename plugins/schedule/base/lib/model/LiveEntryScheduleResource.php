<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
class LiveEntryScheduleResource extends ScheduleResource
{
	const CUSTOM_DATA_FIELD_ENTRY_ID = 'entry_id';

	/* (non-PHPdoc)
	 * @see ScheduleResource::applyDefaultValues()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(ScheduleResourceType::LIVE_ENTRY);
	}
	
	/**
	 * @param string $v
	 */
	public function setEntryId($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_ENTRY_ID, $v);
	}
	
	/**
	 * @return string
	 */
	public function getEntryId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_ENTRY_ID);
	}
}