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

	const CUSTOM_DATA_FIELD_FULL_PARENT_IDS = 'full_parent_ids';
	
	/* (non-PHPdoc)
	 * @see BaseScheduleEvent::preInsert()
	 */
	public function preInsert(PropelPDO $con = null)
	{
		$this->setPartnerId(kCurrentContext::getCurrentPartnerId());
    	
		return parent::preInsert($con);
	}

	/* (non-PHPdoc)
	 * @see BaseScheduleEvent::postSave()
	 */
	public function postSave(PropelPDO $con = null)
	{
		parent::postSave($con);
	}

	/* (non-PHPdoc)
	 * @see BaseScheduleEvent::postDelete()
	 */
	public function postDelete(PropelPDO $con = null)
	{
		parent::postDelete($con);
	}
	
	/**
	 * {@inheritDoc}
	 * @see BaseScheduleEventResource::setEventId()
	 */
	public function setResourceId($v)
	{
		if($v)
		{
			$fullParentIds = array();
			$resource = ScheduleResourcePeer::retrieveByPK($v);
			if($resource)
			{
				$fullParentIds = $resource->getFullParentIds();
			}
			
			$this->setFullParentIds($fullParentIds);
		}
		return parent::setResourceId($v);
	}
	
	/**
	 * @param array $v
	 */
	protected function setFullParentIds(array $v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_FULL_PARENT_IDS, $v);
	}
	
	/**
	 * @return array
	 */
	public function getFullParentIds()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_FULL_PARENT_IDS, null, array());
	}
	
	public function getCacheInvalidationKeys()
	{
		return array("scheduleEventResource:eventId=".strtolower($this->getEventId()));
	}
} // ScheduleEventResource
