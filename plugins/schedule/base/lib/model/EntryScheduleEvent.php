<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
abstract class EntryScheduleEvent extends ScheduleEvent
{
	const CUSTOM_DATA_FIELD_ENTRY_IDS = 'entry_ids';
	const CUSTOM_DATA_FIELD_CATEGORY_IDS = 'category_ids';
	
	/**
	 * @param string $v
	 */
	public function setEntryIds($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_ENTRY_IDS, $v);
	}
	
	/**
	 * @return string
	 */
	public function getEntryIds()
	{
		$this->getFromCustomData(self::CUSTOM_DATA_FIELD_ENTRY_IDS);
	}
	
	/**
	 * @param string $v
	 */
	public function setCategoryIds($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_CATEGORY_IDS, $v);
	}
	
	/**
	 * @return string
	 */
	public function getCategoryIds()
	{
		$this->getFromCustomData(self::CUSTOM_DATA_FIELD_CATEGORY_IDS);
	}
}