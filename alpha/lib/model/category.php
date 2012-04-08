<?php

/**
 * Subclass for representing a row from the 'category' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class category extends Basecategory implements IIndexable
{
	protected $childs_for_save = array();
	
	protected $depth = 0;
	
	protected $parent_category;
	
	protected $inherited_parent_category;
	
	protected $old_full_name = "";
	
	protected $old_parent_id = null;
	
	protected $old_inheritance_type = null;
	
	const CATEGORY_ID_THAT_DOES_NOT_EXIST = 0;
	
	const MAX_NUMBER_OF_MEMBERS_TO_BE_INDEXED_ON_ENTRY = 10;
	
	private static $indexFieldTypes = array(
		'category_id' => IIndexable::FIELD_TYPE_INTEGER,
		'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
		'name' => IIndexable::FIELD_TYPE_STRING,
		'full_name' => IIndexable::FIELD_TYPE_STRING,
		'description' => IIndexable::FIELD_TYPE_STRING,
		'tags' => IIndexable::FIELD_TYPE_STRING,
		'category_status' => IIndexable::FIELD_TYPE_INTEGER,
		'kuser_id' => IIndexable::FIELD_TYPE_INTEGER,
		'display_in_search' => IIndexable::FIELD_TYPE_STRING,
		'members' => IIndexable::FIELD_TYPE_STRING,
		'depth' => IIndexable::FIELD_TYPE_INTEGER,
		'reference_id' => IIndexable::FIELD_TYPE_STRING,
		'privacy_context' => IIndexable::FIELD_TYPE_STRING,
		'privacy_contexts' => IIndexable::FIELD_TYPE_STRING,
		'privacy' => IIndexable::FIELD_TYPE_STRING,
		'members_count' => IIndexable::FIELD_TYPE_INTEGER,
		'pending_members_count' => IIndexable::FIELD_TYPE_INTEGER,
		'entries_count' => IIndexable::FIELD_TYPE_INTEGER,
		'direct_entries_count' => IIndexable::FIELD_TYPE_INTEGER,
		'inheritance_type' => IIndexable::FIELD_TYPE_INTEGER,
		'user_join_policy' => IIndexable::FIELD_TYPE_INTEGER,
		'default_permission_level' => IIndexable::FIELD_TYPE_INTEGER,
		'contribution_policy' => IIndexable::FIELD_TYPE_INTEGER,
		'inherited_parent_id' => IIndexable::FIELD_TYPE_INTEGER,
		'created_at' => IIndexable::FIELD_TYPE_DATETIME,
		'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
		'deleted_at' => IIndexable::FIELD_TYPE_DATETIME
	);
	
	public function save(PropelPDO $con = null)
	{
		if ($this->isNew())
		{
			$c = KalturaCriteria::create(categoryPeer::OM_CLASS); 
			$c->add (categoryPeer::STATUS, CategoryStatus::DELETED, Criteria::NOT_EQUAL);
			$c->add (categoryPeer::PARTNER_ID, kCurrentContext::$ks_partner_id, Criteria::EQUAL);
			
			KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
			$numOfCatsForPartner = categoryPeer::doCount($c);
			KalturaCriterion::enableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
			
			$chunkedCategoryLoadThreshold = kConf::get('kmc_chunked_category_load_threshold');
			if ($numOfCatsForPartner >= $chunkedCategoryLoadThreshold)
				PermissionPeer::enableForPartner(PermissionName::DYNAMIC_FLAG_KMC_CHUNKED_CATEGORY_LOAD, PermissionType::SPECIAL_FEATURE);

			if ($this->getParentId())
			{
				$parentCategory = $this->getParentCategory();
				$this->setPrivacyContexts($parentCategory->getPrivacyContexts());
			}
		}
		
		if($this->getPrivacyContexts() == '' && $this->getPrivacyContext() == '')
		{
			//set default enetitlement default settings = no entitlement
			$this->setPrivacy(PrivacyType::ALL);
			$this->setContributionPolicy(ContributionPolicyType::ALL);
			$this->setDisplayInSearch(DisplayInSearchType::PARTNER_ONLY);
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
		
		if ($this->isColumnModified(categoryPeer::NAME) || $this->isColumnModified(categoryPeer::PARENT_ID))
		{
			$this->updateFullName();
			$this->renameOnEntries();
		}
		else if ($this->isColumnModified(categoryPeer::FULL_NAME))
		{
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
		
		$updateEntriesCount = false;
		if ($this->isColumnModified(categoryPeer::PARENT_ID) && !$this->isNew())
		{
			$updateEntriesCount = true;
			$oldParentId = $this->old_parent_id;
			$newParentId = $this->parent_id;
			$this->old_parent_id = null;	
		}
		
		if (!$this->isNew() &&
			$this->isColumnModified(categoryPeer::INHERITANCE_TYPE) &&  
			$this->inheritance_type == InheritanceType::MANUAL && 
			$this->old_inheritance_type == InheritanceType::INHERIT)
		{
				if($this->old_parent_id)
					$categoryTocopyInheritedFields = categoryPeer::retrieveByPK($this->old_parent_id);
				if($categoryTocopyInheritedFields)
					$this->copyInheritedFields($categoryTocopyInheritedFields);
		}
		
		$kuserChanged = false;
		if ($this->isColumnModified(categoryPeer::KUSER_ID))
			$kuserChanged = true; 
			
		parent::save($con);
		
		if ($kuserChanged && $this->inheritance_type == InheritanceType::MANUAL)
		{	
			$categoryKuser = categoryKuserPeer::retrieveByCategoryIdAndKuserId($this->getId(), $this->kuser_id);
			if (!$categoryKuser)
			{
				$categoryKuser = new categoryKuser();
				$categoryKuser->setCategoryId($this->getId());
				$categoryKuser->setKuserId($this->kuser_id);
			}
			
			$categoryKuser->setPermissionLevel(CategoryKuserPermissionLevel::MANAGER);
			$categoryKuser->setStatus(CategoryKuserStatus::ACTIVE);
			$categoryKuser->setPartnerId($this->getPartnerId());
			$categoryKuser->save();
		}
		
		if ($updateEntriesCount)
		{
			$parentsCategories = array();
			// decrease for the old parent category
			if($oldParentId)
			{
				$oldParentCat = categoryPeer::retrieveByPK($oldParentId);
				if ($oldParentCat)
				{
					$parentsCategories[] = $oldParentCat->getId();
					$parentsCategories = array_merge($parentsCategories, $oldParentCat->getAllParentsIds());
				}
			}
						
			// increase for the new parent category
			$newParentCat = categoryPeer::retrieveByPK($newParentId);
			if ($newParentCat)
			{			
				$parentsCategories[] = $newParentCat->getId();
				$parentsCategories = array_merge($parentsCategories, $newParentCat->getAllParentsIds());
			}
			
			$parentsCategories = array_unique($parentsCategories);
				
			foreach($parentsCategories as $parentsCategoryId)
			{
				$this->updateCategoryCount($parentsCategoryId);
			}
		}			
	}
	
	private function updateCategoryCount($categoryId)
	{
		$category = categoryPeer::retrieveByPK($categoryId);
		if(!$category)
			return;
		
		$allChildren = $category->getAllChildren();
		$allSubCategoriesIds = array();
		$allSubCategoriesIds[] = $category->getId();
		
		if (count($allChildren))
		{
			foreach ($allChildren as $child)
				$allSubCategoriesIds[] = $child->getId();	
		}
		
		$c = KalturaCriteria::create(entryPeer::OM_CLASS);
		$entryFilter = new entryFilter();
		$entryFilter->set("_matchor_categories_ids", implode(',',$allSubCategoriesIds));
		$entryFilter->attachToCriteria($c);
		$c->setLimit(0);
		$entries = entryPeer::doSelect($c);

		$category->setEntriesCount($c->getRecordsCount());
		$category->save();
	}

	/* (non-PHPdoc)
	 * @see lib/model/om/Basecategory#postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
			return parent::postUpdate($con);
		
		$objectUpdated = $this->isModified();
		$objectDeleted = false;
		if($this->isColumnModified(categoryPeer::DELETED_AT) && !is_null($this->getDeletedAt()))
			$objectDeleted = true;
			
		if ($this->isColumnModified(categoryPeer::INHERITANCE_TYPE))
		{
			//TODO ADD_BATCH_JOB TO UPDATE CATEGORY INHERITACE + KUSERS INHERITANCE FOR THIS CATEGORY 
			// + IF CATEGORY DOESN'T INHERIT MEMEBER
		}
		
		if ($this->isColumnModified(categoryPeer::PRIVACY_CONTEXT))
		{
			//TODO ADD_BATCH_JOB TO UPDATE ALL SUB CATEGORIES WITH PROVACY CONTEXT FROM THE PARENT.
		}
		
		$categoryGroupSize = category::MAX_NUMBER_OF_MEMBERS_TO_BE_INDEXED_ON_ENTRY;
		$partner = $this->getPartner();
		if($partner && $partner->getCategoryGroupSize())
			$categoryGroupSize = $partner->getCategoryGroupSize();	
		
		//re-index entries
		if ($this->isColumnModified(categoryPeer::INHERITANCE_TYPE) || 
			$this->isColumnModified(categoryPeer::PRIVACY) || 
			$this->isColumnModified(categoryPeer::PRIVACY_CONTEXTS) || 
			($this->isColumnModified(categoryPeer::MEMBERS_COUNT) && 
			$this->members_count <= $categoryGroupSize && 
			$this->entries_count <= entry::CATEGORY_ENTRIES_COUNT_LIMIT_TO_BE_INDEXED))
		{
			 // TODO ADD JOB TO INDEX ENTRIES.
		}
			
		$ret = parent::postUpdate($con);
		
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		if($objectUpdated)
			kEventsManager::raiseEvent(new kObjectUpdatedEvent($this));
			
		return $ret;
	}
	
	public function setName($v)
	{
		$this->old_full_name = $this->getFullName();
		
		$v = categoryPeer::getParsedName($v);
		parent::setName($v);
	}
	
	public function setFullName($v)
	{
		$this->old_full_name = $this->getFullName();				
		parent::setFullName($v);
	}
	
	/**
	 * @return partner
	 */
	public function getPartner()	{		return PartnerPeer::retrieveByPK( $this->getPartnerId() );	}
	
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
   	* entryAlreadyBlongToCategory return true when entry was already belong to this category before
   	*/
	private function entryAlreadyBlongToCategory($entryCategoriesIds)
	{
		if (!$entryCategoriesIds){
				return false;
		}
		
		$categoriesIds = implode(",",$entryCategoriesIds);
		foreach($entryCategoriesIds as $entryCategoryId)
		{
			if ($entryCategoryId == $this->id)
				return true;
		}
		
		return false;
	}
    
    
      /**
       * Increment entries count (will increment recursively the parent categories too)
       */
      public function incrementEntriesCount($increase = 1, $entryCategoriesAddedIds = null)
      {
            if($entryCategoriesAddedIds && $this->entryAlreadyBlongToCategory($entryCategoriesAddedIds))
                  return;
                  
            $this->setEntriesCount($this->getEntriesCount() + $increase);
            if ($this->getParentId())
            {
                  $parentCat = $this->getParentCategory();
                  if ($parentCat)
                  {
                        $parentCat->incrementEntriesCount($increase, $entryCategoriesAddedIds);
                  }
            }
            
            $this->save();
      }
      
      /**
       * Increment direct entries count (will increment recursively the parent categories too)
       */
      public function incrementDirectEntriesCount()
      {
            $this->setDirectEntriesCount($this->getDirectEntriesCount() + 1);           
            $this->save();
      }
      
      /**
       * Decrement entries count (will decrement recursively the parent categories too)
       */
      public function decrementEntriesCount($decrease = 1, $entryCategoriesRemovedIds = null)
      {
            if($this->entryAlreadyBlongToCategory($entryCategoriesRemovedIds))
                  return;
            
            if($this->getDeletedAt(null))
                  return;
                  
            $newCount = $this->getEntriesCount() - $decrease;
            
            if ($newCount < 0)
            	$newCount = 0;
			$this->setEntriesCount($newCount);
            
            if ($this->getParentId())
            {
                  $parentCat = $this->getParentCategory();
                  if ($parentCat)
                  {
                        $parentCat->decrementEntriesCount($decrease, $entryCategoriesRemovedIds);
                  }
            }
            
            $this->save();
      }
      
      /**
       * Decrement direct entries count (will decrement recursively the parent categories too)
       */
      public function decrementDirectEntriesCount()
      {
			$this->setDirectEntriesCount($this->getDirectEntriesCount() - 1);            
            $this->save();
      }
      
	public function validateFullNameIsUnique()
	{
		$fullName = $this->getFullName();
		$fullName = categoryPeer::getParsedFullName($fullName);
		
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS); 
		$c->add(categoryPeer::FULL_NAME, $fullName);
		$c->add (categoryPeer::STATUS, CategoryStatus::DELETED, Criteria::NOT_EQUAL);
		$c->add (categoryPeer::PARTNER_ID, kCurrentContext::$ks_partner_id, Criteria::EQUAL);
			
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$category = categoryPeer::doSelectOne($c);
		KalturaCriterion::enableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		
		if ($category)
			throw new kCoreException("Duplicate category: $fullName", kCoreException::DUPLICATE_CATEGORY);
	}
	
	public function setDeletedAt($v)
	{
		$this->loadChildsForSave();
		foreach($this->childs_for_save as $child)
		{
			$child->setDeletedAt($v);
		}
		
		$this->setStatus(CategoryStatus::DELETED);
		parent::setDeletedAt($v);
		$this->save();
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
		$c = KalturaCriteria::create(entryPeer::OM_CLASS);
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
			$c = KalturaCriteria::create(entryPeer::OM_CLASS);
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
		$c = KalturaCriteria::create(entryPeer::OM_CLASS);
		$entryFilter = new entryFilter();
		$entryFilter->set("_matchor_categories_ids", $this->getId());
		$entryFilter->attachToCriteria($c);
		KalturaCriterion::disableTags(array(KalturaCriterion::TAG_ENTITLEMENT_ENTRY, KalturaCriterion::TAG_WIDGET_SESSION));
		$entries = entryPeer::doSelect($c);
		KalturaCriterion::enableTags(array(KalturaCriterion::TAG_ENTITLEMENT_ENTRY, KalturaCriterion::TAG_WIDGET_SESSION));
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
	* return array of all parents ids
	* @return array
	*/
	public function getAllParentsIds()
	{
		$parentsIds = array();
		if ($this->getParentId()){
			$parentsIds[] = $this->getParentId();
			$parentsIds = array_merge($parentsIds, $this->getParentCategory()->getAllParentsIds());
		}

		return $parentsIds; 
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
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$categories = categoryPeer::doSelect($c);
		KalturaCriterion::enableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		
		return $categories;
	}
	
	/**
	 * @return array
	 */
	public function getAllChildren()
	{
		$c = new Criteria();
		$c->add(categoryPeer::FULL_NAME, $this->getFullName() . '%', Criteria::LIKE);
		$c->addAnd(categoryPeer::PARTNER_ID,$this->getPartnerId(),Criteria::EQUAL);
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$categories = categoryPeer::doSelect($c);
		KalturaCriterion::enableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		
		return $categories;
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

	public function getCacheInvalidationKeys()
	{
		return array("category:partnerId=".$this->getPartnerId());
	}
	
	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->setName('');
		$this->setFullName('');
		$this->setEntriesCount(0);
		$this->setDirectEntriesCount(0);
		$this->setMembersCount(0);
		$this->setPendingMembersCount(0);
		$this->setDisplayInSearch(DisplayInSearchType::PARTNER_ONLY);
		$this->setPrivacy(PrivacyType::ALL);
		$this->setInheritanceType(InheritanceType::MANUAL);
		$this->setUserJoinPolicy(UserJoinPolicyType::NOT_ALLOWED);
		$this->setDefaultPermissionLevel(CategoryKuserPermissionLevel::MODERATOR);
		$this->setContributionPolicy(ContributionPolicyType::MODERATOR);
		$this->setStatus(CategoryStatus::ACTIVE);
	}
	
	
	/**
	 * Get the [id] column value.
	 * 
	 * @return     int
	 */
	public function getIntId()
	{
		return $this->getId();
	}
	
	//TODO - remove this function when changing sphinx_log model from entryId to objectId and objectType
	public function getEntryId()
	{
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IIndexable::getObjectIndexName()
	 */
	public function getObjectIndexName()
	{
		return categoryPeer::getOMClass(false);
	}
	
	/**
	 * 
	 * return comma seperated string of kusers ids that are active members on this category. 
	 */	
	public function getMembers()
	{
		//TODO - retrieve with pagers
		$categoryIdToGetAllMembers = $this->getId();
		$inheritedParentId = $this->getInheritedParentId();
		if($inheritedParentId)
			$categoryIdToGetAllMembers = $inheritedParentId;
		
		$members = categoryKuserPeer::retrieveActiveKusersByCategoryId($categoryIdToGetAllMembers);
		if (!$members)
			return '';
		
		$membersIds = array();
		foreach ($members as $member)
		{
			$membersIds[] = $member->getKuserId();
		}
		
		return implode(',', $membersIds);
	}
	
/* (non-PHPdoc)
	 * @see IIndexable::getIndexFieldsMap()
	 */
	public function getIndexFieldsMap()
	{
		return array(
		/*sphinx => propel */
			'category_id' => 'id',
			'partner_id' => 'partnerId',
			'name' => 'name',
			'full_name' => 'fullName',
			'description' => 'description',
			'tags' => 'tags',
			'category_status' => 'status',
			'kuser_id' => 'kuserId',
			'display_in_search' => 'displayInSearch',	
			'depth' => 'depth',
			'reference_id' => 'referenceId',
			'privacy_context' => 'privacyContext',
			'privacy_contexts' => 'privacyContexts',
			'members_count' => 'membersCount',
			'pending_members_count' => 'pendingMembersCount',
			'members' => 'members',
			'entries_count' => 'entriesCount',
			'direct_entries_count' => 'directEntriesCount',
			'privacy' => 'privacy',
			'inheritance_type' => 'inheritanceType',
			'user_join_policy' => 'userJoinPolicy',
			'default_permission_level' => 'defaultPermissionLevel',
			'contribution_policy' => 'contributionPolicy',
			'inherited_parent_id' => 'inheritedParentId',
			'created_at' => 'createdAt',
			'updated_at' => 'updatedAt',
			'deleted_at' => 'deletedAt');		
	}
	
	/**
	 * @return string field type, string, int or timestamp
	 */
	public function getIndexFieldType($field)
	{
		if(isset(self::$indexFieldTypes[$field]))
			return self::$indexFieldTypes[$field];
			
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/om/Basecategory#postInsert()
	 */
	public function postInsert(PropelPDO $con = null)
	{	
		parent::postInsert($con);
	
		if (!$this->alreadyInSave)
			kEventsManager::raiseEvent(new kObjectAddedEvent($this));
	}
	
	public function getInheritFromParentCategory()
	{
		$parentCategory = $this->getParentCategory();
		if ($this->getInheritanceType() == InheritanceType::INHERIT)
			return $parentCategory->getInheritedParentId();
			
		return $parentCategory->getId();
	}
	
	
	public function getInheritParent()
	{
		$inheritCategory = categoryPeer::retrieveByPK($this->getInheritedParentId());
		if(!$inheritCategory)
			throw new kCoreException('Invalid inherited parent categroy id for category id [' . $this->getId() . ']');
			
		return $inheritCategory;
	}
	
	/*
	 * to be used when removing inheritance
	 */
	public function copyInheritedFields(category $oldParentCategory)
	{			
		$this->setUserJoinPolicy($oldParentCategory->getUserJoinPolicy());
		$this->setDefaultPermissionLevel($oldParentCategory->getDefaultPermissionLevel());
		$this->setKuserId($oldParentCategory->getKuserId());
		$this->setContributionPolicy($oldParentCategory->getContributionPolicy());
		$this->setMembersCount(0); //removing all members from this category
		$this->setPendingMembersCount(0);
	}
	
	/**
	 * inherited values are not synced in the DB to child category that inherit from them - but should be returned on the object.
	 * (values are copied upon update inhertance from inherited to manual)
	 */
	public function getUserJoinPolicy()
	{
		if ($this->getInheritanceType() == InheritanceType::INHERIT)
		{
			$inheritCategory = $this->getInheritParent();
			return $inheritCategory->getUserJoinPolicy();
		}
		else
			return parent::getUserJoinPolicy();
	}
	
	/**
	 * inherited values are not synced in the DB to child category that inherit from them - but should be returned on the object.
	 * (values are copied upon update inhertance from inherited to manual)
	 */
	public function getDefaultPermissionLevel()
	{
		if ($this->getInheritanceType() == InheritanceType::INHERIT)
			return $this->getInheritParent()->getDefaultPermissionLevel();
		else
			return parent::getDefaultPermissionLevel();
	}
	
	/**
	 * inherited values are not synced in the DB to child category that inherit from them - but should be returned on the object.
	 * (values are copied upon update inhertance from inherited to manual)
	 */
	public function getKuserId()
	{
		if ($this->getInheritanceType() == InheritanceType::INHERIT)
			return $this->getInheritParent()->getKuserId();
		else
			return parent::getKuserId();
	}
	
	/**
	 * inherited values are not synced in the DB to child category that inherit from them - but should be returned on the object.
	 * (values are copied upon update inhertance from inherited to manual)
	 */
	public function getContributionPolicy()
	{
		if ($this->getInheritanceType() == InheritanceType::INHERIT)
			return $this->getInheritParent()->getContributionPolicy();
		else
			return parent::getContributionPolicy();
	}
	
	/**
	 * inherited values are not synced in the DB to child category that inherit from them - but should be returned on the object.
	 * (values are copied upon update inhertance from inherited to manual)
	 */
	public function getMembersCount()
	{
		if ($this->getInheritanceType() == InheritanceType::INHERIT)
			return $this->getInheritParent()->getMembersCount();
		else
			return parent::getMembersCount();
	}
	
	/**
	 * inherited values are not synced in the DB to child category that inherit from them - but should be returned on the object.
	 * (values are copied upon update inhertance from inherited to manual)
	 */
	public function getPendingMembersCount()
	{
		if ($this->getInheritanceType() == InheritanceType::INHERIT)
			return $this->getInheritParent()->getPendingMembersCount();
		else
			return parent::getPendingMembersCount();
	}
	
	/**
	 * If Category inherit settings, and inherited parent category is currently being updated, this category should be in status updating.
	 */
	public function getStatus()
	{
		if ($this->getInheritanceType() == InheritanceType::INHERIT && $this->getInheritParent()->getStatus() == CategoryStatus::UPDATING)
			return CategoryStatus::UPDATING;
		else
			return parent::getContributionPolicy();
	}
	
	/**
	 * If Category inherit settings, there is nothing to sync to child categories, and therefore, should be change status to updating.
	 * @see lib/model/om/Basecategory#setStatus()
	 */
	public function setStatus($v)
	{
		if ($this->getInheritanceType() == InheritanceType::INHERIT && $v == CategoryStatus::UPDATING)
			return;
		
		return parent::setStatus($v);
	}
	
	/**
	 * Set the value of [inheritance] column.
	 * 
	 * @param      int $v new value
	 * @return     category The current object (for fluent API support)
	 */
	public function setInheritanceType($v)
	{
		$this->old_inheritance_type = $this->getInheritanceType();
		if ($v == InheritanceType::INHERIT)
			$this->setInheritedParentId($this->getInheritFromParentCategory());
		else
			$this->setInheritedParentId(null);
		
		parent::setInheritanceType($v);
	}
	
	public function setInheritedParentId($v)
	{
		$this->inherited_parent_category = null;
		if (!is_null($v))
			$this->inherited_parent_category = categoryPeer::retrieveByPK($v);
		
		parent::setInheritedParentId($v);
	}
	
	public function getInheritedParentId()
	{
		$inheritedParentId = parent::getInheritedParentId();
		$inheritedParentCategory = categoryPeer::retrieveByPK($inheritedParentId);

		if($inheritedParentCategory && ($inheritedParentCategory->getInheritanceType() == InheritanceType::INHERIT))
			return $inheritedParentCategory->getInheritedParentId();

		return $inheritedParentId;
	}
	
	public function setPuserId($puserId)
	{
		if ( self::getPuserId() == $puserId )  // same value - don't set for nothing 
			return;

		parent::setPuserId($puserId);
			
		$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $puserId);
		if (!$kuser)
			throw new kCoreException('Invalid user id', kCoreException::INVALID_USER_ID);
			
		$this->setKuserId($kuser->getId());
	}
	
	public function setPrivacyContext($v)
	{
		if (!$this->getParentId())
		{
			$this->setPrivacyContexts($v);
			parent::setPrivacyContext($v);
			return;
		}

		$parentCategory = $this->getParentCategory();
		$privacyContext = explode(',', $parentCategory->getPrivacyContexts());
		$privacyContext[] = $v;
		
		$this->setPrivacyContexts(implode(',', $privacyContext));
		parent::setPrivacyContext($v);
	}
}
