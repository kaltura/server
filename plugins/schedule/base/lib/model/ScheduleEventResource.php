<?php


/**
 * Skeleton subclass for representing a row from the 'schedule_event_resource' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.schedule
 * @subpackage model
 */
class ScheduleEventResource extends BaseScheduleEventResource implements IRelatedObject {

	/* (non-PHPdoc)
	 * @see BaseScheduleEvent::preInsert()
	 */
	public function preInsert(PropelPDO $con = null)
	{
		$this->setPartnerId(kCurrentContext::getCurrentPartnerId());
    	
		return parent::preInsert($con);
	}
	
} // ScheduleEventResource
