<?php


/**
 * Skeleton subclass for performing query and update operations on the 'user_entry' table.
 *
 * Describes the relationship between a specific user and a specific entry
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class UserEntryPeer extends BaseUserEntryPeer {

	// cache classes by their type
	protected static $class_types_cache = array(
	);

	public static function getOMClass($row, $colnum)
	{
		if($row)
		{
			$typeField = self::translateFieldName(UserEntryPeer::TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$userEntryType = $row[$typeField];
			if(isset(self::$class_types_cache[$userEntryType]))
				return self::$class_types_cache[$userEntryType];
			$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $userEntryType);
			if($extendedCls)
			{
				self::$class_types_cache[$userEntryType] = $extendedCls;
				return $extendedCls;
			}
			self::$class_types_cache[$userEntryType] = parent::OM_CLASS;
		}
		return parent::OM_CLASS;
	}

} // UserEntryPeer
