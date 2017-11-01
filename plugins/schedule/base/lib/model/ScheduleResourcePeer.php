<?php


/**
 * Skeleton subclass for performing query and update operations on the 'schedule_resource' table.
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
class ScheduleResourcePeer extends BaseScheduleResourcePeer implements IRelatedObjectPeer {

	const CAMERA_OM_CLASS = 'CameraScheduleResource';
	const LIVE_ENTRY_OM_CLASS = 'LiveEntryScheduleResource';
	const LOCATION_OM_CLASS = 'LocationScheduleResource';
	
	
	// cache classes by their type
	protected static $class_types_cache = array(
		ScheduleResourceType::CAMERA => self::CAMERA_OM_CLASS,
		ScheduleResourceType::LIVE_ENTRY => self::LIVE_ENTRY_OM_CLASS,
		ScheduleResourceType::LOCATION => self::LOCATION_OM_CLASS,
	);
	
	/*
	 * (non-PHPdoc)
	 * @see BaseScheduleResourcePeer::setDefaultCriteriaFilter()
	 */
	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new Criteria();
		$c->addAnd(ScheduleResourcePeer::STATUS, ScheduleResourceStatus::DELETED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter($c);
	}
	
	/**
	 * The returned Class will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @param      array $row PropelPDO result row.
	 * @param      int $colnum Column to examine for OM class information (first is 0).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getOMClass($row, $colnum)
	{
		if($row)
		{
			$typeField = self::translateFieldName(ScheduleResourcePeer::TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$assetType = $row[$typeField];
			if(isset(self::$class_types_cache[$assetType]))
				return self::$class_types_cache[$assetType];
				
			$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $assetType);
			if($extendedCls)
			{
				self::$class_types_cache[$assetType] = $extendedCls;
				return $extendedCls;
			}
			self::$class_types_cache[$assetType] = parent::OM_CLASS;
		}
			
		return parent::OM_CLASS;
	}
	
	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::getRootObjects()
	 */
	public function getRootObjects(IRelatedObject $object)
	{
		/* @var $object ScheduleResource */
		
		$roots = array();
		if($object->getParentId())
		{
			$parent = ScheduleResourcePeer::retrieveByPK($object->getParentId());
			if($parent)
				$roots[] = $parent;
		}
			
		return $roots;
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::isReferenced()
	 */
	public function isReferenced(IRelatedObject $object)
	{
		return false;
	}
	public static function getCacheInvalidationKeys()
	{
		return array(array("scheduleResource:id=%s", self::ID));		
	}
} // ScheduleResourcePeer
