<?php


/**
 * Skeleton subclass for representing a row from the 'schedule_resource' table.
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
class ScheduleResource extends BaseScheduleResource implements IRelatedObject {

	const CUSTOM_DATA_FIELD_FULL_PARENT_IDS = 'full_parent_ids';
	
	public function __construct() 
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or equivalent initialization method).
	 * @see __construct()
	 */
	public function applyDefaultValues()
	{
	}
	
	/* (non-PHPdoc)
	 * @see BaseScheduleEvent::preInsert()
	 */
	public function preInsert(PropelPDO $con = null)
	{
		$this->setStatus(ScheduleResourceStatus::ACTIVE);
		$this->setPartnerId(kCurrentContext::getCurrentPartnerId());
    	
		return parent::preInsert($con);
	}
	
	/**
	 * {@inheritDoc}
	 * @see BaseScheduleResource::setParentId()
	 */
	public function setParentId($v)
	{
		if($v)
		{
			$fullParentIds = array();
			$parent = ScheduleResourcePeer::retrieveByPK($v);
			if($parent)
			{
				$fullParentIds = $parent->getFullParentIds();
			}
			
			$fullParentIds[] = $v;
			$this->setFullParentIds($fullParentIds);
		}
		return parent::setParentId($v);
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
		return array("scheduleResource:id=".strtolower($this->getId()));
	}
} // ScheduleResource
