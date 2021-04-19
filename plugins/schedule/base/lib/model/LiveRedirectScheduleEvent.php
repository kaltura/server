<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
class LiveRedirectScheduleEvent extends BaseLiveStreamScheduleEvent
{
	const REDIRECT_ENTRY_ID = 'redirect_entry_id';
	
	public function getRedirectEntryId ()
	{
		return $this->getFromCustomData(self::REDIRECT_ENTRY_ID);
	}
	public function setRedirectEntryId ($v)
	{
		$this->putInCustomData(self::REDIRECT_ENTRY_ID,$v);
	}

	public function decoratorExecute ($entry)
	{
		if($entry instanceof LiveEntry)
		{
			$entry -> setRedirectEntryId($this -> getRedirectEntryId());
			$entry -> setRecordedEntryId($this -> getRedirectEntryId());
			return EntryServerNodeStatus::STOPPED;
		}
	}
	
	/* (non-PHPdoc)
	 * @see ScheduleEvent::applyDefaultValues()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(ScheduleEventType::LIVE_REDIRECT);
	}
}