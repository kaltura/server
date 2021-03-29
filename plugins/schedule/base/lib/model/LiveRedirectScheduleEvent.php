<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
class LiveRedirectScheduleEvent extends BaseLiveStreamScheduleEvent implements
	ILiveStreamScheduleEvent
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

	public function getAffectedProperty()
	{
		return array('redirectToVod');
	}
	public function decoratorExecute (LiveEntry $e)
	{
		foreach ($this->getAffectedProperty() as $prop)
		{
			$e->$prop = $this->getRedirectEntryId();
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