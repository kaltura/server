<?php


/**
 * Skeleton subclass for performing query and update operations on the 'entry_server_node' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class EntryServerNodePeer extends BaseEntryServerNodePeer {

	// cache classes by their type
	protected static $class_types_cache = array();

	/* (non-PHPdoc)
	 * @see BaseUserEntryPeer::setDefaultCriteriaFilter()
	 */
	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();
		$c = KalturaCriteria::create(EntryServerNodePeer::OM_CLASS);
		self::$s_criteria_filter->setFilter($c);
	}

	/**
	 * Function returns KalturaEntryServerNode sub-type according to protocol
	 * @var string $type
	 * @return KalturaEntryServerNode
	 *
	 */
	public static function getInstanceByType ($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$type = $sourceObject->getServerType();

		switch ($type)
		{
			case EntryServerNodeType::LIVE_BACKUP:
			case EntryServerNodeType::LIVE_PRIMARY:
				$object = new KalturaLiveEntryServerNode();
				break;

			default:
				KalturaLog::err("Did not expect source object to be of type ".$type);
		}

		if (!$object)
			return null;

		$object->fromObject($sourceObject, $responseProfile);
		return $object;
	}

	public static function getOMClass($row, $column)
	{
		$typeField = self::translateFieldName(EntryServerNodePeer::SERVER_TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
		$entryServerNodeServerType = $row[$typeField];
		if(isset(self::$class_types_cache[$entryServerNodeServerType]))
			return self::$class_types_cache[$entryServerNodeServerType];

		$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $entryServerNodeServerType);
		if($extendedCls)
		{
			self::$class_types_cache[$entryServerNodeServerType] = $extendedCls;
			return $extendedCls;
		}

		switch($entryServerNodeServerType)
		{
			case KalturaEntryServerNodeType::LIVE_BACKUP:
			case KalturaEntryServerNodeType::LIVE_PRIMARY:
				self::$class_types_cache[$entryServerNodeServerType] = LiveEntryServerNode::OM_CLASS;
				break;
			default:
				self::$class_types_cache[$entryServerNodeServerType] = parent::OM_CLASS;
				break;
		}
		return self::$class_types_cache[$entryServerNodeServerType];
	}

} // EntryServerNodePeer
