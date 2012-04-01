<?php
/**
 * Subclass for performing query and update operations on the 'entry' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class entryPeer extends BaseentryPeer 
{
	const PRIVACY_BY_CONTEXTS = 'entry.PRIVACY_BY_CONTEXTS';
	const ENTITLED_KUSERS = 'entry.ENTITLED_KUSERS';
	const CREATOR_KUSER_ID = 'entry.CREATOR_KUSER_ID';
	
	private static $s_default_count_limit = 301;
	private static $filerResults = false;
	
	private static $kuserBlongToMoreThanMaxCategoriesForSearch = false;
	
	// cache classes by their type
	private static $class_types_cache = array(
		entryType::AUTOMATIC => parent::OM_CLASS,
		entryType::MEDIA_CLIP => parent::OM_CLASS,
		entryType::MIX => parent::OM_CLASS,
		entryType::PLAYLIST => parent::OM_CLASS,
		entryType::DATA => parent::OM_CLASS,
		entryType::LIVE_STREAM => parent::OM_CLASS,
	);
	
	/**
	 * This function sets the requested order of entries to the given criteria object.
	 * we can use an associative array to hold the ordering fields instead of the
	 * switch statement being used now
	 *
	 * @param $c = given criteria object
	 * @param int $order = the requested sort order
	 */
	public static function setOrder($c, $order)
	{
		switch ($order) {
		case entry::ENTRY_SORT_MOST_VIEWED:
			//$c->hints = array(entryPeer::TABLE_NAME => "views_index");
			$c->addDescendingOrderByColumn(entryPeer::VIEWS);
			break;
		  
		case entry::ENTRY_SORT_MOST_RECENT:
			//$c->hints = array(entryPeer::TABLE_NAME => "created_at_index");
			$c->addDescendingOrderByColumn(entryPeer::CREATED_AT);
			break;
			
		case entry::ENTRY_SORT_MOST_COMMENTS:  
			$c->addDescendingOrderByColumn(entryPeer::COMMENTS);
			break;
			
		case entry::ENTRY_SORT_MOST_FAVORITES:  
			$c->addDescendingOrderByColumn(entryPeer::FAVORITES);
			break;
			
		case entry::ENTRY_SORT_RANK:
			$c->addDescendingOrderByColumn(entryPeer::RANK);
			break;
			
		case entry::ENTRY_SORT_MEDIA_TYPE:
			$c->addAscendingOrderByColumn(entryPeer::MEDIA_TYPE);
			break;
			
		case entry::ENTRY_SORT_NAME:
			$c->addAscendingOrderByColumn(entryPeer::NAME);
			break;
			
			case entry::ENTRY_SORT_KUSER_SCREEN_NAME:
			$c->addAscendingOrderByColumn(kuserPeer::SCREEN_NAME);
			break;
		}
	}
	
	public static function getOrderedCriteria($kshowId, $order, $limit, $introId = null, $entryId = null)
	{
		$c = new Criteria();
		$c->add(entryPeer::KSHOW_ID, $kshowId);
		$c->add(entryPeer::TYPE, entryType::MEDIA_CLIP);
		
		if ($introId)
			$c->add(entryPeer::ID, $introId, Criteria::NOT_EQUAL);
			
		if ($entryId)
			$c->addDescendingOrderByColumn('(' . entryPeer::ID . '="' . $entryId . '")');
		
		entryPeer::setOrder($c, $order);
		$c->addJoin(entryPeer::KUSER_ID, kuserPeer::ID, Criteria::INNER_JOIN);
		
	    $c->setLimit($limit);
			    
	    return $c;
	}
	
	/**
	 * This function returns a pager object holding the specified kshows' entries
	 * sorted by a given sort order.
	 * each entry holds the kuser object of its host.
	 *
	 * @param int $kshowId = the requested sort order
	 * @param int $order = the requested sort order
	 * @param int $pageSize = number of kshows in each page
	 * @param int $page = the requested page
	 * @param int $firstEntries = an array of entries to be picked first (show entry, show intro,
	 *	or an arbitrary entry that was pointed to by the url)
	 * @return the pager object
	 */
	public static function getOrderedPager($kshowId, $order, $pageSize, $page, $firstEntries = null)
	{
		$c = new Criteria();
		$c->add(entryPeer::KSHOW_ID, $kshowId);
		$c->add(entryPeer::TYPE, entryType::MEDIA_CLIP);
		
		if ($firstEntries)
			foreach($firstEntries as $firstEntryId)
				$c->addDescendingOrderByColumn('(' . entryPeer::ID . '="' . $firstEntryId . '")');
			
		entryPeer::setOrder($c, $order);
		$c->addJoin(entryPeer::KUSER_ID, kuserPeer::ID, Criteria::INNER_JOIN);
		
		$pager = new sfPropelPager('entry', $pageSize);
	    $pager->setCriteria($c);
	    $pager->setPage($page);
	    $pager->setPeerMethod('doSelectJoinkuser');
	    $pager->setPeerCountMethod('doCountJoinkuser');
	    $pager->init();
			    
	    return $pager;
	}

	
		/**
	 * This function returns a pager object holding the specified kshows' entries
	 * sorted by a given sort order.
	 * each entry holds the kuser object of its host.
	 *
	 * @param int $kshowId = the requested sort order
	 * @param int $order = the requested sort order
	 * @param int $pageSize = number of kshows in each page
	 * @param int $page = the requested page
	 * @param int $firstEntries = an array of entries to be picked first (show entry, show intro,
	 *	or an arbitrary entry that was pointed to by the url)
	 * @return the pager object
	 */
	public static function getUserEntriesOrderedPager( $order, $pageSize, $page, $userid, $favorites_flag )
	{
		if( $favorites_flag ) return self::getUserFavorites($userid, favorite::SUBJECT_TYPE_ENTRY, favorite::PRIVACY_TYPE_USER, $pageSize, $page, $order );
		
		$c = new Criteria();
		$c->add(entryPeer::KUSER_ID, $userid);
		$c->add(entryPeer::TYPE, entryType::MEDIA_CLIP);
			
		entryPeer::setOrder($c, $order);
		$c->addJoin(entryPeer::KUSER_ID, kuserPeer::ID, Criteria::INNER_JOIN);
		
		$pager = new sfPropelPager('entry', $pageSize);
	    $pager->setCriteria($c);
	    $pager->setPage($page);
	    $pager->setPeerMethod('doSelectJoinkuser');
	    $pager->setPeerCountMethod('doCountJoinkuser');
	    $pager->init();
			    
	    return $pager;
	}
	
	/**
	 * This function returns a pager object holding the given user's favorite entries
	 * each entry holds the kuser object of its host.
	 *
	 * @param int $kuserId = the requested user
	 * @param int $type = the favorite type (currently only SUBJECT_TYPE_ENTRY will match)
	 * @param int $privacy = the privacy filter
	 * @param int $pageSize = number of kshows in each page
	 * @param int $page = the requested page
	 * @return the pager object
	 */
	public static function getUserFavorites($kuserId, $type, $privacy, $pageSize, $page, $order = entry::ENTRY_SORT_MOST_VIEWED )
	{
		$c = new Criteria();
		entryPeer::setOrder($c, $order);
		$c->addJoin(entryPeer::KUSER_ID, kuserPeer::ID, Criteria::INNER_JOIN);
		$c->addJoin(entryPeer::ID, favoritePeer::SUBJECT_ID, Criteria::INNER_JOIN);
		$c->add(favoritePeer::KUSER_ID, $kuserId);
		$c->add(favoritePeer::SUBJECT_TYPE, $type);
		$c->add(favoritePeer::PRIVACY, $privacy);
		$c->setDistinct();
		
		// our assumption is that a request for private favorites should include public ones too 
		if( $privacy == favorite::PRIVACY_TYPE_USER ) 
		{
			$c->addOr( favoritePeer::PRIVACY, favorite::PRIVACY_TYPE_WORLD );
		}
		
		
		$c->addAscendingOrderByColumn(entryPeer::NAME);
		
	    $pager = new sfPropelPager('entry', $pageSize);
	    $pager->setCriteria($c);
	    $pager->setPage($page);
	    $pager->setPeerMethod('doSelectJoinkuser');
	    $pager->setPeerCountMethod('doCountJoinkuser');
	    $pager->init();
			    
	    return $pager;
	}
	
	public static function getUserEntries($kuserId, $pageSize, $page)
	{
		$c = new Criteria();
		$c->addJoin(entryPeer::KUSER_ID, kuserPeer::ID, Criteria::INNER_JOIN);
		$c->add(entryPeer::KUSER_ID, $kuserId);
		$c->add(entryPeer::TYPE, entryType::MEDIA_CLIP);
		$c->addAscendingOrderByColumn(entryPeer::CREATED_AT);
		
	    $pager = new sfPropelPager('entry', $pageSize);
	    $pager->setCriteria($c);
	    $pager->setPage($page);
	    $pager->setPeerMethod('doSelectJoinkuser');
	    $pager->setPeerCountMethod('doCountJoinkuser');
	    $pager->init();
			    
	    return $pager;
	}
	
	public static function selectIdsForCriteria ( Criteria $c )
	{
		$c->addSelectColumn(self::ID);
		$rs = self::doSelectStmt($c);
		$id_list = Array();
		
		while($rs->next())
		{
			$id_list[] = $rs->getInt(1);
		}
		
		$rs->close();
		
		return $id_list;
	}	

	public static function allowDeletedInCriteriaFilter()
	{
		$ecf = entryPeer::getCriteriaFilter();
		$ecf->getFilter()->remove ( entryPeer::STATUS );
	}
	
	public static function blockDeletedInCriteriaFilter()
	{
		$ecf = entryPeer::getCriteriaFilter();
		$ecf->getFilter()->addAnd ( entryPeer::STATUS, entryStatus::DELETED, Criteria::NOT_EQUAL);
	}	
	
/* -------------------- Critera filter functions -------------------- */	
	
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);
		self::$filerResults = true;
		$res = parent::retrieveByPK($pk, $con);
		KalturaCriterion::enableTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);
		self::$filerResults = false;
		
		return $res;
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
	
	/**
	 * find all the entries from a list of ids that have the proper status to be considered non-pending
	 */
	public static function retrievePendingEntries ($pks, $con = null)
	{
		self::setUseCriteriaFilter ( false );
		$c= new Criteria();
		$c->add ( entryPeer::ID , $pks , Criteria::IN );
		$c->add ( entryPeer::STATUS , array ( entryStatus::READY , entryStatus::ERROR_CONVERTING ) , Criteria::NOT_IN );
		$res = self::doSelect( $c );
		self::setUseCriteriaFilter ( true );
		return $res;
	}
	
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}
		
		$c = KalturaCriteria::create(entryPeer::OM_CLASS); 
		$c->addAnd ( entryPeer::STATUS, entryStatus::DELETED, Criteria::NOT_EQUAL);
		
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
		$ksString = kCurrentContext::$ks ? kCurrentContext::$ks : '';
		if($ksString <> '')
			$kuser = kuserPeer::getActiveKuserByPartnerAndUid($partnerId, kCurrentContext::$ks_uid);
		
		$kuserId = null;
		if($kuser)
			$kuserId = $kuser->getId();

		$crit = null;
		if (kEntitlementUtils::getEntitlementEnforcement())
		{
			$privacyContexts = kEntitlementUtils::getPrivacyContextSearch();
			$crit = $c->getNewCriterion (self::PRIVACY_BY_CONTEXTS, $privacyContexts, Criteria::IN);
			$crit->addTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);
			
			if($kuserId)
			{
				//ENTITLED_KUSERS field includes $this->entitledUserEdit, $this->entitledUserEdit, and users on work groups categories.
				$critKusers = ($c->getNewCriterion(self::ENTITLED_KUSERS, $kuserId, Criteria::EQUAL));
				$critKusers->addTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);
				$crit->addOr($critKusers);
				
				$categoriesIds = array();
				$categories = categoryPeer::doSelectEntitledAndNonIndexedCategories($kuserId, entry::CATEGORY_SEARCH_LIMIT);
				if(count($categories) == entry::CATEGORY_SEARCH_LIMIT)
					self::$kuserBlongToMoreThanMaxCategoriesForSearch = true;
			 
			
				foreach($categories as $category)
					$categoriesIds[] = $category->getId();
	
				if (count($categoriesIds))
				{
					$critCategories = $c->getNewCriterion(self::CATEGORIES_IDS, $categoriesIds, Criteria::IN);
					$critCategories->addTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);
					$crit->addOr($critCategories);
				}
			}
		}
		
		$ks = ks::fromSecureString(kCurrentContext::$ks);
		// when session is not admin and without list:* privilege, allow access to user entries only
		if ($ks && $kuserId && !$ks->isAdmin() && !$ks->verifyPrivileges(ks::PRIVILEGE_LIST, ks::PRIVILEGE_WILDCARD))
		{
			$kuserCrit = $c->getNewCriterion(entryPeer::KUSER_ID , $kuserId, Criteria::EQUAL);
			$kuserCrit->addTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);
			
			if(!$crit)
				$crit = $kuserCrit;
			else 
				$crit->addOr ($kuserCrit);
				
			$creatorKuserCrit = $c->getNewCriterion(entryPeer::CREATOR_KUSER_ID, $kuserId, Criteria::EQUAL);
			$creatorKuserCrit->addTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);
			$crit->addOr($creatorKuserCrit);
		}
		
		if($crit)
			$c->addAnd ( $crit );
			
		self::$s_criteria_filter->setFilter($c);
	}
	
	public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
	{
		if (kEntitlementUtils::getEntitlementEnforcement())
			throw new kCoreException('doCount is not supported for entitlement scope enable');
		
		parent::doCount($criteria, $distinct, $con);
	}
	
	public static function doCountWithLimit (Criteria $criteria, $distinct = false, $con = null)
	{
		$criteria = clone $criteria;
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn("DISTINCT ".entryPeer::ID);
		} else {
			$criteria->addSelectColumn(entryPeer::ID);
		}

		$criteria->setLimit( self::$s_default_count_limit );
		
		$rs = self::doSelectStmt($criteria, $con);
		$count = 0;

		// instead of using rs->next() using statement->fetchAll()
		$entries = $rs->fetchAll(PDO::FETCH_COLUMN);
//		while($rs->next())
//			$count++;
		// count is simply the size of the array
		$count = count($entries);
	
		return $count;
	}

	public static function doStubCount (Criteria $criteria, $distinct = false, $con = null)
	{
		return 0;
	}	
	
	
/* -------------------- Critera filter functions -------------------- */
	

	// this function sets the status of an entry to entryStatus::DELETED
	// users can only delete their own entries
	public static function setStatusDeletedForEntry( $entry_id, $kuser_id  )
	{
		// 
		$entry = self::retrieveByPK( $entry_id );
		if( $entry == null ) return false;
		if( $entry->getKuserId() == $kuser_id ) $entry->setStatus( entryStatus::DELETED ); else return false;
		$entry->save();
		return true;
	}
	 
	public static function updateAccessControl($partnerId, $oldAccessControlId, $newAccessControlId)
	{
		$selectCriteria = new Criteria();
		$selectCriteria->add(entryPeer::PARTNER_ID, $partnerId);
		$selectCriteria->add(entryPeer::ACCESS_CONTROL_ID, $oldAccessControlId);
		
		$updateValues = new Criteria();
		$updateValues->add(entryPeer::ACCESS_CONTROL_ID, $newAccessControlId);
		
		$con = Propel::getConnection(self::DATABASE_NAME);
		
		BasePeer::doUpdate($selectCriteria, $updateValues, $con);
	}

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
			$entryType = $row[$colnum + 4]; // type
			if(isset(self::$class_types_cache[$entryType]))
				return self::$class_types_cache[$entryType];
				
			$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $entryType);
			if($extendedCls)
			{
				KalturaLog::debug("Found class[$extendedCls]");
				self::$class_types_cache[$entryType] = $extendedCls;
				return $extendedCls;
			}
			self::$class_types_cache[$entryType] = parent::OM_CLASS;
		}
			
		return parent::OM_CLASS;
	}

	
	public static function doSelectJoinkuser(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = clone $criteria;
		
		if($c instanceof KalturaCriteria)
		{
			$skipApplyFilters = entryPeer::applyEntitlmentCriteria($c);
			
			if(!$skipApplyFilters)
			{
				$c->applyFilters();
				$criteria->setRecordsCount($c->getRecordsCount());
			}
		}
			
		$results = parent::doSelectJoinkuser($c, $con, $join_behavior);
		self::$filerResults = false;
		
		return $results;
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
			$skipApplyFilters = entryPeer::applyEntitlmentCriteria($c);
			
			if(!$skipApplyFilters)
			{
				$c->applyFilters();
				$criteria->setRecordsCount($c->getRecordsCount());
			}
		}
			
		$queryResult =  parent::doSelect($c, $con);
		
		if($c instanceof KalturaCriteria)
			$criteria->setRecordsCount($c->getRecordsCount());
			
		self::$filerResults = false;
		
		return $queryResult;
	}
	
	private static function applyEntitlmentCriteria(Criteria &$c)
	{
		$skipApplyFilters = false;
		
		if(	kEntitlementUtils::getEntitlementEnforcement() && 
			KalturaCriterion::isTagEnable(KalturaCriterion::TAG_ENTITLEMENT_ENTRY) && 
			self::$kuserBlongToMoreThanMaxCategoriesForSearch &&
			!$c->getOffset())
		{
			KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);
			
			$entitlementCrit = clone $c;
			$entitlementCrit->applyFilters();
			
			KalturaCriterion::enableTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);
			
			if ($entitlementCrit->getRecordsCount() < $entitlementCrit->getLimit())
			{
				$c = $entitlementCrit;
				$c->setRecordsCount($entitlementCrit->getRecordsCount());
		 		$skipApplyFilters = true;
		 		self::$filerResults = true;
		 		//TODO add header the not full search
			}
			else
			{
				self::$s_criteria_filter->enableAdditional();
				self::$filerResults = false;
			}
		}
		
		return $skipApplyFilters;
	} 
	
	public static function getDurationType($duration)
	{
		if ($duration >= 0 && $duration <= 4*60)
			return entry::ENTRY_DURATION_TYPE_SHORT;
			
		if ($duration > 4*60 && $duration <= 20*60)
			return entry::ENTRY_DURATION_TYPE_MEDIUM;
		
		if ($duration > 20*60)
			return entry::ENTRY_DURATION_TYPE_LONG;
		
		return entry::ENTRY_DURATION_TYPE_NOTAVAILABLE;
	}
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("entry:id=%s", self::ID));		
	}
	
	/* (non-PHPdoc)
	 * @see BaseentryPeer::getAtomicColumns()
	 */
	public static function getAtomicColumns()
	{
		return array(entryPeer::STATUS);
	}
	
	/**
	 * Override in order to filter objects returned from doSelect.
	 *  
	 * @param      array $selectResults The array of objects to filter.
	 * @param	   Criteria $criteria
	 */
	public static function filterSelectResults(&$selectResults, Criteria $criteria)
	{
		if (!kEntitlementUtils::getEntitlementEnforcement() || KalturaCriterion::isTagEnable(KalturaCriterion::TAG_ENTITLEMENT_ENTRY || !self::$filerResults))
			return parent::filterSelectResults(&$selectResults, $criteria);
		
		KalturaLog::debug('Entitlement: Filter Results');
		
		$removedRecordsCount = 0;
		foreach ($selectResults as $key => $selectResult)
		{
			if (!kEntitlementUtils::isEntryEntitled($selectResult))
			{
				unset($selectResults[$key]);
				$removedRecordsCount++;
			}	
		}
		
		if($criteria instanceof KalturaCriteria)
		{
			$recordsCount = $criteria->getRecordsCount();
			$criteria->setRecordsCount($recordsCount - $removedRecordsCount);
		}
		
		self::$filerResults = false;
		
		parent::filterSelectResults(&$selectResults, $criteria);
	}
}

class entryPool
{
	private $map ;
	public function addEntries ( $entries )
	{
		$this->map = array();
		foreach ( $entries as $entry )
		{
			$this->map[$entry->getId()]=$entry;
		}
	}
	
	public function retrieveByPK ( $id )
	{
		return @$this->map[$id];
	}
	
	public function release()
	{
		$this->map = null;
	}
}
