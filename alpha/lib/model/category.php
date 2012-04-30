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
	
	protected $is_index = false;
	
	protected $move_entries_to_parent_category = null;
	
	const CATEGORY_ID_THAT_DOES_NOT_EXIST = 0;
	
	const MAX_NUMBER_OF_MEMBERS_TO_BE_INDEXED_ON_ENTRY = 10;
	
	private static $indexFieldTypes = array(
		'category_id' => IIndexable::FIELD_TYPE_INTEGER,
		'parent_id' => IIndexable::FIELD_TYPE_INTEGER,
		'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
		'name' => IIndexable::FIELD_TYPE_STRING,
		'full_name' => IIndexable::FIELD_TYPE_STRING,
		'full_ids' => IIndexable::FIELD_TYPE_STRING,
		'sort_name' => IIndexable::FIELD_TYPE_INTEGER,
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
		'deleted_at' => IIndexable::FIELD_TYPE_DATETIME,
		'partner_sort_value' => IIndexable::FIELD_TYPE_INTEGER,
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
			KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);

			$chunkedCategoryLoadThreshold = kConf::get('kmc_chunked_category_load_threshold');
			if ($numOfCatsForPartner >= $chunkedCategoryLoadThreshold)
				PermissionPeer::enableForPartner(PermissionName::DYNAMIC_FLAG_KMC_CHUNKED_CATEGORY_LOAD, PermissionType::SPECIAL_FEATURE);

			if ($this->getParentId() && ($this->getPrivacyContext() == '' || $this->getPrivacyContext() == null))
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
		if (!$this->getIsIndex() && ($this->isNew() || $this->isColumnModified(categoryPeer::PARENT_ID)))
		{
			if ($this->getParentId() !== 0 && $this->getParentId() != null)
			{
				$parentCat = $this->getParentCategory();
				$this->setDepth($parentCat->getDepth() + 1);
			}
			else
			{
				$this->setDepth(0);
			}
		}
		
		//TODO - lock machnizem
		if (!$this->isNew() && $this->isColumnModified(categoryPeer::PARENT_ID))
		{
			//lock category when parent id is changed.
			$this->setCategoryFullIds();
		}
		
		if (!$this->getIsIndex() && 
			($this->isColumnModified(categoryPeer::NAME) || 
			$this->isColumnModified(categoryPeer::PARENT_ID)))
		{
			$this->updateFullName();
		}

		//index + update categoryEntry
		if (!$this->isNew() &&
			($this->isColumnModified(categoryPeer::FULL_IDS) ||
			$this->isColumnModified(categoryPeer::PARENT_ID)))
		{
			$this->addIndexCategoryEntryByFullIdJob($this->getId(),true);
		}

		//index + update category
		if (!$this->isNew() &&
			($this->isColumnModified(categoryPeer::PARENT_ID) || 
			 $this->isColumnModified(categoryPeer::INHERITANCE_TYPE) ||
			 $this->isColumnModified(categoryPeer::FULL_NAME)))
		{
			$this->addIndexCategoryJob($this->getFullIds(), null, true);			
		}
		
		// save the childs 
		foreach($this->childs_for_save as $child)
		{
			//TODO - REMOVE!
			throw new Exception('shouldnt get here');
			$child->save();
		}
		$this->childs_for_save = array();
		
		if ($this->isColumnModified(categoryPeer::DELETED_AT) && $this->getDeletedAt() !== null)
		{
			// delete all categoryKuser objects for this category
			if($this->getInheritanceType() == InheritanceType::MANUAL)
				$this->addDeleteCategoryKuserJob($this->getId());
			
			if($this->getParentId())
			{
				$this->addMoveEntriesToCategoryJob($this->move_entries_to_parent_category);
			}else{
				$this->addDeleteCategoryEntryJob($this->getId());
			}
		}
		
		if (!$this->isNew() &&
			$this->isColumnModified(categoryPeer::INHERITANCE_TYPE))
		{ 
			if ($this->inheritance_type == InheritanceType::MANUAL && 
				$this->old_inheritance_type == InheritanceType::INHERIT)
			{
				if($this->old_parent_id)
					$categoryToCopyInheritedFields = categoryPeer::retrieveByPK($this->old_parent_id);
				if($categoryToCopyInheritedFields)
					$this->copyInheritedFields($categoryToCopyInheritedFields);
					
				$this->reSetMembersCount();
			}
			elseif ($this->inheritance_type == InheritanceType::INHERIT && 
					$this->old_inheritance_type == InheritanceType::MANUAL){
				$this->addDeleteCategoryKuserJob($this->getId());
			}
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
	}
	
	private function addRecalcCategoriesCount($categoryId)
	{
		$oldParentCat = categoryPeer::retrieveByPK($categoryId);
		
		if(!$oldParentCat)
			return;
		
		$parentsCategories = explode(categoryPeer::CATEGORY_SEPARATOR, $oldParentCat->getFullIds());
		$this->addIndexCategoryJob(null, $parentsCategories, true);
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
				
		$categoryGroupSize = category::MAX_NUMBER_OF_MEMBERS_TO_BE_INDEXED_ON_ENTRY;
		$partner = $this->getPartner();
		if($partner && $partner->getCategoryGroupSize())
			$categoryGroupSize = $partner->getCategoryGroupSize();	
		//re-index entries
		if ($this->isColumnModified(categoryPeer::INHERITANCE_TYPE) || 
			$this->isColumnModified(categoryPeer::PRIVACY) || 
			$this->isColumnModified(categoryPeer::PRIVACY_CONTEXTS) || 
			$this->isColumnModified(categoryPeer::FULL_NAME) || 
			($this->isColumnModified(categoryPeer::MEMBERS_COUNT) && 
			$this->members_count <= $categoryGroupSize && 
			$this->entries_count <= entry::CATEGORY_ENTRIES_COUNT_LIMIT_TO_BE_INDEXED))
		{
			$this->addIndexEntryJob($this->getId(), true);
		}
		
		if($this->isColumnModified(categoryPeer::PARENT_ID))
		{
			$this->addRecalcCategoriesCount($this->getId());
			$this->addRecalcCategoriesCount($this->old_parent_id);
		}

		// check if parnet is deleted and could be purged
		if ($this->isColumnModified(categoryPeer::STATUS) && $this->getStatus() == CategoryStatus::PURGED)
		{
			$parentCategory = $this->getParentCategory();
			//TODO - all logic for purge is not right
			if($parentCategory && $parentCategory->isReadyForPurge())
			{
				$parentCategory->setStatus(CategoryStatus::PURGED);
				$parentCategory->save();
			}
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
				
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$category = categoryPeer::getByFullNameExactMatch($fullName);
		KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		
		if ($category)
			throw new kCoreException("Duplicate category: $fullName", kCoreException::DUPLICATE_CATEGORY);
	}
	
	public function setDeletedAt($v, $moveEntriesToParentCategory = null)
	{
		$this->move_entries_to_parent_category = $moveEntriesToParentCategory;

		if(is_null($moveEntriesToParentCategory))
		{
			//TODO - LOCK CATEGORY!
			$moveEntriesToParentCategory = $this->getId();
			$this->move_entries_to_parent_category = $moveEntriesToParentCategory;
		}
		
		$this->loadChildsForSave();
		foreach($this->childs_for_save as $child)
		{
			$child->setDeletedAt($v, $moveEntriesToParentCategory);
		}
		$this->setStatus(CategoryStatus::DELETED);
		parent::setDeletedAt($v);
		$this->save();
	}
	
	public function getRootCategoryFromFullIds($category)
	{		
		if ($category->getParentId() == null)
			return null; 	
			
		$fullIds = explode(categoryPeer::CATEGORY_SEPARATOR, $category->getFullIds());
		
		//TODO - disable tag for unlisted categories
		return categoryPeer::retrieveByPK($fullIds[0]); 
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
	
	public function setCategoryFullIds()
	{
		$parentCat = $this->getParentCategory();
		
		if ($parentCat)
		{
			$this->setFullIds($parentCat->getFullIds() . categoryPeer::CATEGORY_SEPARATOR . $this->getId());
		}
		else
		{
			$this->setFullIds($this->getId());
		}
	}
	
	private function addDeleteCategoryKuserJob($categoryId)
	{
		$filter = new categoryKuserFilter();
		$filter->setCategoryIdEqual($categoryId);

		kJobsManager::addDeleteJob($this->getPartnerId(), DeleteObjectType::CATEGORY_USER, $filter);
	}
	
	private function addDeleteCategoryEntryJob($categoryId)
	{
		$filter = new categoryEntryFilter();
		$filter->setCategoryIdEqaul($categoryId);

		kJobsManager::addDeleteJob($this->getPartnerId(), DeleteObjectType::CATEGORY_ENTRY, $filter);
	}
		
	private function addIndexEntryJob($categoryId, $shouldUpdate = false)
	{
		$filter = new entryFilter();
		$filter->setCategoriesIdsMatchAnd($categoryId);
		
		$statusArr = array(entryStatus::BLOCKED, 
						   entryStatus::ERROR_CONVERTING, 
						   entryStatus::ERROR_IMPORTING, 
						   entryStatus::IMPORT, 
						   entryStatus::MODERATE, 
						   entryStatus::NO_CONTENT, 
						   entryStatus::PENDING, 
						   entryStatus::PRECONVERT, 
						   entryStatus::READY);
		
		$filter->setStatusIn($statusArr);
			
		//TODO - add batch job size after sharon commits her code.		
		kJobsManager::addIndexJob($this->getPartnerId(), IndexObjectType::ENTRY, $filter, $shouldUpdate);
	}
	
	private function addMoveEntriesToCategoryJob($destCategoryId)
	{
		kJobsManager::addMoveCategoryEntriesJob(null, $this->getPartnerId(), $this->getId(), $destCategoryId);
	}
	
	private function addIndexCategoryJob($fullIdsStartsWithCategoryId, $categoriesIdsIn, $shouldUpdate = false)
	{
		$filter = new categoryFilter();
		$filter->setFullIdsStartsWith($fullIdsStartsWithCategoryId);
		$filter->setIdIn($categoriesIdsIn);

		kJobsManager::addIndexJob($this->getPartnerId(), IndexObjectType::CATEGORY, $filter, $shouldUpdate);
	}

	private function addIndexCategoryEntryByFullIdJob($categoryId = null, $shouldUpdate = false)
	{
		$filter = new categoryEntryFilter();
		$filter->setCategoryIdEqaul($categoryId);

		kJobsManager::addIndexJob($this->getPartnerId(), IndexObjectType::CATEGORY_ENTRY, $filter, $shouldUpdate);
	}
	
	/**
	 * Validate recursivly that the new parent id is not one of the child categories
	 * 
	 * @param int $parentId
	 */
	public function validateParentIdIsNotChild($parentId)
	{
		if ($this->getId() == $parentId && $parentId != 0)
			throw new kCoreException("Parent id [$parentId] is one of the childs", kCoreException::PARENT_ID_IS_CHILD);
		
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
		KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		
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
		KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		
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
	 * @return int sorting value
	 */
	public function getSortName()
	{
		return kUTF8::str2int64($this->getName());
	}
	
	/* (non-PHPdoc)
	 * @see IIndexable::getIntId()
	 */
	public function getIntId()
	{
		return $this->getId();
	}
	
	/* (non-PHPdoc)
	 * @see IIndexable::getEntryId()
	 * 
	 */
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
			'parent_id' => 'parentId',
			'partner_id' => 'partnerId',
			'name' => 'name',
			'full_name' => 'fullName',
			'full_ids' => 'fullIds',
			'sort_name' => 'sortName',
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
			'deleted_at' => 'deletedAt',
			'partner_sort_value' => 'partnerSortValue',
		);		
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

		if($this->getFullIds() == null)
		{
			$this->setCategoryFullIds();
			parent::save();
		}
			
		if (!$this->alreadyInSave)
			kEventsManager::raiseEvent(new kObjectAddedEvent($this));
	}
	
	/**
	 * Indicates that the category is deleted and could be purged
	 * @return boolean
	 */
	public function isReadyForPurge()
	{
		
		if(
			$this->getStatus() != CategoryStatus::DELETED || 
			$this->getMembersCount() > 0 || 
			$this->getEntriesCount() > 0
		)
			return false;
		
		$criteria = KalturaCriteria::create(categoryPeer::OM_CLASS);
		$criteria->add(categoryPeer::PARENT_ID, $this->getId());
		$criteria->applyFilters();
		$childCategories = $criteria->getRecordsCount();
		
		return ($childCategories == 0);
	}
	
	/* (non-PHPdoc)
	 * @see Basecategory::preUpdate()
	 */
	public function preUpdate(PropelPDO $con = null)
	{
		if (!$this->alreadyInSave)
		{
			// when the category is deleted and has no entries and no members, it could be purged
			if($this->isReadyForPurge())
				$this->setStatus(CategoryStatus::PURGED);
		}
		
		return parent::preUpdate($con);
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
		$this->reSetMembersCount(); //removing all members from this category
		$this->reSetPendingMembersCount();
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

	
	/**
	 * @param int $v
	 */
	public function setPartnerSortValue($v)
	{
		$this->putInCustomData("partnerSortValue", $v);
	}
	
	/**
	 * @return int
	 */
	public function getPartnerSortValue()
	{
		return (int)$this->getFromCustomData("partnerSortValue");
	}
	
	/**
	 * @param string $v
	 */
	public function setPartnerData($v)
	{
		$this->putInCustomData("partnerData", $v);
	}
	
	/**
	 * @return string
	 */
	public function getPartnerData()
	{
		return $this->getFromCustomData("partnerData");
	}
	
	/**
	 * @param string $v
	 */
	public function setDefaultOrderBy($v)
	{
		$this->putInCustomData("defaultOrderBy", $v);
	}
	
	/**
	 * @return string
	 */
	public function getDefaultOrderBy()
	{
		return $this->getFromCustomData("defaultOrderBy");
	}
	
	/**
	 * reset category's Depth by calculate it.
	 * depth should be calculated after full ids is calculated.
	 */
	public function reSetDepth()
	{		
		$this->setDepth(substr_count($this->getFullIds(), categoryPeer::CATEGORY_SEPARATOR));
	}
		
	/**
	 * reset category's full Name by calculate it.
	 */
	public function reSetFullName()
	{
		$this->setFullName($this->getActuallFullName());
	}
	
	private function getActuallFullName()
	{
		if (!$this->getParentId())
			return $this->getName();
			
			
		return $this->getParentCategory()->getActuallFullName() . categoryPeer::CATEGORY_SEPARATOR . $this->getName();
	}
	
	/**
	 * reset category's inherited parent id by calculate it.
	 */
	public function reSetInheritedParentId()
	{
		$this->setInheritedParentId($this->getActuallInheritedParentId());
	}
	
	private function getActuallInheritedParentId()
	{
		if (!$this->getParentId())
			return $this->getId();
		
		return $this->getParentCategory()->getActuallInheritedParentId() . categoryPeer::CATEGORY_SEPARATOR . $this->getId();
	}
	
	/**
	 * reset category's entriesCount by calculate it.
	 */
	public function reSetEntriesCount()
	{
		$criteria = KalturaCriteria::create(categoryEntryPeer::OM_CLASS);
		$criteria->addAnd(categoryEntryPeer::CATEGORY_FULL_IDS, $this->getFullIds() . '%', Criteria::LIKE);
		$count = categoryEntryPeer::doCount($criteria);

		$this->setEntriesCount($count);
		$this->save();
	}
	
	/**
	 * reset category's full ids by calculate it.
	 */
	public function reSetFullIds()
	{
		$this->setCategoryFullIds();
	}
	
	/**
	 * reset category's membersCount by calculate it.
	 */
	public function reSetMembersCount()
	{
		if($this->getInheritanceType() == InheritanceType::INHERIT)
		{
			$this->setMembersCount($this->getInheritParent()->getMembersCount());
		}
		else
		{
			$criteria = KalturaCriteria::create(categoryKuserPeer::OM_CLASS);
			$criteria->addAnd(categoryKuserPeer::CATEGORY_ID, $this->getId(), Criteria::EQUAL);
			$criteria->addAnd(categoryKuserPeer::STATUS, CategoryKuserStatus::ACTIVE, Criteria::EQUAL);
			$this->setMembersCount(categoryKuserPeer::doCount($criteria));
		}
	}
	
	/**
	 * reset category's pendingMembersCount by calculate it.
	 */
	public function reSetPendingMembersCount()
	{
		if($this->getInheritanceType() == InheritanceType::INHERIT)
		{
			$this->setPendingMembersCount($this->getInheritParent()->getPendingMembersCount());
		}
		else
		{
			$criteria = KalturaCriteria::create(categoryKuserPeer::OM_CLASS);
			$criteria->addAnd(categoryKuserPeer::CATEGORY_ID, $this->getId(), Criteria::EQUAL);
			$criteria->addAnd(categoryKuserPeer::STATUS, CategoryKuserStatus::PENDING, Criteria::EQUAL);
			$this->setPendingMembersCount(categoryKuserPeer::doCount($criteria));
		}
	}
	
	public function setBulkUploadId ( $bulkUploadId )	{		$this->putInCustomData ( "bulk_upload_id" , $bulkUploadId );	}
	public function getBulkUploadId (  )	{		return $this->getFromCustomData( "bulk_upload_id" );	}
	
	/*
	 * to be set when category is indexing - recalculating inheritance fields.
	 */
	public function setIsIndex($v)
	{
		$this->is_index = $v;
	}
	
	/*
	 * if category is reindexing - recalculating inheritance fields.
	 * no need to add all batch job, 
	 * because some of the batch jobs are already done by the parent category.
	 */
	protected function getIsIndex()
	{
		return $this->is_index;
	}
}
