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
class categoryEntryPeer extends BasecategoryEntryPeer {

	private static $skipEntrySave = false;
	
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}

		$c = KalturaCriteria::create(categoryPeer::OM_CLASS); 
		$c->add ( self::PARTNER_ID, kCurrentContext::$ks_partner_id, Criteria::EQUAL);
				
		self::$s_criteria_filter->setFilter ( $c );
	}
	
	public static function getSkipSave()
	{
		return self::$skipEntrySave;
	}
	
	public static function retrieveByCategoryIdAndEntryId($categoryId, $entryId)
	{
		$c = new Criteria();
		$c->add(self::PARTNER_ID, kCurrentContext::$ks_partner_id);
		$c->add(self::CATEGORY_ID, $categoryId);
		$c->add(self::ENTRY_ID, $entryId);
		
		return self::doSelectOne($c);
	}
	
	public static function selectByEntryId($entryId)
	{
		$c = new Criteria();
		$c->add(self::PARTNER_ID, kCurrentContext::$ks_partner_id);
		$c->add(self::ENTRY_ID, $entryId);
		
		return self::doSelect($c);
	}
	
	public static function syncEntriesCategories(entry $entry)
	{		
		//TODO save on entry only with no privacy context 
		self::$skipEntrySave = true;
		
		if($entry->getNewCategories() != null && $entry->getNewCategories() !== "")
			$newCats = explode(entry::ENTRY_CATEGORY_SEPARATOR, $entry->getNewCategories());
		else
			$newCats = array();
			
		if($entry->getNewCategoriesIds() != null && $entry->getNewCategoriesIds() !== "")
			$newCatsIds = explode(entry::ENTRY_CATEGORY_SEPARATOR, $entry->getNewCategoriesIds());
		else
			$newCatsIds = array();	
		
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$dbCategories = categoryPeer::retrieveByPKs($newCatsIds);
		KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);

		foreach ($dbCategories as $dbCategory)
		{
			$newCats[] = $dbCategory->getFullName();
		}
		
		array_unique($newCats);
		
		$allIds = array();
		$allCats = array();
		$allIdsWithParents = array ();
		
		$addedCats = array();
		$removedCats = array();
		$remainingCats = array();
		$oldCats = array();
		
		$dbOldCategoriesEntry = categoryEntryPeer::selectByEntryId($entry->getId());
		foreach ($dbOldCategoriesEntry as $dbOldCategoryEntry)
		{
			$category = categoryPeer::retrieveByPKNoFilter($dbOldCategoryEntry->getCategoryId());
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
			$category = categoryPeer::getByFullNameExactMatch ( $cat );
			if ($category) 
			{
				if($category->getPrivacyContext() == '' || $category->getPrivacyContext() == null)
				{
					$allCats[] = $category->getFullName();
					$allIds [] = $category->getId ();
				}
					
				$allIdsWithParents [] = $category->getId ();
				$allIdsWithParents = array_merge ( $allIdsWithParents, $category->getAllParentsIds () );
			}
		}

		$alreadyAddedCatIds = $allIdsWithParents;
		
		foreach ( $addedCats as $cat )
		{
			$category = categoryPeer::getByFullNameExactMatch ( $cat );
			if (!$category)
			{
				KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
				$unentitedCategory = categoryPeer::getByFullNameExactMatch ( $cat );
				KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);

				if(!$unentitedCategory)
					$category = category::createByPartnerAndFullName ( $entry->getPartnerId (), $cat );
			}
				
			if (!$category)
				continue;
				
			$categoryEntry = new categoryEntry();
			$categoryEntry->setEntryId($entry->getId());
			$categoryEntry->setCategoryId($category->getId());
			$categoryEntry->setEntryCategoriesAddedIds($alreadyAddedCatIds);
			$categoryEntry->setPartnerId($entry->getPartnerId());
			$categoryEntry->save();
			
			if($category->getPrivacyContext() == '' || $category->getPrivacyContext() == null)
			{
				$allCats[] = $category->getFullName();
				$allIds [] = $category->getId ();
			}

			$alreadyAddedCatIds [] = $category->getId ();
			$alreadyAddedCatIds = array_merge ( $alreadyAddedCatIds, $category->getAllParentsIds () );
		}

		$alreadyRemovedCatIds = $allIdsWithParents;
		
		foreach ( $removedCats as $cat ) 
		{
			$category = categoryPeer::getByFullNameExactMatch ( $cat );

			if ($category){
				$categoryEntryToDelete = categoryEntryPeer::retrieveByCategoryIdAndEntryId($category->getId(), $entry->getId());
				if($categoryEntryToDelete)
				{
					$categoryEntryToDelete->setEntryCategoriesRemovedIds($alreadyRemovedCatIds);
					$categoryEntryToDelete->delete();
				}
				
				$alreadyRemovedCatIds [] = $category->getId ();
				$alreadyRemovedCatIds = array_merge ( $alreadyRemovedCatIds, $category->getAllParentsIds () );
			}
		}
		self::$skipEntrySave = false;
		
		$categories = implode ( ",", $allCats);
		$categoriesIds = implode (',', $allIds);
		return array($categories, $categoriesIds);
		
	} 
} // categoryEntryPeer
