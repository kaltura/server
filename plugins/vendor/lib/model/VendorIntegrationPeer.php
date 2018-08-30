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
 * @package plugins.reach
 * @subpackage model
 */
class VendorIntegrationPeer extends BaseVendorIntegrationPeer {


	/**
	 * @param $accountID
	 * @param $vendorType
	 * @param $partnerId
	 * @return VendorIntegration
	 * @throws PropelException
	 */
	public static function retrieveSingleVendorPerPartner($accountID, $vendorType, $partnerId)
	{
		$c = new Criteria();
		$c->add(VendorIntegrationPeer::ACCOUNT_ID, $accountID);
		$c->add(VendorIntegrationPeer::VENDOR_TYPE, $vendorType);
		$c->add(VendorIntegrationPeer::PARTNER_ID , $partnerId);
		return self::doSelectOne($c);
	}

	/**
	 * @param $accountID
	 * @param $vendorType
	 * @return VendorIntegration
	 * @throws PropelException
	 */
	public static function retrieveSingleVendor($accountID, $vendorType)
	{
		$c = new Criteria();
		$c->add(VendorIntegrationPeer::ACCOUNT_ID, $accountID);
		$c->add(VendorIntegrationPeer::VENDOR_TYPE, $vendorType);
		return self::doSelectOne($c);
	}

} // VendorIntegrationPeer
