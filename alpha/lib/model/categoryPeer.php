<?php
/**
 * Subclass for performing query and update operations on the 'category' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class categoryPeer extends BasecategoryPeer implements IRelatedObjectPeer
{
	const CATEGORY_SEPARATOR = ">";
	
	const MAX_CATEGORY_NAME = 200;
	
	const MEMBERS = 'category.MEMBERS';
	const CATEGORY_ID = 'category.CATEGORY_ID';
	
	private static $invalid_characters = array('>','<',',');
	
	private static $replace_character = "_";
	
	private static $ignoreDeleted = false;
	
	public static function setIgnoreDeleted ($ignore)
	{
		self::$ignoreDeleted = $ignore;
	}
	
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}

		$c = KalturaCriteria::create(categoryPeer::OM_CLASS); 
		
		$partnerId = kCurrentContext::$ks_partner_id ? kCurrentContext::$ks_partner_id : kCurrentContext::$partner_id; 			
		
		if($partnerId != Partner::BATCH_PARTNER_ID || self::$ignoreDeleted)
		{
			$c->add ( self::STATUS, array(CategoryStatus::DELETED, CategoryStatus::PURGED), Criteria::NOT_IN );
		}
		else
		{
			$c->add ( self::STATUS, CategoryStatus::PURGED, Criteria::NOT_EQUAL );
		}
		
		if (kEntitlementUtils::getEntitlementEnforcement())
		{
			//add context as filter
			$privacyContextCrit = $c->getNewCriterion(self::PRIVACY_CONTEXTS, kEntitlementUtils::getKsPrivacyContext(), KalturaCriteria::IN_LIKE);
			$privacyContextCrit->addTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
			$c->addAnd($privacyContextCrit);
			
			$crit = $c->getNewCriterion ( self::DISPLAY_IN_SEARCH, DisplayInSearchType::PARTNER_ONLY, Criteria::EQUAL);
			$crit->addTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);

			$kuser = null;
			$ksString = kCurrentContext::$ks ? kCurrentContext::$ks : '';
			if($ksString <> '')
				$kuser = kCurrentContext::getCurrentKsKuser();

			if($kuser)
			{
				// get the groups that the user belongs to in case she is not associated to the category directly
				$kgroupIds = KuserKgroupPeer::retrieveKgroupIdsByKuserId($kuser->getId());
				$kgroupIds[] = $kuser->getId();
				$membersCrit = $c->getNewCriterion ( self::MEMBERS , $kgroupIds, KalturaCriteria::IN_LIKE);
				$membersCrit->addTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
     			$crit->addOr($membersCrit);
			}
				
			$c->addAnd ( $crit );
		}
		
		self::$s_criteria_filter->setFilter ( $c );
	}
	
	/* (non-PHPdoc)
	 * @see BasecategoryPeer::addPartnerToCriteria()
	 * 
	 * Override parent implementation in order to add tag to pertner id criterion in order to be able to disable it later 
	 */
	public static function addPartnerToCriteria($partnerId, $privatePartnerData = false, $partnerGroup = null, $kalturaNetwork = null)
	{
		$criteriaFilter = self::getCriteriaFilter();
		$criteria = $criteriaFilter->getFilter();
		
		if(!$privatePartnerData)
		{
			// the private partner data is not allowed - 
			if($kalturaNetwork)
			{
				// allow only the kaltura network stuff
				$criteria->addAnd(self::DISPLAY_IN_SEARCH , mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK);
				
				if($partnerId)
				{
					$orderBy = "(" . self::PARTNER_ID . "<>{$partnerId})";  // first take the pattner_id and then the rest
					myCriteria::addComment($criteria , "Only Kaltura Network");
					$criteria->addAscendingOrderByColumn($orderBy);//, Criteria::CUSTOM );
				}
			}
			else
			{
				// no private data and no kaltura_network - 
				// add a criteria that will return nothing
				$criteria->addAnd(self::PARTNER_ID, Partner::PARTNER_THAT_DOWS_NOT_EXIST);
			}
		}
		else
		{
			$criterion = null;
			
			// private data is allowed
			if(!strlen(strval($partnerGroup)))
			{
				// the default case
				$criterion = $criteria->getNewCriterion(self::PARTNER_ID, $partnerId);
				$criteria->addAnd($criterion);
			}
			elseif ($partnerGroup == myPartnerUtils::ALL_PARTNERS_WILD_CHAR)
			{
				// all is allowed - don't add anything to the criteria
			}
			else 
			{
				// $partnerGroup hold a list of partners separated by ',' or $kalturaNetwork is not empty (should be mySearchUtils::KALTURA_NETWORK = 'kn')
				$partners = explode(',', trim($partnerGroup));
				foreach($partners as &$p)
					trim($p); // make sure there are not leading or trailing spaces

				// add the partner_id to the partner_group
				if (!in_array(strval($partnerId), $partners))
					$partners[] = strval($partnerId);
				
				if(count($partners) == 1 && reset($partners) == $partnerId)
				{
					$criterion = $criteria->getNewCriterion(self::PARTNER_ID, $partnerId);
					$criteria->addAnd($criterion);
				}
				else 
				{
					$criterion = $criteria->getNewCriterion(self::PARTNER_ID, $partners, Criteria::IN);
					if($kalturaNetwork)
					{
						$criterionNetwork = $criteria->getNewCriterion(self::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK);
						$criterion->addOr($criterionNetwork);
					}
					$criteria->addAnd($criterion);
				}
			}
			
			if($criterion && $criterion instanceof KalturaCriterion)
				$criterion->addTag(KalturaCriterion::TAG_PARTNER_SESSION);
		}
			
		$criteriaFilter->enable();
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
	 * @param $fullName
	 * @param $ignoreCategoryId
	 * @param $partnerId
	 * @return category
	 */
	public static function getByFullNameExactMatch($fullName, $ignoreCategoryId = null, $partnerId = null)
	{
		$fullName = self::getParsedFullName($fullName);
		
		if (trim($fullName) == '')
			return null;
		
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS); 
		$c->add(categoryPeer::FULL_NAME, $fullName);
		
		if($ignoreCategoryId)
			$c->add(categoryPeer::ID, $ignoreCategoryId, Criteria::NOT_EQUAL);
		
		$tagDisabled = false;
		if(!is_null($partnerId))
		{
			$tagDisabled = true;
			KalturaCriterion::disableTag(KalturaCriterion::TAG_PARTNER_SESSION);
			$c->add(categoryPeer::PARTNER_ID, $partnerId);
		}
		
		$ret = categoryPeer::doSelectOne($c);
		
		if ($tagDisabled)
		    KalturaCriterion::restoreTag(KalturaCriterion::TAG_PARTNER_SESSION);
		    
		return $ret;
	}
	
	/**
	 * Get categories by full name using exact match (returns null or category object)
	 *  
	 * @param $fullNames
	 * @return category
	 */
	public static function getByFullNamesExactMatch($fullNames)
	{
		$fullNameParsed = array();
		foreach ($fullNames as $fullName)
		{
			$fullName = self::getParsedFullName($fullName);
			if (trim($fullName) == '')
				continue;
				
			$fullNameParsed[] = $fullName;
		}
		
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS); 
		$c->add(categoryPeer::FULL_NAME, $fullNameParsed, KalturaCriteria::IN);

		return categoryPeer::doSelect($c);
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
		if (trim($fullName) == '')
			return null;
			
//		$fullName = str_replace(array('\\', '%', '_'), array('\\\\', '\%', '\_'), $fullName);
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS); 
		$c->add(categoryPeer::FULL_NAME, "$fullName\\*", Criteria::LIKE);
		
		return categoryPeer::doSelect($c, $con);
	}
	
	/**
	 * Get categories by id using full ids wildcard match (returns an array)
	 *  
	 * @param $id
	 * @param $con
	 * @return array
	 */
	public static function getByFullIdsWildcardMatchForCategoryId($id, $con = null)
	{
		if (trim($id) == '')
			return null;
			
		$category = categoryPeer::retrieveByPK($id);
		if(!$category)
			return null;
		
		$fullIds = $category->getFullIds();
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS); 
		$c->add(categoryPeer::FULL_IDS, "$fullIds\\*", Criteria::LIKE);

		return categoryPeer::doSelect($c, $con);
	}
	
	/**
	 * Get categories by full name using full ids wildcard match (returns an array)
	 *  
	 * @param $partnerId
	 * @param $fullName
	 * @param $con
	 * @return array
	 */
	public static function getByFullIdsWildcardMatch($fullIds, $con = null)
	{
		if (trim($fullIds) == '')
			return null;
		
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS); 
		$c->add(categoryPeer::FULL_IDS, "$fullIds\\*", Criteria::LIKE);

		return categoryPeer::doSelect($c, $con);
	}

	public static function getCacheInvalidationKeys()
	{
		return array(array("category:id=%s", self::ID), array("category:partnerId=%s", self::PARTNER_ID));		
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
	 * Return all categories kuser is entitled to view the content.
	 * (User may call category->get to view a category - but not to view its content)
	 * 
	 * @param int $kuserId
	 * @param int $limit
	 * @return array<category>
	 */
	public static function retrieveEntitledAndNonIndexedByKuser($kuserId, $limit)
	{
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
		$partner = PartnerPeer::retrieveByPK($partnerId);
		
		$categoryGroupSize = kConf::get('max_number_of_memebrs_to_be_indexed_on_entry');
		if($partner && $partner->getCategoryGroupSize())
			$categoryGroupSize = $partner->getCategoryGroupSize();

		$c = KalturaCriteria::create(categoryPeer::OM_CLASS);
		
		$filteredCategoriesIds = entryPeer::getFilterdCategoriesIds();
		
		if(count($filteredCategoriesIds))
			$c->addAnd(categoryPeer::ID, $filteredCategoriesIds, Criteria::IN);
			
		$membersCountCrit = $c->getNewCriterion (categoryPeer::MEMBERS_COUNT, $categoryGroupSize, Criteria::GREATER_THAN);
		$membersCountCrit->addOr($c->getNewCriterion (categoryPeer::ENTRIES_COUNT, 
										kConf::get('category_entries_count_limit_to_be_indexed'), Criteria::GREATER_THAN));		
		$c->addAnd($membersCountCrit);

		$c->setLimit($limit);
		$c->addDescendingOrderByColumn(categoryPeer::UPDATED_AT);
		
		//all fields needed from default criteria
		//here we cannot use the default criteria, as we need to get all categories user is entitled to view the content.
		
		//not deleted or purged
		$c->add ( self::STATUS, array(CategoryStatus::DELETED, CategoryStatus::PURGED), Criteria::NOT_IN );
		$c->add(self::PARTNER_ID, $partnerId, Criteria::EQUAL);

		//add privacy context
		$privacyContextCrit = $c->getNewCriterion(self::PRIVACY_CONTEXTS, kEntitlementUtils::getKsPrivacyContext(), KalturaCriteria::IN_LIKE);
		$privacyContextCrit->addTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$c->addAnd($privacyContextCrit);

		//set privacy by ks and type

		//user is entitled to view all cantent that belong to categoires he is a membr of
		$kuser = null;
		$ksString = kCurrentContext::$ks ? kCurrentContext::$ks : '';
		if($ksString <> '')
			$kuser = kCurrentContext::getCurrentKsKuser();


		if($kuser)
		{
			// get the groups that the user belongs to in case she is not associated to the category directly
			$kgroupIds = KuserKgroupPeer::retrieveKgroupIdsByKuserId($kuser->getId());
			$kgroupIds[] = $kuser->getId();
			$membersCrit = $c->getNewCriterion ( self::MEMBERS , $kgroupIds, KalturaCriteria::IN_LIKE);
			$membersCrit->addTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
			$c->addAnd($membersCrit);
		}
		else{
			return array();
		}

		$c->applyFilters();
		$categoryIds = $c->getFetchedIds();
					
		return $categoryIds;
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
		if (!strlen(trim($pk))) {
        	return null;
        }
        
		if (null !== ($obj = categoryPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}

		$criteria = KalturaCriteria::create(categoryPeer::OM_CLASS);
		$criteria->add(categoryPeer::ID, $pk);

		$v = categoryPeer::doSelect($criteria, $con);

		return !empty($v) > 0 ? $v[0] : null;
	}
	
	/**
	 * Retrieve multiple objects by pkey.
	 *
	 * @param      array $pks List of primary keys
	 * @param      PropelPDO $con the connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function retrieveByPKs($pks, PropelPDO $con = null)
	{
		$objs = null;
		if (empty($pks)) {
			$objs = array();
		} else {
			$criteria = KalturaCriteria::create(categoryPeer::OM_CLASS);
			$criteria->add(categoryPeer::ID, $pks, Criteria::IN);
			$objs = categoryPeer::doSelect($criteria, $con);
		}
		return $objs;
	}
	
	/**
	 * Retrieve category(ies) by referenceId
	 * @param string $v
	 * @return array
	 */
	public static function getByReferenceId ($v)
	{
		$c = KalturaCriteria::create(self::OM_CLASS);
		$c->addAnd(categoryPeer::REFERENCE_ID, $v);
		$objects = self::doSelect($c);
		
		return $objects;
		
	}
	
	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::getRootObjects()
	 */
	public function getRootObjects(IRelatedObject $object)
	{
		/* @var $object category */
		
		$rootCategory = $object->getRootCategoryFromFullIds($object);
		if($rootCategory)
			return array($rootCategory);
			
		return array();
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::isReferenced()
	 */
	public function isReferenced(IRelatedObject $object)
	{
		return false;
	}

	public static function getFullNamesByCategoryIds(array $categoryIds)
	{
		$fullNames = array();
		if(!count($categoryIds))
			return $fullNames;
		$categories = self::retrieveByPKs($categoryIds);
		foreach($categories as $category)
		{
			$fullNames[] = $category->getFullName();
		}

		return $fullNames;
	}
}
