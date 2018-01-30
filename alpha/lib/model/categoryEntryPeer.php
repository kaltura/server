<?php


/**
 * Skeleton subclass for performing query and update operations on the 'category_entry' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class categoryEntryPeer extends BasecategoryEntryPeer implements IRelatedObjectPeer {

	private static $skipEntrySave = false;
	
	/**
	 * For backward compatibility we need to keep entry->categories and 
	 * entry->categoriesIds updated with categoryEntry. 
	 * If the entry was already saved - shouldn't save it from this class
	 */
	public static function getSkipSave()
	{
		return self::$skipEntrySave;
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
	
	
	public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
	{
		$c = clone $criteria;

		if($c instanceof KalturaCriteria)
		{
			$c->applyFilters();
		}
			
		return parent::doCount($c, $con);
	}
	
	
	public static function retrieveByCategoryIdAndEntryId($categoryId, $entryId)
	{
		$c = new Criteria();
		$c->add(self::CATEGORY_ID, $categoryId);
		$c->add(self::ENTRY_ID, $entryId);
		
		return self::doSelectOne($c);
	}
	
	public static function retrieveByCategoryIdAndEntryIdNotRejected($categoryId, $entryId)
	{
		$c = new Criteria();
		$c->add(self::CATEGORY_ID, $categoryId);
		$c->add(self::ENTRY_ID, $entryId);
		$c->add(self::STATUS, CategoryEntryStatus::REJECTED, Criteria::NOT_EQUAL);
		
		return self::doSelectOne($c);
	}
	
	public static function retrieveActiveByEntryId($entryId)
	{
		$c = new Criteria();
		$c->addAnd(categoryEntryPeer::ENTRY_ID, $entryId);
		$c->addAnd(categoryEntryPeer::STATUS, CategoryEntryStatus::ACTIVE, Criteria::EQUAL);
		
		return categoryEntryPeer::doSelect($c);
	}
	
	public static function retrieveActiveByEntryIdAndCategoryIds($entryId, array $categoryIds)
 	{
 		$c = new Criteria();
 		$c->addAnd(categoryEntryPeer::ENTRY_ID, $entryId);
 		$c->addAnd(categoryEntryPeer::CATEGORY_ID, $categoryIds, Criteria::IN);
 		$c->addAnd(categoryEntryPeer::STATUS, CategoryEntryStatus::ACTIVE);
 		
 		return categoryEntryPeer::doSelect($c);
 	}
	
	public static function retrieveOneActiveByEntryId($entryId)
	{
		$c = new Criteria();
		$c->addAnd(categoryEntryPeer::ENTRY_ID, $entryId);
		$c->addAnd(categoryEntryPeer::STATUS, CategoryEntryStatus::ACTIVE, Criteria::EQUAL);
		
		return categoryEntryPeer::doSelectOne($c);
	}
	
	
	public static function retrieveActiveAndPendingByEntryId($entryId)
	{
		$c = new Criteria();
		$c->addAnd(categoryEntryPeer::ENTRY_ID, $entryId);
		$c->addAnd(categoryEntryPeer::STATUS, array(CategoryEntryStatus::PENDING, CategoryEntryStatus::ACTIVE), Criteria::IN);
		
		return categoryEntryPeer::doSelect($c);
	}
	
	public static function retrieveActiveAndPendingByEntryIdAndPrivacyContext($entryId, $privacyContext)
	{
		$c = new Criteria();
		$c->addAnd(categoryEntryPeer::ENTRY_ID, $entryId);
		$c->addAnd(categoryEntryPeer::STATUS, array(CategoryEntryStatus::PENDING, CategoryEntryStatus::ACTIVE), Criteria::IN);
		$c->addAnd(categoryEntryPeer::PRIVACY_CONTEXT, $privacyContext, Criteria::IN);
		
		return categoryEntryPeer::doSelect($c);
	}

	public static function retrieveByEntryIdStatusPrivacyContextExistance($entryId, array $statuses = null, $hasPrivacyContext = false)
	{
		$c = new Criteria();
		$c->addAnd(categoryEntryPeer::ENTRY_ID, $entryId);
		if(!$statuses)
			$c->addAnd(categoryEntryPeer::STATUS, CategoryEntryStatus::ACTIVE, Criteria::EQUAL);
		else
			$c->addAnd(categoryEntryPeer::STATUS, $statuses, Criteria::IN);
		if($hasPrivacyContext)
			$c->addAnd(categoryEntryPeer::PRIVACY_CONTEXT, null, Criteria::ISNOTNULL);
		else 
			$c->addAnd(categoryEntryPeer::PRIVACY_CONTEXT, null, Criteria::ISNULL);
			
		return categoryEntryPeer::doSelect($c);
	}
	
	public static function retrieveOneByEntryIdStatusPrivacyContextExistance($entryId, array $statuses = null, $hasPrivacyContext = false)
	{
		$c = new Criteria();
		$c->addAnd(categoryEntryPeer::ENTRY_ID, $entryId);
		if(!$statuses)
			$c->addAnd(categoryEntryPeer::STATUS, CategoryEntryStatus::ACTIVE, Criteria::EQUAL);
		else
			$c->addAnd(categoryEntryPeer::STATUS, $statuses, Criteria::IN);
		if($hasPrivacyContext)
			$c->addAnd(categoryEntryPeer::PRIVACY_CONTEXT, null, Criteria::ISNOTNULL);
		else 
			$c->addAnd(categoryEntryPeer::PRIVACY_CONTEXT, null, Criteria::ISNULL);
			
		return categoryEntryPeer::doSelectOne($c);
	}
	
	public static function selectByEntryId($entryId)
	{
		$c = new Criteria();
		$c->add(self::ENTRY_ID, $entryId);
		
		return self::doSelect($c);
	}
		
	public static function syncEntriesCategories(entry $entry, $isCategoriesModified)
	{					 		
		self::$skipEntrySave = true;
		
		if($entry->getNewCategories() != null && $entry->getNewCategories() !== "")
			$newCats = explode(entry::ENTRY_CATEGORY_SEPARATOR, $entry->getNewCategories());
		else
			$newCats = array();

		if(!$isCategoriesModified)
		{
			if($entry->getNewCategoriesIds() != null && $entry->getNewCategoriesIds() !== "")
				$newCatsIds = explode(entry::ENTRY_CATEGORY_SEPARATOR, $entry->getNewCategoriesIds());
			else
				$newCatsIds = array();	
				
			
			KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
			$dbCategories = categoryPeer::retrieveByPKs($newCatsIds);
			KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
	
			foreach ($dbCategories as $dbCategory)
			{
				//skip categoy with privacy contexts.
				if($dbCategory->getPrivacyContexts() != null && $dbCategory->getPrivacyContexts() != '')
					continue;
					
				$newCats[] = $dbCategory->getFullName();
			}
		}
		
		$newCats = array_unique($newCats);
		
		$allIds = array();
		$allCats = array();
		
		$addedCats = array();
		$removedCats = array();
		$remainingCats = array();
		$oldCats = array();
		$oldCatsIds = array();
		
		$dbOldCategoriesEntry = categoryEntryPeer::selectByEntryId($entry->getId());
		foreach ($dbOldCategoriesEntry as $dbOldCategoryEntry)
			$oldCatsIds[] = $dbOldCategoryEntry->getCategoryId();

		
		$oldCategoris = categoryPeer::retrieveByPKsNoFilter($oldCatsIds);
		foreach($oldCategoris as $category)
		{
			if($category->getPrivacyContexts() != '' && $category->getPrivacyContexts() != null)
				continue;
				
			$oldCats[] = $category->getFullName();
		}
				
		
		foreach ( $oldCats as $cat )
		{
			if (array_search ( $cat, $newCats ) === false)
				$removedCats [] = $cat;
		}

		foreach ( $newCats as $cat )
		{
			if (array_search ( $cat, $oldCats ) === false)
				$addedCats [] = $cat;
			else
				$remainingCats [] = $cat;
		}
		
		foreach ( $remainingCats as $cat ) 
		{
			KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
			$category = categoryPeer::getByFullNameExactMatch ( $cat );
			KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
			if ($category) 
			{
				if($category->getPrivacyContext() == '' || $category->getPrivacyContext() == null)
				{
					$allCats[] = $category->getFullName();
					$allIds [] = $category->getId ();
				}
			}
		}

		foreach ( $addedCats as $cat )
		{
			$category = categoryPeer::getByFullNameExactMatch ( $cat );
			if (!$category)
			{
				KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
				$unentitedCategory = categoryPeer::getByFullNameExactMatch ( $cat );
				KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);

				if(!$unentitedCategory)
				{
					$category = category::createByPartnerAndFullName ( $entry->getPartnerId (), $cat );
					
					//it is possible to add on an entry a few new categories on the same new parent - 
					//and we need to sync sphinx once we add so the category will not be duplicated 
					kEventsManager::flushEvents();
				}
			}
			else
			{
				$categoryKuser = categoryKuserPeer::retrievePermittedKuserInCategory($category->getId(), kCurrentContext::getCurrentKsKuserId());
				if(kEntitlementUtils::getEntitlementEnforcement() && 
					$category->getContributionPolicy() != ContributionPolicyType::ALL &&
					(!$categoryKuser || $categoryKuser->getPermissionLevel() == CategoryKuserPermissionLevel::MEMBER))
				{
					//user is not entitled to add entry to this category
					$category = null;
				}
			}
				
			if (!$category)
				continue;

			//when use caetgoryEntry->add categoryEntry object was alreay created - and no need to create it.
			//when using baseEntry->categories = 'my category' will need to add the new category.
			$categoryEntry = categoryEntryPeer::retrieveByCategoryIdAndEntryId($category->getId(), $entry->getId())	;
			
			if(!$categoryEntry)
			{
				$categoryEntry = new categoryEntry();
				$categoryEntry->setEntryId($entry->getId());
				$categoryEntry->setCategoryId($category->getId());
				$categoryEntry->setPartnerId($entry->getPartnerId());
				$categoryEntry->setStatus(CategoryEntryStatus::ACTIVE);
				$categoryEntry->save();
			}
			
			if($category->getPrivacyContext() == '' || $category->getPrivacyContext() == null)
			{
				// only categories with no context should be set on entry->categories and entry->categoriesIds
				$allCats[] = $category->getFullName();
				$allIds [] = $category->getId ();
			}

			$alreadyAddedCatIds [] = $category->getId ();
			$alreadyAddedCatIds = array_merge ( $alreadyAddedCatIds, $category->getAllParentsIds () );
		}

		foreach ( $removedCats as $cat ) 
		{
			$category = categoryPeer::getByFullNameExactMatch ( $cat );

			if ($category)
			{
				$categoryEntryToDelete = categoryEntryPeer::retrieveByCategoryIdAndEntryId($category->getId(), $entry->getId());
				if($categoryEntryToDelete)
				{
					$categoryKuser = categoryKuserPeer::retrievePermittedKuserInCategory($categoryEntryToDelete->getCategoryId(), kCurrentContext::getCurrentKsKuserId());
					if($category->getPrivacyContexts() && (!$categoryKuser || $categoryKuser->getPermissionLevel() == CategoryKuserPermissionLevel::MEMBER))
					{
						//not entiteld to delete - should be set back on the entry.
						$allCats[] = $category->getFullName();
						$allIds[] = $category->getId ();
					}
					else
					{
						$categoryEntryToDelete->setStatus(CategoryEntryStatus::DELETED);
						$categoryEntryToDelete->save();
					}
				}
			}
			else
			{
				//category was not found - it could be that user is not entitled to remove it 
				KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
				$category = categoryPeer::getByFullNameExactMatch ( $cat );
				KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
				
				if($category)
				{
					$allCats[] = $category->getFullName();
					$allIds[] = $category->getId ();
				}
			}
		}
		self::$skipEntrySave = false;
		
		$entry->parentSetCategories ( implode ( ",", $allCats) );
		$entry->parentSetCategoriesIds ( implode (',', $allIds) );	
	} 
	
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}
		
		$c =  new Criteria(); 
		$c->addAnd ( categoryEntryPeer::STATUS, CategoryEntryStatus::DELETED, Criteria::NOT_EQUAL);

		self::$s_criteria_filter->setFilter($c);
	}
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("categoryEntry:entryId=%s", self::ENTRY_ID), array("categoryEntry:categoryId=%s", self::CATEGORY_ID));		
	}
	
	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::getRootObjects()
	 */
	public function getRootObjects(IRelatedObject $object)
	{
		/* @var $object categoryEntry */
		
		$roots = array();
		
		$category = categoryPeer::retrieveByPK($object->getCategoryId());
		if($category)
		{
			$roots = $category->getPeer()->getRootObjects($category);
			$roots[] = $category;
		}
		
		$entry = entryPeer::retrieveByPK($object->getEntryId());
		if($entry)
			$roots[] = $entry;
		
		return $roots;
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::isReferenced()
	 */
	public function isReferenced(IRelatedObject $object)
	{
		return false;
	}
} // categoryEntryPeer
