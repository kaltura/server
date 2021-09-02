<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
abstract class BaseLiveStreamScheduleEvent extends EntryScheduleEvent
{
	public function postInsert(PropelPDO $con = null)
	{
		parent::postInsert($con);
		$this->addCapabilityToTemplateEntry($con);
	}
	
	public function postUpdate(PropelPDO $con = null)
	{
		parent::postUpdate($con);
		$this->addCapabilityToTemplateEntry($con);
	}

}