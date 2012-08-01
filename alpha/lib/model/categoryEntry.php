<?php


/**
 * Skeleton subclass for representing a row from the 'category_entry' table.
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
class categoryEntry extends BasecategoryEntry {
	
	/*
	 * when calculating category->entries count, 
	 * entry might belong to a few sub categories and should not be calculated more than once in the parent category.
	 * those fields means what categories where already set the calculation of the entry.
	 */
	private $entryCategoriesRemovedIds = null;
	private $entryCategoriesAddedIds = null;
	
	public function setEntryCategoriesAddedIds($v)
	{
		$this->entryCategoriesAddedIds = $v;
	}
	
	public function setEntryCategoriesRemovedIds($v)
	{
		$this->entryCategoriesRemovedIds = $v;
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/om/Basecategory#preSave()
	 */
	public function preSave(PropelPDO $con = null)
	{
		$category = categoryPeer::retrieveByPK($this->getCategoryId());
		$this->setCategoryFullIds($category->getFullIds());
		return parent::preSave();
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/om/Basecategory#postInsert()
	 */
	public function postInsert(PropelPDO $con = null)
	{	
		parent::postInsert($con);
		categoryPeer::setUseCriteriaFilter(false);
		$category = categoryPeer::retrieveByPK($this->getCategoryId());
		categoryPeer::setUseCriteriaFilter(true);

		$entry = entryPeer::retrieveByPK($this->getEntryId());
		
		if ($this->getStatus() == CategoryEntryStatus::PENDING)
			$category->incrementPendingEntriesCount();

		if($this->getStatus() == CategoryEntryStatus::ACTIVE)
			$this->setEntryOnCategory($category, $entry);
			
		if(!categoryEntryPeer::getSkipSave())
			$entry->indexToSearchIndex();
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/om/Basecategory#postInsert()
	 */
	public function postUpdate(PropelPDO $con = null)
	{	
		parent::postUpdate($con);
		
		categoryPeer::setUseCriteriaFilter(false);
		$category = categoryPeer::retrieveByPK($this->getCategoryId());
		categoryPeer::setUseCriteriaFilter(true);
		if(!$category)
			throw new kCoreException('category id [' . $this->getCategoryId() . 'was not found', kCoreException::ID_NOT_FOUND);
			
		$entry = entryPeer::retrieveByPK($this->getEntryId());
		if(!$entry && $this->getStatus() != CategoryEntryStatus::DELETED)
			throw new kCoreException('entry id [' . $this->getEntryId() . 'was not found', kCoreException::ID_NOT_FOUND);
		
		
		if($entry && $this->getStatus() == CategoryEntryStatus::ACTIVE && 
			($this->getColumnsOldValue(categoryEntryPeer::STATUS) == CategoryEntryStatus::PENDING))
			$entry = $this->setEntryOnCategory($category, $entry);
		
		if($this->getStatus() == CategoryEntryStatus::REJECTED &&
			$this->getColumnsOldValue(categoryEntryPeer::STATUS) == CategoryEntryStatus::PENDING)
			$category->decrementPendingEntriesCount();
			
		if($this->getStatus() == CategoryEntryStatus::DELETED)
		{ 
			if($this->getColumnsOldValue(categoryEntryPeer::STATUS) == CategoryEntryStatus::ACTIVE)
			{
				if(is_null($this->entryCategoriesRemovedIds))
				{
					$categoriesEntries = categoryEntryPeer::retrieveActiveByEntryId($this->getEntryId());
					
					$categoriesIds = array();
					foreach ($categoriesEntries as $categroyEntry)
					{
						//cannot get directly the full ids - since it might not be updated.
						if($categroyEntry->getCategoryId() != $this->getCategoryId())
							$categoriesIds[] = $categroyEntry->getCategoryId();
					}
					
					$categoriesRemoved = categoryPeer::retrieveByPKs($categoriesIds);
					
					$entryCategoriesRemovedIds = array();
					foreach($categoriesRemoved as $categoryRemoved)
					{
						$fullIds = explode(categoryPeer::CATEGORY_SEPARATOR, $categoryRemoved->getFullIds());
						$entryCategoriesRemovedIds = array_merge($entryCategoriesRemovedIds, $fullIds);
					}
					
					$this->entryCategoriesRemovedIds = $entryCategoriesRemovedIds;
				}
				
				$category->decrementEntriesCount(1, $this->entryCategoriesRemovedIds);
				$category->decrementDirectEntriesCount();
		
				if($entry && !categoryEntryPeer::getSkipSave()) //entry might be deleted - and delete job remove the categoryEntry object
				{
					$fullName = $category->getFullName();
					
					$entryCategories = explode(entry::ENTRY_CATEGORY_SEPARATOR, $entry->getCategories());
					$newCategories = array();
					foreach($entryCategories as $entryCategory) 
					{
						if (!preg_match("/^".$fullName."/", $entryCategory))
							$newCategories[] = $entryCategory;
					}

					$categoryId = $category->getId();
					
					$entryCategoriesIds = explode(entry::ENTRY_CATEGORY_SEPARATOR, $entry->getCategoriesIds());
					$newCategoriesIds = array();
					foreach($entryCategoriesIds as $entryCategoryIds) 
					{
						if (!preg_match("/^".$categoryId."/", $entryCategoryIds))
							$newCategoriesIds[] = $entryCategoryIds;
					}
					
					$entry->parentSetCategories(implode(entry::ENTRY_CATEGORY_SEPARATOR, $newCategories));
					$entry->parentSetCategoriesIds(implode(entry::ENTRY_CATEGORY_SEPARATOR, $newCategoriesIds));
					
					$entry->save();
				}
			}
			
			if($this->getColumnsOldValue(categoryEntryPeer::STATUS) == CategoryEntryStatus::PENDING)
				$category->decrementPendingEntriesCount();
		}
		$category->save();
		
		if($entry && !categoryEntryPeer::getSkipSave())
			$entry->indexToSearchIndex();
	}
	
	private function setEntryOnCategory(category $category, $entry = null)
	{
		if(is_null($this->entryCategoriesAddedIds))
		{
			$categoriesEntries = categoryEntryPeer::retrieveActiveByEntryId($this->getEntryId());
			
			$categoriesIds = array();
			foreach ($categoriesEntries as $categroyEntry)
			{
				//cannot get directly the full ids - since it might not be updated.
				if($categroyEntry->getCategoryId() != $this->getCategoryId())
					$categoriesIds[] = $categroyEntry->getCategoryId();
			}
			
			$categoriesAdded = categoryPeer::retrieveByPKs($categoriesIds);
			
			$entryCategoriesAddedIds = array();
			foreach($categoriesAdded as $categoryAdded)
			{
				$fullIds = explode(categoryPeer::CATEGORY_SEPARATOR, $categoryAdded->getFullIds());
				$entryCategoriesAddedIds = array_merge($entryCategoriesAddedIds, $fullIds);
			}
			
			$this->entryCategoriesAddedIds = $entryCategoriesAddedIds;
		}
		
		$category->incrementEntriesCount(1, $this->entryCategoriesAddedIds);
		$category->incrementDirectEntriesCount();
		
		//if was pending - decrease pending entries count!
		if($this->getColumnsOldValue(categoryEntryPeer::STATUS) == CategoryEntryStatus::PENDING)
			$category->decrementPendingEntriesCount();
			
		$category->save();

		//only categories with no context are saved on entry - this is only for Backward compatible 
		if($entry && !categoryEntryPeer::getSkipSave() && (trim($category->getPrivacyContexts()) == '' || $category->getPrivacyContexts() == null))
		{
			$categories = array();
			if(trim($entry->getCategories()) != '')
				$categories = explode(entry::ENTRY_CATEGORY_SEPARATOR, $entry->getCategories());
				
			$categories[] = $category->getFullName();
			
			$categoriesIds = array();
			if(trim($entry->getCategoriesIds()) != '')
				$categoriesIds = explode(entry::ENTRY_CATEGORY_SEPARATOR, $entry->getCategoriesIds());
				
			$categoriesIds[] = $category->getId();
			
			$entry->parentSetCategories(implode(entry::ENTRY_CATEGORY_SEPARATOR, $categories));
			$entry->parentSetCategoriesIds(implode(entry::ENTRY_CATEGORY_SEPARATOR, $categoriesIds));
			$entry->justSave();
		}
		
		return $entry;
	}
	
	public function reSetCategoryFullIds()
	{
		$category = categoryPeer::retrieveByPK($this->getCategoryId());
		if(!$category)
			throw new kCoreException('category id [' . $this->getCategoryId() . 'was not found', kCoreException::ID_NOT_FOUND);
			
		$this->setCategoryFullIds($category->getFullIds());
	}
	

	
	public function getCacheInvalidationKeys()
	{
		return array("categoryEntry:entryId=".$this->getEntryId());
	}
} // categoryEntry
