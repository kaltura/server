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
	
	public static function addPartnerToCriteria($partnerId, $privatePartnerData = false, $partnerGroup = null, $kalturaNetwork = null)
	{
		$criteriaFilter = self::getCriteriaFilter();
		$criteria = $criteriaFilter->getFilter();
		
		if(empty($partnerGroup) && empty($kalturaNetwork))
		{
			// the default case
			$criteria->addAnd(self::PARTNER_ID, $partnerId);
		}
		elseif ($partnerGroup == myPartnerUtils::ALL_PARTNERS_WILD_CHAR)
		{
			// all is allowed - don't add anything to the criteria
		}
		else
		{
			$criterion = null;
			if($partnerGroup)
			{
				// $partnerGroup hold a list of partners separated by ',' or $kalturaNetwork is not empty (should be mySearchUtils::KALTURA_NETWORK = 'kn')
				$partners = explode(',', trim($partnerGroup));
				$hasPartnerZero = false;
				foreach($partners as $index => &$p)
				{
					trim($p); // make sure there are not leading or trailing spaces
					if($p == 0)
					{
						unset($partners[$index]);
						$hasPartnerZero = true;
					}
				}
				
				// add the partner_id to the partner_group
				$partners[] = strval($partnerId);
				$partners = array_unique($partners);
				
				$criterion = $criteria->getNewCriterion(self::PARTNER_ID, $partners, Criteria::IN);
				
				if($hasPartnerZero)
				{
					$defaultAndGlobal = $criteria->getNewCriterion(self::PARTNER_ID, 0);
					$defaultAndGlobal->addAnd($criteria->getNewCriterion(self::IS_DEFAULT, 1));
					$criterion->addOr($defaultAndGlobal);
				}
			}
			else
			{
				$criterion = $criteria->getNewCriterion(self::PARTNER_ID, $partnerId);
			}
			
			$criteria->addAnd($criterion);
		}
		
		$criteriaFilter->enable();
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
	
	public static function retrieveBySystemName ($systemName, $excludeId = null, $partnerIds = null, PropelPDO $con = null)
	{
		$criteria = new Criteria ( VendorCatalogItemPeer::DATABASE_NAME );
		$criteria->add ( VendorCatalogItemPeer::STATUS, VendorCatalogItemStatus::ACTIVE );
		$criteria->add ( VendorCatalogItemPeer::SYSTEM_NAME, $systemName );
		if ($excludeId)
			$criteria->add( VendorCatalogItemPeer::ID, $excludeId, Criteria::NOT_EQUAL);
		
		// use the partner ids list if given
		if (!$partnerIds)
		{
			$partnerIds = array (kCurrentContext::getCurrentPartnerId());
		}
		
		$criteria->add(VendorCatalogItemPeer::PARTNER_ID, $partnerIds, Criteria::IN);
		
		$criteria->addDescendingOrderByColumn(VendorCatalogItemPeer::PARTNER_ID);
		
		return VendorCatalogItemPeer::doSelectOne($criteria);
	}

} // VendorCatalogItemPeer
