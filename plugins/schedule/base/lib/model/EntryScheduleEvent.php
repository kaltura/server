<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
abstract class EntryScheduleEvent extends ScheduleEvent
{
	const CUSTOM_DATA_FIELD_ENTRY_ID = 'entry_id';
	const CUSTOM_DATA_FIELD_ENTRY = 'entry';
	
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
		$this->getFromCustomData(self::CUSTOM_DATA_FIELD_ENTRY_ID);
	}
	
	/**
	 * @param entry $v
	 */
	public function setEntry(entry $v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_ENTRY, $v);
	}
	
	/**
	 * @return entry
	 */
	public function getEntry()
	{
		$this->getFromCustomData(self::CUSTOM_DATA_FIELD_ENTRY);
	}
}