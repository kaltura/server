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

		if ($this->getStatus() == CategoryEntryStatus::PENDING)
			$category->incrementPendingEntriesCount();

		if($this->getStatus() == CategoryEntryStatus::ACTIVE)
		{
			$entry = entryPeer::retrieveByPK($this->getEntryId());
			$this->setEntryOnCategory($category, $entry);
		}
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
			
		if($this->getStatus() == CategoryEntryStatus::ACTIVE && 
			($this->getColumnsOldValue(categoryEntryPeer::STATUS) == CategoryEntryStatus::PENDING))
			$this->setEntryOnCategory($category, $entry);
		
		if($this->getStatus() == CategoryEntryStatus::REJECTED &&
			$this->getColumnsOldValue(categoryEntryPeer::STATUS) == CategoryEntryStatus::PENDING)
			$category->decrementPendingEntriesCount();
			
		if($this->getStatus() == CategoryEntryStatus::DELETED)
		{ 
			if($this->getColumnsOldValue(categoryEntryPeer::STATUS) == CategoryEntryStatus::ACTIVE)
			{
				$category->decrementEntriesCount(1, $this->entryCategoriesRemovedIds);
				$category->decrementDirectEntriesCount();
		
				if(!categoryEntryPeer::getSkipSave())
				{
					$entry->removeCategory($category->getFullName());
					$entry->save();
				}
			}
			
			if($this->getColumnsOldValue(categoryEntryPeer::STATUS) == CategoryEntryStatus::PENDING)
				$category->decrementPendingEntriesCount();
		}
	}
	
	private function setEntryOnCategory($category, $entry = null)
	{
		$category->incrementEntriesCount(1, $this->entryCategoriesAddedIds);
		$category->incrementDirectEntriesCount();
		
		//if was pending - decrease pending entries count!
		if($this->getColumnsOldValue(categoryEntryPeer::STATUS) == CategoryEntryStatus::PENDING)
			$category->decrementPendingEntriesCount();
		
		//only categories with no context are saved on entry - this is only for Backward compatible 
		if($entry && !categoryEntryPeer::getSkipSave())
		{
			if($category->getPrivacyContext() == '')
				$entry->setCategories($entry->getCategories() . entry::ENTRY_CATEGORY_SEPARATOR . $category->getFullName());
				
			$entry->setUpdatedAt(time());
			$entry->save();
		}
	}
	
	public function reSetCategoryFullIds()
	{
		$category = categoryPeer::retrieveByPK($this->getCategoryId());
		if(!$category)
			throw new kCoreException('category id [' . $this->getCategoryId() . 'was not found', kCoreException::ID_NOT_FOUND);
			
		$this->setCategoryFullIds($category->getFullIds());
	}
	

	
} // categoryEntry
