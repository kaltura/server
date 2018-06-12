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
	const ENTRY_ID = 'entry.ENTRY_ID';

	const ENTRIES_PER_ACCESS_CONTROL_UPDATE_LIMIT = 1000;
	
	private static $s_default_count_limit = 301;
	private static $filterResults = false;

	private static $userContentOnly = false;
	private static $filteredCategoriesIds = array();

	private static $accessControlScope;

	private static $kuserBlongToMoreThanMaxCategoriesForSearch = false;
	
	private static $lastInitializedContext = null; // last initialized security context (ks + partner id)
	private static $validatedEntries = array();

	// cache classes by their type
	private static $class_types_cache = array(
		entryType::AUTOMATIC => parent::OM_CLASS,
		entryType::MEDIA_CLIP => parent::OM_CLASS,
		entryType::MIX => parent::OM_CLASS,
		entryType::PLAYLIST => parent::OM_CLASS,
		entryType::DATA => parent::OM_CLASS,
		entryType::LIVE_STREAM => 'LiveStreamEntry',
		entryType::LIVE_CHANNEL => 'LiveChannel',
	);

	public static function setUserContentOnly($contentOnly)
	{
		self::$userContentOnly = $contentOnly;
	}

	public static function getUserContentOnly()
	{
		return self::$userContentOnly;
	}

	public static function setFilterResults ($v)
	{
		self::$filterResults = $v;
	}

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

	public static function onlyReadyCriteriaFilter()
	{
		$ecf = entryPeer::getCriteriaFilter();
		$ecf->getFilter()->remove(entryPeer::STATUS);
		$ecf->getFilter()->addAnd(entryPeer::STATUS, entryStatus::READY, Criteria::EQUAL);
	}

	public static function blockDeletedInCriteriaFilter()
	{
		$ecf = entryPeer::getCriteriaFilter();
		$ecf->getFilter()->addAnd ( entryPeer::STATUS, entryStatus::DELETED, Criteria::NOT_EQUAL);
	}

/* -------------------- Critera filter functions -------------------- */

	public static function retrieveByPK($pk, PropelPDO $con = null)
	{
		KalturaCriterion::disableTags(array(KalturaCriterion::TAG_ENTITLEMENT_ENTRY, KalturaCriterion::TAG_WIDGET_SESSION));
		self::$filterResults = true;
		$res = parent::retrieveByPK($pk, $con);
		KalturaCriterion::restoreTags(array(KalturaCriterion::TAG_ENTITLEMENT_ENTRY, KalturaCriterion::TAG_WIDGET_SESSION));
		self::$filterResults = false;

		return $res;
	}

	public static function retrieveByPKNoFilter ($pk, $con = null, $filterEntitlements = true)
	{
		KalturaCriterion::disableTags(array(KalturaCriterion::TAG_ENTITLEMENT_ENTRY, KalturaCriterion::TAG_WIDGET_SESSION));
		self::$filterResults = $filterEntitlements;
		self::setUseCriteriaFilter ( false );
		$res = parent::retrieveByPK( $pk , $con );
		self::setUseCriteriaFilter ( true );
		self::$filterResults = false;
		KalturaCriterion::restoreTags(array(KalturaCriterion::TAG_ENTITLEMENT_ENTRY, KalturaCriterion::TAG_WIDGET_SESSION));
		return $res;
	}

	public static function retrieveByPKsNoFilter ($pks, $con = null)
	{
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);
		self::$filterResults = true;
		self::setUseCriteriaFilter ( false );
		$res = parent::retrieveByPKs( $pks , $con );
		self::setUseCriteriaFilter ( true );
		self::$filterResults = false;
		KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);
		return $res;
	}

	/**
	 * Retrieves array of entries with referenceId $v
	 * @param string $v
	 * @return array
	 */
	public static function retrieveByReferenceId ($v)
	{
		$c = KalturaCriteria::create(entryPeer::OM_CLASS);
		$c->addAnd("referenceID", $v);
		return entryPeer::doSelect($v);
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
	
	public static function retrieveChildEntriesByEntryIdAndPartnerId ($parentId, $partnerId)
	{
		$c = KalturaCriteria::create(entryPeer::OM_CLASS);
		
		$filter = new entryFilter();
		$filter->setParentEntryIdEqual($parentId);
		$filter->setPartnerSearchScope($partnerId);
		$filter->setDisplayInSearchEquel(mySearchUtils::DISPLAY_IN_SEARCH_SYSTEM);
		$filter->attachToCriteria($c);

		$parentEntry = entryPeer::retrieveByPK($parentId);
		if($parentEntry)
			KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);
		$res = self::doSelect($c);
		if($parentEntry)
			KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);
		return $res;
	}

	public static function setFilterdCategoriesIds($filteredCategoriesIds)
	{
		self::$filteredCategoriesIds = $filteredCategoriesIds;
	}

	public static function getFilterdCategoriesIds()
	{
		return self::$filteredCategoriesIds;
	}

	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}

		$c = KalturaCriteria::create(entryPeer::OM_CLASS);
		$c->addAnd ( entryPeer::STATUS, entryStatus::DELETED, Criteria::NOT_EQUAL);

		$critEntitled = null;

		$ks = ks::fromSecureString(kCurrentContext::$ks);

		//when entitlement is enable and admin session or user session with list:* privilege
		if (kEntitlementUtils::getEntitlementEnforcement() &&
		   ((kCurrentContext::$is_admin_session || !self::$userContentOnly)))
		{
			$privacyContexts = kEntitlementUtils::getPrivacyContextSearch();
			$critEntitled = $c->getNewCriterion (self::PRIVACY_BY_CONTEXTS, $privacyContexts, KalturaCriteria::IN_LIKE);
			$critEntitled->addTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);

			if(kCurrentContext::getCurrentKsKuserId())
			{
				//ENTITLED_KUSERS field includes $this->entitledUserEdit, $this->entitledUserEdit, and users on work groups categories.
				$entitledKuserByPrivacyContext = kEntitlementUtils::getEntitledKuserByPrivacyContext();
				$critEntitledKusers = $c->getNewCriterion(self::ENTITLED_KUSERS, $entitledKuserByPrivacyContext, KalturaCriteria::IN_LIKE);
				$critEntitledKusers->addTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);

				$categoriesIds = array();
				$categoriesIds = categoryPeer::retrieveEntitledAndNonIndexedByKuser(kCurrentContext::getCurrentKsKuserId(), kConf::get('category_search_limit'));
				if(count($categoriesIds) >= kConf::get('category_search_limit'))
					self::$kuserBlongToMoreThanMaxCategoriesForSearch = true;

				if (count($categoriesIds))
				{
					sort($categoriesIds); // sort categories in order to later create identical queries which enable better caching  
					$critCategories = $c->getNewCriterion(self::CATEGORIES_IDS, $categoriesIds, KalturaCriteria::IN_LIKE);
					$critCategories->addTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);
					$critEntitled->addOr($critCategories);
				}

				$critEntitled->addOr($critEntitledKusers);
			}

			//user should be able to get all entries s\he uploaded - outside the privacy context
			$kuser = kCurrentContext::getCurrentKsKuserId();
			if($kuser !== 0) {
				$critKuser = $c->getNewCriterion(entryPeer::KUSER_ID , $kuser , Criteria::EQUAL);
				$critKuser->addTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);
				$critEntitled->addOr($critKuser);
			}
		}
		elseif(self::$userContentOnly) // when session is not admin and without list:* privilege, allow access to user entries only
		{
			$critEntitled = $c->getNewCriterion(entryPeer::KUSER_ID , kCurrentContext::getCurrentKsKuserId(), Criteria::EQUAL);
			$critEntitled->addTag(KalturaCriterion::TAG_WIDGET_SESSION);
		}

		//we need to set the filter before getDisableEntitlementForEntry since otherwise the partner criteria will not be added to $c,
		//it will be added to some other criteria object which will get disposed once setFilter is called
		self::$s_criteria_filter->setFilter($c);

		if($ks && count($ks->getDisableEntitlementForEntry()))
		{
			$entryCrit = $c->getNewCriterion(entryPeer::ENTRY_ID, $ks->getDisableEntitlementForEntry(), Criteria::IN);
			$entryCrit->addTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);

			if($critEntitled)
			{
				$critEntitled->addOr($entryCrit);
			}
			else
			{
				$critEntitled = $entryCrit;
			}
		}

		if($critEntitled)
			$c->addAnd ($critEntitled);

	}

	public static function getDefaultCriteriaFilter()
	{
		return entryPeer::getCriteriaFilter()->getFilter();
	}

	public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
	{
		//TODO - this is problematic! should fix this!
		/*if (kEntitlementUtils::getEntitlementEnforcement())
			throw new kCoreException('doCount is not supported for entitlement scope enable');
		*/
		return parent::doCount($criteria, $distinct, $con);
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

		if($criteria instanceof KalturaCriteria)
		{
			$criteria->applyFilters();
			return min(self::$s_default_count_limit,$criteria->getRecordsCount());
		}

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
		$c = KalturaCriteria::create(entryPeer::OM_CLASS);

		//trying to fetch more entries than the $entryCount limit
		$c->setMaxRecords(self::ENTRIES_PER_ACCESS_CONTROL_UPDATE_LIMIT + 1);
		$c->add(entryPeer::ACCESS_CONTROL_ID, $oldAccessControlId);

		$partner = PartnerPeer::retrieveByPK($partnerId);
		$partnerEntitlement = $partner->getDefaultEntitlementEnforcement();

		kEntitlementUtils::initEntitlementEnforcement($partnerId , false);
		$entries = self::doSelect($c);
		$entryCount = count($entries);

		if ($entryCount == 0)
			return;

		if ($entryCount > self::ENTRIES_PER_ACCESS_CONTROL_UPDATE_LIMIT)
			throw new kCoreException("exceeded max entries per access control update limit",kCoreException::EXCEEDED_MAX_ENTRIES_PER_ACCESS_CONTROL_UPDATE_LIMIT);

		$entryIds = array();
		foreach($entries as $entry)
			$entryIds[] = $entry->getId();

		$selectCriteria = new Criteria();
		$selectCriteria->add(entryPeer::PARTNER_ID, $partnerId);
		$selectCriteria->add(entryPeer::ID, $entryIds ,Criteria::IN);

		$updateValues = new Criteria();
		$updateValues->add(entryPeer::ACCESS_CONTROL_ID, $newAccessControlId);

		$con = Propel::getConnection(self::DATABASE_NAME);

		BasePeer::doUpdate($selectCriteria, $updateValues, $con);

		foreach($entries as $entry)
			kEventsManager::raiseEventDeferred(new kObjectReadyForIndexEvent($entry));

		if ($partnerEntitlement)
			kEntitlementUtils::initEntitlementEnforcement($partnerId , true);
	}

	/**
	 * Return the class name that associated with the entry type
	 */
	public static function getEntryClassByType($entryType)
	{
		if(isset(self::$class_types_cache[$entryType]))
			return self::$class_types_cache[$entryType];

		$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $entryType);
		if($extendedCls)
		{
			self::$class_types_cache[$entryType] = $extendedCls;
			return $extendedCls;
		}
		self::$class_types_cache[$entryType] = parent::OM_CLASS;
		return parent::OM_CLASS;
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
			 $typeField = self::translateFieldName(entryPeer::TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
  			 return self::getEntryClassByType($row[$typeField]);
		}

		return parent::OM_CLASS;
	}


	public static function doSelectJoinkuser(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		$c = self::prepareEntitlementCriteriaAndFilters( $criteria );

		$results = parent::doSelectJoinkuser($c, $con, $join_behavior);
		self::$filterResults = false;

		return $results;
	}

	/**
	 * @param Criteria $criteria
	 * @param PropelPDO $con
	 */
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		$c = self::prepareEntitlementCriteriaAndFilters( $criteria );

		$queryResult =  parent::doSelect($c, $con);

		if($c instanceof KalturaCriteria)
			$criteria->setRecordsCount($c->getRecordsCount());

		self::$filterResults = false;

		return $queryResult;
	}

	private static function applyEntitlementCriteria(Criteria &$c)
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

			KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);

			if ($entitlementCrit->getRecordsCount() < $entitlementCrit->getLimit())
			{
				$c = $entitlementCrit;
				$c->setRecordsCount($entitlementCrit->getRecordsCount());
		 		$skipApplyFilters = true;
		 		self::$filterResults = true;
			}
			else
			{
				self::$filterResults = false;
				//TODO add header that not full search
			}
		}

		return $skipApplyFilters;
	}

	public static function prepareEntitlementCriteriaAndFilters(Criteria $criteria)
	{
		$c = clone $criteria;

		if($c instanceof KalturaCriteria)
		{
			$skipApplyFilters = entryPeer::applyEntitlementCriteria($c);

			if(!$skipApplyFilters)
			{
				$c->applyFilters();
				$criteria->setRecordsCount($c->getRecordsCount());
			}
		}

		return $c;
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
		return array(array("entry:id=%s", self::ID), array("entry:partnerId=%s", self::PARTNER_ID));		
	}

	/* (non-PHPdoc)
	 * @see BaseentryPeer::getAtomicColumns()
	 */
	public static function getAtomicColumns()
	{
		return array(entryPeer::STATUS);
	}
	
	/* (non-PHPdoc)
	 * @see BaseentryPeer::getAtomicCustomDataFields()
	*/
	public static function getAtomicCustomDataFields()
	{
		return array("replacingEntryId");
	}
	

	private static function filterByAccessControl($entry) {

		self::$accessControlScope->setEntryId($entry->getId());

		$context = new kEntryContextDataResult();
		$accessControl = $entry->getAccessControl();
		$accessControl->applyContext($context, self::$accessControlScope);

		$actions = $context->getActions();
		foreach($actions as $action) {
			/* @var $action kAccessControlAction */
			if($action->getType() == RuleActionType::BLOCK) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Override in order to filter objects returned from doSelect.
	 *
	 * @param      array $selectResults The array of objects to filter.
	 * @param	   Criteria $criteria
	 */
	public static function filterSelectResults(&$selectResults, Criteria $criteria)
	{
		if(empty($selectResults))
			return;


		$partnerId = kCurrentContext::getCurrentPartnerId();
		$partner = PartnerPeer::retrieveByPK($partnerId);

		if($partner && $partner->getShouldApplyAccessControlOnEntryMetadata() && !kCurrentContext::$is_admin_session) {
			if(is_null(self::$accessControlScope)) {
				self::$accessControlScope = new accessControlScope();
				self::$accessControlScope->setContexts(array(ContextType::METADATA));
			}

			$selectResults = array_filter($selectResults, array('entryPeer', 'filterByAccessControl'));
			if($criteria instanceof KalturaCriteria)
				$criteria->setRecordsCount(count($selectResults));
		}

		$removedRecordsCount = 0;
		if ((!kEntitlementUtils::getEntitlementEnforcement() && !is_null(kCurrentContext::$ks))||
			!self::$filterResults ||
			!kEntitlementUtils::getInitialized()) // if initEntitlement hasn't run - skip filters.
			return parent::filterSelectResults($selectResults, $criteria);

		if(is_null(kCurrentContext::$ks) && count($selectResults))
		{
			$entry = $selectResults[0];
			$partner = $entry->getPartner();

			if(!$partner)
				throw new kCoreException('entry partner not found');

			if(!$partner->getDefaultEntitlementEnforcement() || !PermissionPeer::isValidForPartner(PermissionName::FEATURE_ENTITLEMENT, $partner->getId()))
				return parent::filterSelectResults($selectResults, $criteria);
		}

		foreach ($selectResults as $key => $entry)
		{
			if (!kEntitlementUtils::isEntryEntitled($entry))
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

		self::$filterResults = false;
		parent::filterSelectResults($selectResults, $criteria);
	}

	/* (non-PHPdoc)
	 * @see BaseentryPeer::retrieveByPKs()
	 *
	 * Override this function in order to use KalturaCriteria
	 */
	public static function retrieveByPKs($pks, PropelPDO $con = null)
	{
		if (empty($pks))
			return array();

		$criteria = KalturaCriteria::create(self::OM_CLASS);
		$criteria->add(entryPeer::ID, $pks, Criteria::IN);
		return entryPeer::doSelect($criteria, $con);
	}

	public static function addValidatedEntry($entryId)
	{
		$securityContext = array(kCurrentContext::$partner_id, kCurrentContext::$ks);
		if (self::$lastInitializedContext && self::$lastInitializedContext !== $securityContext) {
			self::$validatedEntries = array();
		}
		
		self::$validatedEntries[] = $entryId;
	}

	public static function filterEntriesByPartnerOrKalturaNetwork(array $entryIds, $partnerId)
	{
		$validatedEntries = array_intersect($entryIds, self::$validatedEntries);
		$entryIds = array_diff($entryIds, self::$validatedEntries);
		if(count($entryIds))
		{
			$entryIds = array_slice($entryIds, 0, baseObjectFilter::getMaxInValues());
			
			$c = KalturaCriteria::create(entryPeer::OM_CLASS);
			$c->addAnd(entryPeer::ID, $entryIds, Criteria::IN);
			
			if($partnerId >= 0)
			{
				$criterionPartnerOrKn = $c->getNewCriterion(entryPeer::PARTNER_ID, $partnerId);
				$criterionPartnerOrKn->addOr($c->getNewCriterion(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK));
				$c->addAnd($criterionPartnerOrKn);
			}
	
			$dbEntries = self::doSelect($c);
	
			foreach ($dbEntries as $dbEntry)
			{
				$validatedEntries[] = $dbEntry->getId();
			}
		}

		return $validatedEntries;
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
