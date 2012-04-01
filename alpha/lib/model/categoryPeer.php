<?php
/**
 * Subclass for performing query and update operations on the 'category' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class categoryPeer extends BasecategoryPeer
{
	const CATEGORY_SEPARATOR = ">";
	
	const MAX_CATEGORY_NAME = 60;
	const MEMBERS = 'category.MEMBERS';
	
	
	private static $invalid_characters = array('>','<',',');
	
	private static $replace_character = "_";
	
	
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}

		$c = KalturaCriteria::create(categoryPeer::OM_CLASS); 
		$c->add ( self::STATUS, CategoryStatus::DELETED, Criteria::NOT_EQUAL );	
		
		if (kEntitlementUtils::getEntitlementEnforcement())
		{
			$crit = $c->getNewCriterion ( self::DISPLAY_IN_SEARCH, DisplayInSearchType::PARTNER_ONLY, Criteria::EQUAL );
			$crit->addTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);

			$ksString = kCurrentContext::$ks ? kCurrentContext::$ks : '';
			if($ksString <> '')
				$kuser = kuserPeer::getActiveKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, kCurrentContext::$ks_uid);

			if($kuser)
			{
				$membersCrit = $c->getNewCriterion ( self::MEMBERS , $kuser->getId(), Criteria::EQUAL);
				$membersCrit->addTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
				$crit->addOr($membersCrit);
			}
				
			$c->addAnd ( $crit );
		}
		
		self::$s_criteria_filter->setFilter ( $c );
	}

	public static function retrieveByPKNoFilter ($pk, $con = null)
	{
		self::setUseCriteriaFilter ( false );
		$res = parent::retrieveByPK( $pk , $con );
		self::setUseCriteriaFilter ( true );
		return $res;
	}

	public static function retrieveByPKsNoFilter ($pks, $con = null)
	{
		self::setUseCriteriaFilter ( false );
		$res = parent::retrieveByPKs( $pks , $con );
		self::setUseCriteriaFilter ( true );
		return $res;
	}

	public static function getParsedName($v)
	{
		$v = substr($v, 0, self::MAX_CATEGORY_NAME);
		$v = str_replace(self::$invalid_characters, self::$replace_character, $v);
		
		return $v;
	}

	public static function getParsedFullName($v)
	{
		$names = explode(self::CATEGORY_SEPARATOR, $v);
		$finalNames = array();
		foreach($names as $name)
			$finalNames[] = self::getParsedName($name);
		
		return implode(self::CATEGORY_SEPARATOR, $finalNames);
	}
	
	/**
	 * Get category by full name using exact match (returns null or category object)
	 *  
	 * @param $partnerId
	 * @param $fullName
	 * @param $con
	 * @return category
	 */
	public static function getByFullNameExactMatch($fullName, $con = null)
	{
		$fullName = self::getParsedFullName($fullName);
		
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS); 
		$c->add(categoryPeer::FULL_NAME, $fullName);
		return categoryPeer::doSelectOne($c, $con);
	}
	
	/**
	 * Get categories by full name using full name wildcard match (returns an array)
	 *  
	 * @param $partnerId
	 * @param $fullName
	 * @param $con
	 * @return array
	 */
	public static function getByFullNameWildcardMatch($fullName, $con = null)
	{
		$fullName = str_replace(array('\\', '%', '_'), array('\\\\', '\%', '\_'), $fullName);
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS); 
		$c->add(categoryPeer::FULL_NAME, $fullName."%", Criteria::LIKE);
		
		return categoryPeer::doSelect($c, $con);
	}

	public static function getCacheInvalidationKeys()
	{
		return array(array("category:partnerId=%s", self::PARTNER_ID));		
	}
	
	/**
	 * @param Criteria $criteria
	 * @param PropelPDO $con
	 */
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		$c = clone $criteria;
		
		if($c instanceof KalturaCriteria)
		{
			$c->applyFilters();
			$criteria->setRecordsCount($c->getRecordsCount());
		}

		return parent::doSelect($c, $con);
	}
	
	/**
	 * @param Criteria $criteria
	 * @param PropelPDO $con
	 */
	public static function doSelectEntitledAndNonIndexedCategories($kuserId, $limit)
	{
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS);
		$c->addAnd(categoryPeer::MEMBERS, $kuserId, Criteria::EQUAL);
		$categoryGroupSize = category::MAX_NUMBER_OF_MEMBERS_TO_BE_INDEXED_ON_ENTRY;
		
		$partner = PartnerPeer::retrieveByPK(kCurrentContext::$ks_partner_id);
		if($partner && $partner->getCategoryGroupSize())
			$categoryGroupSize = $partner->getCategoryGroupSize();

		$membersCountCrit = $c->getNewCriterion (categoryPeer::MEMBERS_COUNT, $categoryGroupSize, Criteria::GREATER_THAN);
		$membersCountCrit->addOr($c->getNewCriterion (categoryPeer::ENTRIES_COUNT, 
										entry::CATEGORY_ENTRIES_COUNT_LIMIT_TO_BE_INDEXED, Criteria::GREATER_THAN));		
		$c->addAnd($membersCountCrit);
		
		
		$c->setLimit($limit);
		$c->addDescendingOrderByColumn(categoryPeer::UPDATED_AT);

		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$categories = self::doSelect($c);
		KalturaCriterion::enableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		
		return $categories;
	}
	
	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     category
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{
		if (null !== ($obj = categoryPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = KalturaCriteria::create(categoryPeer::OM_CLASS);
		$criteria->add(categoryPeer::ID, $pk);

		$v = categoryPeer::doSelect($criteria, $con);

		return !empty($v) > 0 ? $v[0] : null;
	}
	
}
