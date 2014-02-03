<?php


/**
 *
 * @package Core
 * @subpackage model
 */
class DeliveryPeer extends BaseDeliveryPeer {
	
	// cache classes by their type
	protected static $class_types_cache = array(
			DeliveryType::AKAMAI_HTTP => 'DeliveryAkamaiHttp',
			DeliveryType::AKAMAI_RTMP => 'DeliveryRtmp',
			DeliveryType::AKAMAI_RTSP => 'DeliveryAkamaiRtsp',
	);
	
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
			$typeField = self::translateFieldName(DeliveryPeer::TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$deliveryType = $row[$typeField];
			if(isset(self::$class_types_cache[$deliveryType]))
				return self::$class_types_cache[$deliveryType];
	
			$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $deliveryType);
			if($extendedCls)
			{
				self::$class_types_cache[$deliveryType] = $extendedCls;
				return $extendedCls;
			}
			self::$class_types_cache[$deliveryType] = parent::OM_CLASS;
		}
			
		return parent::OM_CLASS;
	}
	

} // DeliveryPeer
