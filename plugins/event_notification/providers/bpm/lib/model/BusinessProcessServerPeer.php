<?php


/**
 * Skeleton subclass for performing query and update operations on the 'business_process_server' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.businessProcessNotification
 * @subpackage model
 */
class BusinessProcessServerPeer extends BaseBusinessProcessServerPeer {

	protected static $class_types_cache = array();
	
	public static function setDefaultCriteriaFilter ()
	{
		if(is_null(self::$s_criteria_filter))
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new Criteria(); 
		$c->add(self::STATUS, BusinessProcessServerStatus::DELETED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter($c);
	}
	
	public static function getOMClass($row, $colnum)
	{
		if($row)
		{
			$typeField = self::translateFieldName(BusinessProcessServerPeer::TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$type = $row[$typeField];
			if(isset(self::$class_types_cache[$type]))
				return self::$class_types_cache[$type];
				
			$extendedCls = KalturaPluginManager::getObjectClass('BusinessProcessServer', $type);
			if($extendedCls)
			{
				self::$class_types_cache[$type] = $extendedCls;
				return $extendedCls;
			}
		}
			
		return null;
	}
	
} // BusinessProcessServerPeer
