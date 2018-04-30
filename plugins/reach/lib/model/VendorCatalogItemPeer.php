<?php


/**
 * Skeleton subclass for performing query and update operations on the 'vendor_catalog_item' table.
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
class VendorCatalogItemPeer extends BaseVendorCatalogItemPeer 
{
	const CAPTIONS_OM_CLASS = 'VendorCaptionsCatalogItem';
	const TRANSLATION_OM_CLASS = 'VendorTranslationCatalogItem';
	
	// cache classes by their type
	protected static $class_types_cache = array(
		VendorServiceFeature::TRANSLATION => self::TRANSLATION_OM_CLASS, 
		VendorServiceFeature::CAPTIONS => self::CAPTIONS_OM_CLASS,
	);
	
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
			self::$s_criteria_filter = new criteriaFilter ();
		
		$c = KalturaCriteria::create(VendorCatalogItemPeer::OM_CLASS);
		$c->addAnd ( VendorCatalogItemPeer::STATUS, VendorCatalogItemStatus::DELETED, Criteria::NOT_EQUAL);
		
		self::$s_criteria_filter->setFilter($c);
	}
	
	/**
	 * The returned Class will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @param      array $row PropelPDO result row.
	 * @param      int $colnum Column to examine for sOM class information (first is 0).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getOMClass($row, $colnum)
	{
		if($row)
		{
			$typeField = self::translateFieldName(VendorCatalogItemPeer::SERVICE_FEATURE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$catalogItemType = $row[$typeField];
			if(isset(self::$class_types_cache[$catalogItemType]))
				return self::$class_types_cache[$catalogItemType];
			
			$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $catalogItemType);
			if($extendedCls)
			{
				self::$class_types_cache[$catalogItemType] = $extendedCls;
				return $extendedCls;
			}
			self::$class_types_cache[$catalogItemType] = parent::OM_CLASS;
		}
		
		return parent::OM_CLASS;
	}
	
	public static function retrieveBySystemName ($systemName, $excludeId = null)
	{
		$criteria = new Criteria ( VendorCatalogItemPeer::DATABASE_NAME );
		$criteria->add ( VendorCatalogItemPeer::STATUS, array(VendorCatalogItemStatus::ACTIVE, VendorCatalogItemStatus::DEPRECATED), Criteria::IN);
		$criteria->add ( VendorCatalogItemPeer::SYSTEM_NAME, $systemName );
		if ($excludeId)
			$criteria->add( VendorCatalogItemPeer::ID, $excludeId, Criteria::NOT_EQUAL);
		
		return VendorCatalogItemPeer::doSelectOne($criteria);
	}
	
	public static function retrieveByPKNoFilter ($pk, $con = null)
	{
		self::setUseCriteriaFilter ( false );
		$res = parent::retrieveByPK( $pk , $con );
		self::setUseCriteriaFilter ( true );
		return $res;
	}

} // VendorCatalogItemPeer
