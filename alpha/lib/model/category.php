<?php

/**
 * Subclass for representing a row from the 'category' table.
 *
 * 
 *
 * @package lib.model
 */ 
class category extends Basecategory
{
	protected $childs_for_save = array();
	
	protected $depth = 0;
	
	protected $parent_category;
	
	protected $old_full_name = "";
	
	protected $old_parent_id = null;
	
	const MAX_CATEGORY_DEPTH = 8;
	
	public function save(PropelPDO $con = null)
	{
		if ($this->isNew())
		{
			$numOfCatsForPartner = categoryPeer::doCount(new Criteria());
			
			if ($numOfCatsForPartner >= Partner::MAX_NUMBER_OF_CATEGORIES)
			{
				throw new kCoreException("Max number of categories was reached", kCoreException::MAX_NUMBER_OF_CATEGORIES_REACHED);
			}
		}
		
		// set the depth of the parent category + 1
		if ($this->isNew() || $this->isColumnModified(categoryPeer::PARENT_ID))
		{
			$parentCat = $this->getParentCategory();
			if ($this->getParentId() !== 0){
				$this->setDepth($parentCat->getDepth() + 1);
			}else{
				$this->setDepth(0);
			}
				$this->setChildsDepth();
		}
		
		if ($this->getDepth() >= self::MAX_CATEGORY_DEPTH)
		{
			throw new kCoreException("Max depth was reached", kCoreException::MAX_CATEGORY_DEPTH_REACHED);
		} 
		
		if ($this->isColumnModified(categoryPeer::NAME) || $this->isColumnModified(categoryPeer::PARENT_ID))
		{
			$this->updateFullName();
			
			$this->renameOnEntries();
		}
		
		// happens in 3 cases:
		// 1. name of the current category was updated
		// 2. full name of the parent category was updated and it was set here as child
		// 3. parent id was changed
		if ($this->isColumnModified(categoryPeer::FULL_NAME)) 
			$this->setChildsFullNames();
		
		// save the childs 
		foreach($this->childs_for_save as $child)
		{
			$child->save();
		}
		$this->childs_for_save = array();
		
		if ($this->isColumnModified(categoryPeer::DELETED_AT) && $this->getDeletedAt() !== null)
		{
			$this->moveEntriesToParent();
		}
		
		if ($this->isColumnModified(categoryPeer::PARENT_ID))
		{
			// decrease for the old parent category
			$oldParentCat = categoryPeer::retrieveByPK($this->old_parent_id);
			if ($oldParentCat)
				$oldParentCat->decrementEntriesCount($this->entries_count);
			
			// increase for the new parent category
			$newParentCat = categoryPeer::retrieveByPK($this->parent_id);
			if ($newParentCat)
				$newParentCat->incrementEntriesCount($this->entries_count);
			
			$this->old_parent_id = null;
		}
		
		parent::save($con);
	}

	/* (non-PHPdoc)
	 * @see lib/model/om/Basecategory#preUpdate()
	 */
	public function preUpdate(PropelPDO $con = null)
	{
		if($this->isColumnModified(categoryPeer::DELETED_AT) && !is_null($this->getDeletedAt()))
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return parent::preUpdate($con);
	}
	
	public function setName($v)
	{
		$this->old_full_name = $this->getFullName();
		
		$v = categoryPeer::getParsedName($v);
		parent::setName($v);
	}
	
	public function setParentId($v)
	{
		$this->old_full_name = $this->getFullName();
		
		$this->validateParentIdIsNotChild($v);
		
		if ($v !== 0)
		{
			$parentCat = $this->getPeer()->retrieveByPK($v);
			if (!$parentCat)
				throw new Exception("Parent category [".$this->getParentId()."] was not found on category [".$this->getId()."]");
		}
			
		$this->old_parent_id = $this->parent_id;
		parent::setParentId($v);
		$this->parent_category = null;
	}
	
	/**
	 * Set the child categories full names using the current full path
	 */
	public function setChildsFullNames()
	{
		if ($this->isNew()) // do nothing
			return;
			
		$this->loadChildsForSave();
		foreach($this->childs_for_save as $child)
		{
			$child->setFullName($this->getFullName() . categoryPeer::CATEGORY_SEPARATOR . $child->getName());
		}
	}
	
	/**
	 * Set the child depth
	 */
	public function setChildsDepth()
	{
		if ($this->isNew()) // do nothing
			return;
			
		$this->loadChildsForSave();
		foreach($this->childs_for_save as $child)
		{
			$child->setDepth($this->getDepth() + 1);
			$child->setChildsDepth();
		}
	}
	
	/**
	 * Increment entries count (will increment recursively the parent categories too)
	 */
	public function incrementEntriesCount($increase = 1)
	{
		$this->setEntriesCount($this->getEntriesCount() + $increase);
		
		if ($this->getParentId())
		{
			$parentCat = $this->getParentCategory();
			if ($parentCat)
			{
				$parentCat->incrementEntriesCount($increase);
			}
		}
		
		$this->save();
	}
	
	/**
	 * Decrement entries count (will decrement recursively the parent categories too)
	 */
	public function decrementEntriesCount($decrease = 1)
	{
		if($this->getDeletedAt(null))
			return;
			
		$newCount = $this->getEntriesCount() - $decrease;
		if ($newCount <= 0) // don't allow zero values
			$this->setEntriesCount(0);
		else
			$this->setEntriesCount($newCount);
		
		if ($this->getParentId())
		{
			$parentCat = $this->getParentCategory();
			if ($parentCat)
			{
				$parentCat->decrementEntriesCount($decrease);
			}
		}
		
		$this->save();
	}
	
	public function validateFullNameIsUnique()
	{
		$name = $this->getFullName();
		$category = categoryPeer::getByFullNameExactMatch($name);
		if ($category)
			throw new kCoreException("Duplicate category: $name", kCoreException::DUPLICATE_CATEGORY);
	}
	
	public function setDeletedAt($v)
	{
		$this->loadChildsForSave();
		foreach($this->childs_for_save as $child)
		{
			$child->setDeletedAt($v);
		}
		
		parent::setDeletedAt($v);
	}
	
	public function delete(PropelPDO $con = null)
	{
		$this->loadChildsForSave();
		foreach($this->childs_for_save as $child)
		{
			$child->delete($con);
		}
		
		$this->moveEntriesToParent(); // will remove from entries
		parent::delete($con);
	}
	
	private function loadChildsForSave()
	{
		if (count($this->childs_for_save) > 0)
			return;
			
		$this->childs_for_save = $this->getChilds();
	}
	
	/**
	 * Update the current full path by using the parent full path (if exists)
	 * 
	 * @param category $parentCat
	 */
	private function updateFullName()
	{
		$parentCat = $this->getParentCategory();
			
		if ($parentCat)
		{
			$this->setFullName($parentCat->getFullName() . categoryPeer::CATEGORY_SEPARATOR . $this->getName());
		}
		else
		{
			$this->setFullName($this->getName());
		}
		
		$this->validateFullNameIsUnique();
	}
	
	/**
	 * Rename category name on linked entries
	 */
	private function renameOnEntries()
	{
		if ($this->isNew()) // do nothing
			return;
		/*
		 * TODO: this can be queued to a batch job as this will only affect the
		 * categories returned by baseEntry.get and not the search functionality 
		 * (because search translates categories to ids and use ids to search)   
		*/ 
		$c = KalturaCriteria::create("entry");
		$entryFilter = new entryFilter();
		$entryFilter->set("_matchor_categories_ids", $this->getId());
		$entryFilter->attachToCriteria($c);
		$entries = entryPeer::doSelect($c);
		KalturaLog::log("category::save - Updating [".count($entries)."] entries");
		foreach($entries as $entry)
		{
			$entry->renameCategory($this->old_full_name, $this->getFullName());
			$entry->justSave();
		}
	}
	
	/**
	 * Moves the entries from the current category to the parent category (if exists) or remove from entry (if parent doesn't exists)
	 */
	private function moveEntriesToParent()
	{
		$parentCat = $this->getParentCategory();
		if ($parentCat)
		{
			$c = KalturaCriteria::create("entry");
			$entryFilter = new entryFilter();
			$entryFilter->set("_matchor_categories_ids", $this->getId());
			$entryFilter->attachToCriteria($c);
			$entries = entryPeer::doSelect($c);
			foreach($entries as $entry)
			{
				$entry->renameCategory($this->getFullName(), $parentCat->getFullName());
				$entry->syncCategories();
			}
		}
		else
		{
			$this->removeFromEntries();
		}
	}
	
	/**
	 * Removes the category from the entries
	 */
	private function removeFromEntries()
	{
		$c = KalturaCriteria::create("entry");
		$entryFilter = new entryFilter();
		$entryFilter->set("_matchor_categories_ids", $this->getId());
		$entryFilter->attachToCriteria($c);
		$entries = entryPeer::doSelect($c);
		foreach($entries as $entry)
		{
			$entry->removeCategory($this->full_name);
			$entry->syncCategories();
		}
	}
	
	/**
	 * Validate recursivly that the new parent id is not one of the child categories
	 * 
	 * @param int $parentId
	 */
	public function validateParentIdIsNotChild($parentId)
	{
		$childs = $this->getChilds();
		foreach($childs as $child)
		{
			if ($child->getId() == $parentId)
			{
				throw new kCoreException("Parent id [$parentId] is one of the childs", kCoreException::PARENT_ID_IS_CHILD);
			}
			
			$child->validateParentIdIsNotChild($parentId);
		}
	}
	
	/**
	 * @return catagory
	 */
	public function getParentCategory()
	{
		if ($this->parent_category === null && $this->getParentId())
			$this->parent_category = $this->getPeer()->retrieveByPK($this->getParentId());
			
		return $this->parent_category;
	}
	
	/**
	 * @return array
	 */
	public function getChilds()
	{
		if ($this->isNew())
			return array();
			
		$c = new Criteria();
		$c->add(categoryPeer::PARENT_ID, $this->getId());
		return categoryPeer::doSelect($c);
	}
	
	/**
	 * Initialize new category using patnerId and fullName, this will also create the needed categories for the fullName
	 * 
	 * @param $partnerId
	 * @param $fullName
	 * @return category
	 */
	public static function createByPartnerAndFullName($partnerId, $fullName)
	{
		$fullNameArray = explode(categoryPeer::CATEGORY_SEPARATOR, $fullName);
		$fullNameTemp = "";
		$parentId = 0;
		foreach($fullNameArray as $name)
		{
			if ($fullNameTemp === "")
				$fullNameTemp .= $name;
			else
				$fullNameTemp .= (categoryPeer::CATEGORY_SEPARATOR . $name);
				
			$category = categoryPeer::getByFullNameExactMatch($fullNameTemp);
			if (!$category)
			{
				$category = new category();
				$category->setPartnerId($partnerId);
				$category->setParentId($parentId);
				$category->setName($name);
				$category->save();
			}
			$parentId = $category->getId();
		}
		return $category;
	}
}
