<?php


/**
 * Skeleton subclass for performing query and update operations on the 'partner_catalog_item' table.
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
class PartnerCatalogItemPeer extends BasePartnerCatalogItemPeer {
	
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
			self::$s_criteria_filter = new criteriaFilter ();
		
		$c = KalturaCriteria::create(PartnerCatalogItemPeer::OM_CLASS);
		$c->addAnd ( PartnerCatalogItemPeer::STATUS, VendorCatalogItemStatus::DELETED, Criteria::NOT_EQUAL);
		
		self::$s_criteria_filter->setFilter($c);
	}
	
	public static function getIds(Criteria $criteria, $con = null)
	{
		$result = array();
		$partnerCatalogItems = PartnerCatalogItemPeer::doSelect($criteria, $con);
		foreach ($partnerCatalogItems as $item)
		{
			/* @var $item PartnerCatalogItem */
			$result[] = $item->getCatalogItemId();
		}
		
		return $result;
	}
	
	public static function retrieveByCatalogItemId($catalogItemId, $partnerId)
	{
		$criteria = new Criteria ( PartnerCatalogItemPeer::DATABASE_NAME );
		$criteria->add(PartnerCatalogItemPeer::CATALOG_ITEM_ID, $catalogItemId);
		$criteria->add(PartnerCatalogItemPeer::PARTNER_ID, $partnerId);
		return PartnerCatalogItemPeer::doSelectOne($criteria);
	}

} // PartnerCatalogItemPeer
