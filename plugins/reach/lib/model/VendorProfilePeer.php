<?php


/**
 * Skeleton subclass for performing query and update operations on the 'vendor_profile' table.
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
class VendorProfilePeer extends BaseVendorProfilePeer 
{
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
			self::$s_criteria_filter = new criteriaFilter ();
		
		$c = KalturaCriteria::create(VendorCatalogItemPeer::OM_CLASS);
		$c->addAnd ( VendorProfilePeer::STATUS, VendorCatalogItemStatus::DELETED, Criteria::NOT_EQUAL);
		
		self::$s_criteria_filter->setFilter($c);
	}
	
	public static function retrieveByPartnerId($partnerId)
	{
		$c = new Criteria();
		$c->add(VendorProfilePeer::PARTNER_ID, $partnerId);
		$c->add(VendorProfilePeer::STATUS, VendorCatalogItemStatus::ACTIVE);
		
		return VendorProfilePeer::doSelect($c);
	}
	
	public static function updateUsedCredit($vendorProfileId, $value)
	{
		$connection = Propel::getConnection();
		
		$updateSql = "UPDATE ".VendorProfilePeer::TABLE_NAME." SET " . 
			VendorProfilePeer::USED_CREDIT . " = " . VendorProfilePeer::USED_CREDIT . "$value WHERE " .
			VendorProfilePeer::ID . "=" . $vendorProfileId . ";";
		
		$stmt = $connection->prepare($updateSql);
		$stmt->execute();
		KalturaLog::debug("Successfully saved vendor profile [$vendorProfileId]");
	}
	
} // VendorProfilePeer
