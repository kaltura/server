<?php


/**
 * Skeleton subclass for performing query and update operations on the 'vendor_integration' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.vendor
 * @subpackage model
 */
class VendorIntegrationPeer extends BaseVendorIntegrationPeer {

	const ZOOM_VENDOR_INTEGRATION = 'ZoomVendorIntegration';

	// cache classes by their type
	protected static $class_types_cache = array(
		VendorTypeEnum::ZOOM_ACCOUNT => self::ZOOM_VENDOR_INTEGRATION,
	);

	/**
	 * @param $row
	 * @param $colnum
	 * @return mixed
	 * @throws Exception
	 */
	public static function getOMClass($row, $colnum)
	{
		$vendorType = null;
		if ($row)
		{
			$typeField = self::translateFieldName(self::VENDOR_TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$vendorType = $row[$typeField];
			if (isset(self::$class_types_cache[$vendorType]))
			{
				return self::$class_types_cache[$vendorType];
			}
			$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $vendorType);
			if ($extendedCls)
			{
				self::$class_types_cache[$vendorType] = $extendedCls;
				return $extendedCls;
			}
		}
		throw new Exception("Can't instantiate un-typed [$vendorType] vendorService [" . print_r($row, true) . "]");
	}

	public static function setDefaultCriteriaFilter()
	{
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter();
		}

		$c = KalturaCriteria::create(VendorIntegrationPeer::OM_CLASS);
		$c->addAnd ( VendorIntegrationPeer::STATUS, VendorStatus::DELETED, Criteria::NOT_EQUAL);

		self::$s_criteria_filter->setFilter($c);
	}

	/**
	 * @param $accountID
	 * @param $vendorType
	 * @return VendorIntegration
	 * @throws PropelException
	 */
	public static function retrieveSingleVendorPerPartner($accountID, $vendorType)
	{
		$c = new Criteria();
		$c->add(VendorIntegrationPeer::ACCOUNT_ID, $accountID);
		$c->add(VendorIntegrationPeer::VENDOR_TYPE, $vendorType);
		return self::doSelectOne($c);
	}

	/**
	 * @param $accountID
	 * @param $vendorType
	 * @return VendorIntegration
	 * @throws PropelException
	 */
	public static function retrieveSingleVendorPerPartnerNoFilter($accountID, $vendorType)
	{
		$c = new Criteria();
		$c->add(VendorIntegrationPeer::ACCOUNT_ID, $accountID);
		$c->add(VendorIntegrationPeer::VENDOR_TYPE, $vendorType);
		self::setUseCriteriaFilter(false);
		$result = self::doSelectOne($c);
		self::setUseCriteriaFilter(true);
		return $result;
	}

	/**
	 * @param $partnerId
	 * @param $vendorType
	 * @return VendorIntegration
	 * @throws PropelException
	 */
	public static function retrieveSingleVendorByPartner($partnerId, $vendorType)
	{
		$c = new Criteria();
		$c->add(VendorIntegrationPeer::PARTNER_ID, $partnerId);
		$c->add(VendorIntegrationPeer::VENDOR_TYPE, $vendorType);
		return self::doSelectOne($c);
	}

} // VendorIntegrationPeer
