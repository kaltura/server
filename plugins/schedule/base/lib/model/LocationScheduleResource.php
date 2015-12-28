<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
class LocationScheduleResource extends ScheduleResource
{
	/* (non-PHPdoc)
	 * @see ScheduleResource::applyDefaultValues()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(ScheduleResourceType::LOCATION);
	}
}