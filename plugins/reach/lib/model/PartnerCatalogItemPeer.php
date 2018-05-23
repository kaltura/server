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
	
	public static function retrieveByCatalogItemId($catalogItemId, $partnerId = null)
	{
		$criteria = new Criteria ( PartnerCatalogItemPeer::DATABASE_NAME );
		$criteria->add(PartnerCatalogItemPeer::CATALOG_ITEM_ID, $catalogItemId);
		if($partnerId)
			$criteria->add(PartnerCatalogItemPeer::PARTNER_ID, $partnerId);
		return PartnerCatalogItemPeer::doSelectOne($criteria);
	}
	
	public static function retrieveByCatalogItemIdNoFilter($catalogItemId, $partnerId)
	{
		$criteria = new Criteria ( PartnerCatalogItemPeer::DATABASE_NAME );
		$criteria->add(PartnerCatalogItemPeer::CATALOG_ITEM_ID, $catalogItemId);
		$criteria->add(PartnerCatalogItemPeer::PARTNER_ID, $partnerId);
		
		PartnerCatalogItemPeer::setUseCriteriaFilter(false);
		$result = PartnerCatalogItemPeer::doSelectOne($criteria);
		PartnerCatalogItemPeer::setUseCriteriaFilter(true);
		
		return $result;
	}
	
	public static function retrieveActiveCatalogItemIds($catalogItemIds, $partnerId)
	{
		$c = new Criteria(PartnerCatalogItemPeer::DATABASE_NAME);
		$c->add(PartnerCatalogItemPeer::CATALOG_ITEM_ID, $catalogItemIds, Criteria::IN);
		$c->add(PartnerCatalogItemPeer::PARTNER_ID, $partnerId);
		$c->add(PartnerCatalogItemPeer::STATUS, VendorCatalogItemStatus::ACTIVE);
		$c->addSelectColumn(PartnerCatalogItemPeer::CATALOG_ITEM_ID);
		
		$stmt = PartnerCatalogItemPeer::doSelectStmt($c, null);
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

} // PartnerCatalogItemPeer
