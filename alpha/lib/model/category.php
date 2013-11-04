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
	
	protected $old_full_name = "";
	
	protected $old_parent_id = null;
	
	protected $old_inheritance_type = null;
	
	protected $is_index = false;
	
	protected $move_entries_to_parent_category = null;
	
	const CATEGORY_ID_THAT_DOES_NOT_EXIST = 0;
	
	const FULL_NAME_EQUAL_MATCH_STRING = 'fullnameequalmatchstring';
	
	const FULL_IDS_EQUAL_MATCH_STRING = 'fullidsequalmatchstring';
	
	private static $indexFieldsMap = null;
	private static $indexNullableFields = null;	
	private static $indexFieldTypes = null;
	
	public function save(PropelPDO $con = null)
	{
		if ($this->isNew())
		{
			$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
			
			$c = KalturaCriteria::create(categoryPeer::OM_CLASS);
			$c->add (categoryPeer::STATUS, CategoryStatus::DELETED, Criteria::NOT_EQUAL);
			$c->add (categoryPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
			
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
		
		if (!$this->getIsIndex() &&
			($this->isColumnModified(categoryPeer::NAME) ||
			$this->isColumnModified(categoryPeer::PARENT_ID)))
		{
			$this->updateFullName();
		}
		
		if(trim($this->getPrivacyContexts()) == '')
			$this->setDefaultUnEntitlmenetCategory();
		
		// set the depth of the parent category + 1
		if (!$this->getIsIndex() && ($this->isNew() || $this->isColumnModified(categoryPeer::PARENT_ID)))
		{
			$this->reSetDepth();
		}
		
		if (!$this->isNew() && $this->isColumnModified(categoryPeer::PARENT_ID))
		{
			$this->resetFullIds();
		}
		
		//index + update categoryEntry
		if (!$this->isNew() &&
			($this->isColumnModified(categoryPeer::FULL_IDS) ||
			$this->isColumnModified(categoryPeer::PARENT_ID)))
		{
			$this->addIndexCategoryEntryJob($this->getId());
			$this->addIndexCategoryKuserJob($this->getId());
		}
		
		if (!$this->isNew() && $this->getInheritanceType() !== InheritanceType::INHERIT &&
			$this->isColumnModified(categoryPeer::PRIVACY_CONTEXTS))
		{
			if ($this->getPrivacyContexts() == '')
			{
				$this->addDeleteCategoryKuserJob($this->getId());
			}
		}
		
		// save the childs for action category->delete - delete category is not done by async batch.
		foreach($this->childs_for_save as $child)
		{
			$child->save();
		}
		
		$this->childs_for_save = array();
		
		if ($this->isColumnModified(categoryPeer::DELETED_AT) && $this->getDeletedAt() !== null)
		{
			// delete all categoryKuser objects for this category
			if($this->getInheritanceType() == InheritanceType::MANUAL)
				$this->addDeleteCategoryKuserJob($this->getId());
			
			if($this->move_entries_to_parent_category)
			{
				$this->addMoveEntriesToCategoryJob($this->move_entries_to_parent_category);
			}
			elseif($this->move_entries_to_parent_category === 0)
			{
				$this->addDeleteCategoryEntryJob($this->getId());
			}
		}
		
		$kuserChanged = false;
		if (!$this->isNew() &&
			$this->isColumnModified(categoryPeer::INHERITANCE_TYPE))
		{
			if ($this->inheritance_type == InheritanceType::MANUAL &&
				$this->old_inheritance_type == InheritanceType::INHERIT)
			{
				if($this->parent_id)
					$categoryToCopyInheritedFields = $this->getInheritParent();
				if($categoryToCopyInheritedFields)
				{
					$this->copyInheritedFields($categoryToCopyInheritedFields);
					$kuserChanged = true;
				}	
				$this->reSetMembersCount();
			}
			elseif ($this->inheritance_type == InheritanceType::INHERIT &&
					$this->old_inheritance_type == InheritanceType::MANUAL)
			{
				$this->addDeleteCategoryKuserJob($this->getId(), true);
			}
		}
		
		if ($this->isColumnModified(categoryPeer::KUSER_ID))
			$kuserChanged = true;

		if (!$this->isNew() && $this->isColumnModified(categoryPeer::PRIVACY) && $this->getPrivacy() == PrivacyType::MEMBERS_ONLY)
		{
			$this->removeNonMemberKusers ();
		}

		parent::save($con);
		
		if ($kuserChanged && $this->inheritance_type != InheritanceType::INHERIT && $this->getKuserId())
		{
			$categoryKuser = categoryKuserPeer::retrieveByCategoryIdAndKuserId($this->getId(), $this->getKuserId());
			if (!$categoryKuser)
			{
				$categoryKuser = new categoryKuser();
				$categoryKuser->setCategoryId($this->getId());
				$categoryKuser->setCategoryFullIds($this->getFullIds());
				$categoryKuser->setKuserId($this->getKuserId());
			}
			
			$categoryKuser->setPermissionLevel(CategoryKuserPermissionLevel::MANAGER);
			$permissionNamesArr = array();
			if ($categoryKuser->getPermissionNames())
			{
					$permissionNamesArr = explode(",", $categoryKuser->getPermissionNames());
			}
			$permissionNamesArr = categoryKuser::removeCategoryPermissions($permissionNamesArr);
			$permissionNamesArr[] = PermissionName::CATEGORY_CONTRIBUTE;
			$permissionNamesArr[] = PermissionName::CATEGORY_EDIT;
			$permissionNamesArr[] = PermissionName::CATEGORY_MODERATE;
			$permissionNamesArr[] = PermissionName::CATEGORY_VIEW;
			$categoryKuser->setPermissionNames(implode(",", $permissionNamesArr));
			$categoryKuser->setStatus(CategoryKuserStatus::ACTIVE);
			$categoryKuser->setPartnerId($this->getPartnerId());
			$categoryKuser->setUpdateMethod(UpdateMethodType::MANUAL);
			$categoryKuser->save();
			
			$this->indexToSearchIndex();
		}
	}
	
	private function removeNonMemberKusers ()
	{
		$filter = new categoryKuserFilter();
		$filter->setCategoryIdEqual($this->getId());
		$filter->set('_notcontains_permission_names', PermissionName::CATEGORY_CONTRIBUTE.",".PermissionName::CATEGORY_EDIT.",".PermissionName::CATEGORY_MODERATE.",".PermissionName::CATEGORY_VIEW);
		kJobsManager::addDeleteJob($this->getPartnerId(), DeleteObjectType::CATEGORY_USER, $filter);
	}
	
	
	/* (non-PHPdoc)
	 * @see IIndexable::indexToSearchIndex()
	 */
	public function indexToSearchIndex()
	{
		kEventsManager::raiseEventDeferred(new kObjectReadyForIndexEvent($this));
	}
	
	protected function addRecalcCategoriesCount($categoryId)
	{
		$oldParentCat = categoryPeer::retrieveByPK($categoryId);
		
		if(!$oldParentCat)
			return;
		
		$parentsCategories = explode(categoryPeer::CATEGORY_SEPARATOR, $oldParentCat->getFullIds());
		$this->addIndexCategoryJob(null, $parentsCategories);
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
				
		$categoryGroupSize = kConf::get('max_number_of_memebrs_to_be_indexed_on_entry');
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
			$this->entries_count <= kConf::get('category_entries_count_limit_to_be_indexed')))
		{
			$this->addIndexEntryJob($this->getId(), true);
		}
		
		$oldParentCategoryToResetSubCategories = null;
		$parentCategoryToResetSubCategories = null;
		
		if($this->isColumnModified(categoryPeer::PARENT_ID))
		{
			$this->addRecalcCategoriesCount($this->getId());
			$this->addRecalcCategoriesCount($this->old_parent_id);
			
			$oldParentCategoryToResetSubCategories = categoryPeer::retrieveByPK($this->old_parent_id);;
			$parentCategoryToResetSubCategories = $this->getParentCategory();
		}
		
		if (kCurrentContext::getCurrentPartnerId() != Partner::BATCH_PARTNER_ID &&
			($this->isColumnModified(categoryPeer::PARENT_ID) ||
			 $this->isColumnModified(categoryPeer::INHERITANCE_TYPE) ||
			 $this->isColumnModified(categoryPeer::NAME) ||
			 $this->isColumnModified(categoryPeer::PRIVACY_CONTEXTS) ||
			 $this->isColumnModified(categoryPeer::MEMBERS) ||
			 $this->isColumnModified(categoryPeer::MEMBERS_COUNT)))
		{
			$lock = false;
			if ($this->isColumnModified(categoryPeer::PARENT_ID))
				$lock = true;
			
			$fullIds = $this->getFullIds();
			if($this->isColumnModified(categoryPeer::FULL_IDS))
				$fullIds = $this->getColumnsOldValue(categoryPeer::FULL_IDS);
			$fullIds .= categoryPeer::CATEGORY_SEPARATOR;
			
			$this->addIndexCategoryJob($fullIds, null, $lock);
		}
		
		if ($this->isColumnModified(categoryPeer::STATUS) &&
			($this->getStatus() == CategoryStatus::PURGED ||
			 $this->getStatus() == CategoryStatus::DELETED) &&
			($this->getColumnsOldValue(categoryPeer::STATUS) == CategoryStatus::ACTIVE ||
			 $this->getColumnsOldValue(categoryPeer::STATUS) == CategoryStatus::UPDATING))
		{
			$parentCategoryToResetSubCategories = $this->getParentCategory();
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
			
		kEventsManager::flushEvents();
		
		if($oldParentCategoryToResetSubCategories)
		{
			$oldParentCategoryToResetSubCategories->reSetDirectSubCategoriesCount();
			$oldParentCategoryToResetSubCategories->save();
		}
		
		if($parentCategoryToResetSubCategories)
		{
			$parentCategoryToResetSubCategories->reSetDirectSubCategoriesCount();
			$parentCategoryToResetSubCategories->save();
		}
		
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
	private function entryAlreadyBlongToCategory(array $entryCategoriesIds = null)
	{
		if (!$entryCategoriesIds){
			KalturaLog::debug("No entry categories ids supplied");
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
	public function incrementEntriesCount($increase = 1, array $entryCategoriesAddedIds = null)
	{
		if($entryCategoriesAddedIds && $this->entryAlreadyBlongToCategory($entryCategoriesAddedIds))
		{
			KalturaLog::debug("Entry already blong to category");
			return;
		}
		
		$this->setEntriesCount($this->getEntriesCount() + $increase);
		if($this->getParentId())
		{
			$parentCat = $this->getParentCategory();
			if($parentCat)
			{
				$parentCat->incrementEntriesCount($increase, $entryCategoriesAddedIds);
				$parentCat->save();
			}
		}
	}
      
	/**
	 * Increment direct entries count
	 */
	public function incrementDirectEntriesCount()
	{
		$this->setDirectEntriesCount($this->getDirectEntriesCount() + 1);
	}
      
    /**
	 * Increment direct pending entries count
	 */
	public function incrementPendingEntriesCount()
	{
		$this->setPendingEntriesCount($this->getPendingEntriesCount() + 1);
		$this->save();
	}
	
	/**
	 * Decrement entries count (will decrement recursively the parent categories too)
	 */
	public function decrementEntriesCount($decrease = 1, array $entryCategoriesRemovedIds = null)
	{
		if($this->entryAlreadyBlongToCategory($entryCategoriesRemovedIds))
		{
			KalturaLog::debug("Entry already blong to category");
			return;
		}
		
		if($this->getStatus() == CategoryStatus::PURGED)
		{
			KalturaLog::debug("Category already purged at [" . $this->getDeletedAt() . "]");
			return;
		}
		
		$newCount = $this->getEntriesCount() - $decrease;
		
		if($newCount < 0)
			$newCount = 0;
		$this->setEntriesCount($newCount);
		
		if($this->getParentId())
		{
			$parentCat = $this->getParentCategory();
			if($parentCat)
			{
				$parentCat->decrementEntriesCount($decrease, $entryCategoriesRemovedIds);
				$parentCat->save();
			}
		}
	}
	
	/**
	 * Decrement direct entries count (will decrement recursively the parent categories too)
	 */
	public function decrementDirectEntriesCount()
	{
		$this->setDirectEntriesCount($this->getDirectEntriesCount() - 1);
	}
      
	/**
	* Decrement direct entries count (will decrement recursively the parent categories too)
	*/
	public function decrementPendingEntriesCount()
	{
		$this->setPendingEntriesCount($this->getPendingEntriesCount() - 1);
	}
      
	protected function validateFullNameIsUnique()
	{
		$fullName = $this->getFullName();
		$fullName = categoryPeer::getParsedFullName($fullName);
		
		$partnerId = null;
		if($this->getPartnerId() != kCurrentContext::$ks_partner_id)
			$partnerId = $this->getPartnerId();
		
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$category = categoryPeer::getByFullNameExactMatch($fullName, $this->getId(), $partnerId);
		KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		
		if ($category)
			throw new kCoreException("Duplicate category: $fullName", kCoreException::DUPLICATE_CATEGORY);
	}
	
	public function setDeletedAt($v, $moveEntriesToParentCategory = null)
	{
		if(is_null($this->move_entries_to_parent_category))
		{
			if(is_null($moveEntriesToParentCategory))
				$moveEntriesToParentCategory = $this->getParentId();
					
			$this->move_entries_to_parent_category = $moveEntriesToParentCategory;
			
			$this->loadChildsForSave();
			foreach($this->childs_for_save as $child)
			{
				$child->setDeletedAt($v, $moveEntriesToParentCategory);
				$child->save();
			}
		}
		
		$this->setStatus(CategoryStatus::DELETED);
		parent::setDeletedAt($v);
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
	
	public function reSetFullIds()
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
	
	protected function addDeleteCategoryKuserJob($categoryId, $deleteCategoryDirectMembersOnly = false)
	{
		$filter = new categoryKuserFilter();
		$filter->setCategoryIdEqual($categoryId);
		$filter->set('_category_direct_members', $deleteCategoryDirectMembersOnly);
		
		kJobsManager::addDeleteJob($this->getPartnerId(), DeleteObjectType::CATEGORY_USER, $filter);
	}
	
	protected function addCopyCategoryKuserJob($categoryId)
	{
		$templateCategory = new categoryKuser();
		$templateCategory->setCategoryId($this->getId());
		
		$filter = new categoryKuserFilter();
		$filter->setCategoryIdEqual($categoryId);

		kJobsManager::addCopyJob($this->getPartnerId(), CopyObjectType::CATEGORY_USER, $filter, $templateCategory);
	}
	
	protected function addDeleteCategoryEntryJob($categoryId)
	{
		$filter = new categoryEntryFilter();
		$filter->setCategoryIdEqual($categoryId);

		kJobsManager::addDeleteJob($this->getPartnerId(), DeleteObjectType::CATEGORY_ENTRY, $filter);
	}
		
	protected function addIndexEntryJob($categoryId, $shouldUpdate = false)
	{
		$featureStatusToRemoveIndex = new kFeatureStatus();
		$featureStatusToRemoveIndex->setType(IndexObjectType::ENTRY);
		
		$featureStatusesToRemove = array();
		$featureStatusesToRemove[] = $featureStatusToRemoveIndex;
		
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
		kJobsManager::addIndexJob($this->getPartnerId(), IndexObjectType::ENTRY, $filter, $shouldUpdate, $featureStatusesToRemove);
	}
	
	protected function addMoveEntriesToCategoryJob($destCategoryId)
	{
		kJobsManager::addMoveCategoryEntriesJob(null, $this->getPartnerId(), $this->getId(), $destCategoryId, false, false, $this->getFullIds());
	}
	
	protected function addIndexCategoryJob($fullIdsStartsWithCategoryId, $categoriesIdsIn, $lock = false)
	{
		$jobSubType = IndexObjectType::CATEGORY;
		if($lock)
		{
			$jobSubType = IndexObjectType::LOCK_CATEGORY;
			
			$featureStatusToRemoveIndex = new kFeatureStatus();
			$featureStatusToRemoveIndex->setType(IndexObjectType::LOCK_CATEGORY);
		}
		else
		{
			$featureStatusToRemoveIndex = new kFeatureStatus();
			$featureStatusToRemoveIndex->setType(IndexObjectType::CATEGORY);
		}
		
		$featureStatusesToRemove = array();
		$featureStatusesToRemove[] = $featureStatusToRemoveIndex;

		$filter = new categoryFilter();
		$filter->setFullIdsStartsWith($fullIdsStartsWithCategoryId);
		$filter->setIdIn($categoriesIdsIn);
		
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS);
		$filter->attachToCriteria($c);
			
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		categoryPeer::doSelect($c);
		KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		
		if($c->getRecordsCount() > 0)
			kJobsManager::addIndexJob($this->getPartnerId(), $jobSubType, $filter, true, $featureStatusesToRemove);
	}

	protected function addIndexCategoryEntryJob($categoryId = null, $shouldUpdate = true)
	{
		$featureStatusToRemoveIndex = new kFeatureStatus();
		$featureStatusToRemoveIndex->setType(IndexObjectType::CATEGORY_ENTRY);
		
		$featureStatusesToRemove = array();
		$featureStatusesToRemove[] = $featureStatusToRemoveIndex;
		
		$filter = new categoryEntryFilter();
		$filter->setCategoryIdEqual($categoryId);

		kJobsManager::addIndexJob($this->getPartnerId(), IndexObjectType::CATEGORY_ENTRY, $filter, $shouldUpdate, $featureStatusesToRemove);
		
	}
	
	protected function addIndexCategoryKuserJob($categoryId = null, $shouldUpdate = true)
	{
		$featureStatusToRemoveIndex = new kFeatureStatus();
		$featureStatusToRemoveIndex->setType(IndexObjectType::CATEGORY_USER);
		
		$featureStatusesToRemove = array();
		$featureStatusesToRemove[] = $featureStatusToRemoveIndex;
		
		$filter = new categoryKuserFilter();
		$filter->setCategoryIdEqual($categoryId);

		kJobsManager::addIndexJob($this->getPartnerId(), IndexObjectType::CATEGORY_USER, $filter, $shouldUpdate, $featureStatusesToRemove);
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
			$parentCategory = $this->getParentCategory();
			if ($parentCategory)
				$parentsIds = array_merge($parentsIds, $parentCategory->getAllParentsIds());
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
		return array("category:id=".$this->getId(), "category:partnerId=".$this->getPartnerId());
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
		$this->setDirectSubCategoriesCount(0);
		$this->setMembersCount(0);
		$this->setPendingMembersCount(0);
		$this->setDisplayInSearch(DisplayInSearchType::PARTNER_ONLY);
		$this->setPrivacy(PrivacyType::ALL);
		$this->setInheritanceType(InheritanceType::MANUAL);
		$this->setUserJoinPolicy(UserJoinPolicyType::NOT_ALLOWED);
		$this->setDefaultPermissionLevel(CategoryKuserPermissionLevel::MEMBER);
		$this->setContributionPolicy(ContributionPolicyType::ALL);
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
	 * Return space seperated string of permission level and kusers ids that are active members on this category.
	 * Example: "CONTRIBUTOR kuserId1 kuserId2 CONTRIBUTOR MANAGER kuserId3 kuserId4 MANAGER"
	 * Used by index engine.
	 *
	 * @return string
	 */
	public function getMembersByPermissionLevel()
	{
		$categoryIdToGetAllMembers = $this->getId();
		$inheritedParentId = $this->getInheritedParentId();
		if($inheritedParentId)
			$categoryIdToGetAllMembers = $inheritedParentId;
		
		$members = categoryKuserPeer::retrieveActiveKusersByCategoryId($categoryIdToGetAllMembers);
		if (!$members)
			return '';
		
		$membersIdsByPermission = array();
		foreach ($members as $member)
		{
			if(isset($membersIdsByPermission[$member->getPermissionLevel()]))
				$membersIdsByPermission[$member->getPermissionLevel()][] = $member->getKuserId();
			else
				$membersIdsByPermission[$member->getPermissionLevel()] = array ($member->getKuserId());
		}
		
		//Add indexed permission_names
		$permissionNamesByMembers = array();
		foreach ($members as $member)
		{
			/* @var $member categoryKuser */
			$permissionNames = explode(",", $member->getPermissionNames());
			foreach ($permissionNames as &$permissionName)
			{
				$permissionName = str_replace('_', '', $permissionName);				
			}
			$permissionNamesByMembers[] = $member->getKuserId().implode(" ".$member->getKuserId(), $permissionNames);
		}
		
		$membersIds = array();
		foreach ($membersIdsByPermission as $permissionLevel => $membersIdByPermission)
		{
			$permissionLevelByName = self::getPermissionLevelName($permissionLevel);
			$membersIds[] = $permissionLevelByName . '_' . implode(' ' . $permissionLevelByName . '_', $membersIdByPermission);
			$membersIds[] = implode(' ', $membersIdByPermission);
			$membersIds[] = implode(' ', $permissionNamesByMembers);
		}
		
		return implode(' ', $membersIds);
	}

	
	/**
	 * Return kusers ids that are active members on this category.
	 *
	 * @return array
	 */
	public function getMembers()
	{
		$categoryIdToGetAllMembers = $this->getId();
		$inheritedParentId = $this->getInheritedParentId();
		if($inheritedParentId)
			$categoryIdToGetAllMembers = $inheritedParentId;
		
		$members = categoryKuserPeer::retrieveActiveKusersByCategoryId($categoryIdToGetAllMembers);
		if (!$members)
			return array();
		
		$membersIds = array();
		foreach ($members as $member)
		{
			$membersIds[] = $member->getKuserId();
		}
		
		return $membersIds;
	}
	
	public static function getPermissionLevelName($permissionLevel)
	{
		switch ($permissionLevel)
		{
			case CategoryKuserPermissionLevel::CONTRIBUTOR:
				return 'CONTRIBUTOR';
				
			case CategoryKuserPermissionLevel::MANAGER:
				return 'MANAGER';
				
			case CategoryKuserPermissionLevel::MEMBER:
				return 'MEMBER';
				
			case CategoryKuserPermissionLevel::MODERATOR:
				return 'MODERATOR';
		}
		
		return '';
	}
	
	/* (non-PHPdoc)
	 * @see IIndexable::getIndexNullableFields()
	 */
	public static function getIndexNullableFields()
	{
		if (!self::$indexNullableFields)
		{
			self::$indexNullableFields = array(
				'description',
				'tags',
				'reference_id',
				'privacy_contexts',
				'members',
			);
		}
		
		return self::$indexNullableFields;
	}
	
	/* (non-PHPdoc)
	 * @see IIndexable::getIndexFieldsMap()
	 */
	public function getIndexFieldsMap()
	{
		if (!self::$indexFieldsMap)
		{
			self::$indexFieldsMap = array(
		/*sphinx => propel */
			'category_id' => 'id',
			'str_category_id' => 'id',
			'parent_id' => 'parentId',
			'partner_id' => 'partnerId',
			'name' => 'name',
			'full_name' => 'searchIndexfullName',
			'full_ids' => 'searchIndexfullIds',
			'description' => 'description',
			'tags' => 'tags',
			'category_status' => 'status',
			'kuser_id' => 'kuserId',
			'display_in_search' => 'displayInSearch',
			'depth' => 'depth',
			'reference_id' => 'referenceId',
			'privacy_context' => 'searchIndexprivacyContext',
			'privacy_contexts' => 'searchIndexPrivacyContexts',
			'members_count' => 'membersCount',
			'pending_members_count' => 'pendingMembersCount',
			'members' => 'membersByPermissionLevel',
			'entries_count' => 'entriesCount',
			'direct_entries_count' => 'directEntriesCount',
			'direct_sub_categories_count' => 'directSubCategoriesCount',
			'privacy' => 'privacyPartnerIdx',
			'inheritance_type' => 'inheritanceType',
			'user_join_policy' => 'userJoinPolicy',
			'default_permission_level' => 'defaultPermissionLevel',
			'contribution_policy' => 'contributionPolicy',
			'inherited_parent_id' => 'inheritedParentId',
			'created_at' => 'createdAt',
			'updated_at' => 'updatedAt',
			'deleted_at' => 'deletedAt',
			'partner_sort_value' => 'partnerSortValue',
			'sphinx_match_optimizations' => 'sphinxMatchOptimizations',
			
			);
		}
		
		return self::$indexFieldsMap;
	}
	
	/**
	 * @return string field type, string, int or timestamp
	 */
	public function getIndexFieldType($field)
	{
		if (!self::$indexFieldTypes)
		{
			self::$indexFieldTypes = array(
				'category_id' => IIndexable::FIELD_TYPE_INTEGER,
				'str_category_id' => IIndexable::FIELD_TYPE_STRING,
				'parent_id' => IIndexable::FIELD_TYPE_INTEGER,
				'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
				'name' => IIndexable::FIELD_TYPE_STRING,
				'full_name' => IIndexable::FIELD_TYPE_STRING,
				'full_ids' => IIndexable::FIELD_TYPE_STRING,
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
				'direct_sub_categories_count' => IIndexable::FIELD_TYPE_INTEGER,
				'inheritance_type' => IIndexable::FIELD_TYPE_INTEGER,
				'user_join_policy' => IIndexable::FIELD_TYPE_INTEGER,
				'default_permission_level' => IIndexable::FIELD_TYPE_INTEGER,
				'contribution_policy' => IIndexable::FIELD_TYPE_INTEGER,
				'inherited_parent_id' => IIndexable::FIELD_TYPE_INTEGER,
				'created_at' => IIndexable::FIELD_TYPE_DATETIME,
				'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
				'deleted_at' => IIndexable::FIELD_TYPE_DATETIME,
				'partner_sort_value' => IIndexable::FIELD_TYPE_INTEGER,
				'sphinx_match_optimizations' => IIndexable::FIELD_TYPE_STRING,
			);
		}
		
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
			$this->reSetFullIds();
			
			parent::save();
		}
		if (!$this->alreadyInSave)
			kEventsManager::raiseEvent(new kObjectAddedEvent($this));

		kEventsManager::flushEvents();
			
		if($this->getParentCategory())
		{
			$parentCategory = $this->getParentCategory();
			
			if($parentCategory)
			{
				$parentCategory->reSetDirectSubCategoriesCount();
				$parentCategory->save();
			}
		}
	}
	
	/**
	 * Indicates that the category is deleted and could be purged
	 * @return boolean
	 */
	public function isReadyForPurge()
	{
		if($this->getStatus() != CategoryStatus::DELETED)
			return false;
			
		if($this->getMembersCount())
		{
			KalturaLog::debug("Category still associated with [" . $this->getMembersCount() . "] users");
			return false;
		}
			
		if($this->getEntriesCount() > 0)
		{
			KalturaLog::debug("Category still associated with [" . $this->getEntriesCount() . "] entries");
			return false;
		}
			
		if($this->getDirectSubCategoriesCount() > 0)
		{
			KalturaLog::debug("Category still associated with [" . $this->getDirectSubCategoriesCount() . "] sub categories");
			return false;
		}
		
		return true;
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
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$parentCategory = $this->getParentCategory();
		KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		
		if(!$parentCategory)
			return null;
		
		if ($parentCategory->getInheritanceType() == InheritanceType::INHERIT)
			return $parentCategory->getInheritedParentId();
			
		return $parentCategory->getId();
	}
	
	private function getInheritParent()
	{
		if ($this->getInheritanceType() != InheritanceType::INHERIT || is_null($this->getInheritedParentId()))
			return $this;
			
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$inheritCategory = categoryPeer::retrieveByPK($this->getInheritedParentId());
		KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		
		if(!$inheritCategory)
			return $this;
			
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
		$this->setPuserId($oldParentCategory->getPuserId());
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
			$inheritPartner = $this->getInheritParent();
			if($inheritPartner->getId() != $this->getId())
				return $this->getInheritParent()->getUserJoinPolicy();
		}
		
		return parent::getUserJoinPolicy();
	}
	
	public function getPrivacyPartnerIdx() {
		return self::formatPrivacy($this->getPrivacy(), $this->getPartnerId());
	}
	
	public static function formatPrivacy($privacy, $partnerId) {
		return sprintf("%sP%s", $privacy, $partnerId);
	}
	
	public function getSphinxMatchOptimizations() {
		// Please add all you sphinx specific optimizations here.
		// Should be equivalant to $sphinxOptimizationMap
		$matches = array();
		$matches[] = $this->getId();
	
		return implode(" ", $matches);
	}
	
	/**
	 * inherited values are not synced in the DB to child category that inherit from them - but should be returned on the object.
	 * (values are copied upon update inhertance from inherited to manual)
	 */
	public function getDefaultPermissionLevel()
	{
		if ($this->getInheritanceType() == InheritanceType::INHERIT)
		{
			$inheritPartner = $this->getInheritParent();
			if($inheritPartner->getId() != $this->getId())
				return $this->getInheritParent()->getDefaultPermissionLevel();
		}
		
		return parent::getDefaultPermissionLevel();
	}
	
	/**
	 * inherited values are not synced in the DB to child category that inherit from them - but should be returned on the object.
	 * (values are copied upon update inhertance from inherited to manual)
	 */
	public function getKuserId()
	{
		if ($this->getInheritanceType() == InheritanceType::INHERIT)
		{
			$inheritPartner = $this->getInheritParent();
			if($inheritPartner->getId() != $this->getId())
				return $this->getInheritParent()->getKuserId();
		}
		
		return parent::getKuserId();
	}
	
	/**
	 * inherited values are not synced in the DB to child category that inherit from them - but should be returned on the object.
	 * (values are copied upon update inhertance from inherited to manual)
	 */
	public function getPuserId()
	{
		if ($this->getInheritanceType() == InheritanceType::INHERIT)
		{
			$inheritPartner = $this->getInheritParent();
			if($inheritPartner->getId() != $this->getId())
				return $this->getInheritParent()->getPuserId();
		}
		
		return parent::getPuserId();
	}
	
	/**
	 * inherited values are not synced in the DB to child category that inherit from them - but should be returned on the object.
	 * (values are copied upon update inhertance from inherited to manual)
	 */
	public function getMembersCount()
	{
		if ($this->getInheritanceType() == InheritanceType::INHERIT)
		{
			$inheritPartner = $this->getInheritParent();
			if($inheritPartner->getId() != $this->getId())
				return $this->getInheritParent()->getMembersCount();
		}
		
		return parent::getMembersCount();
	}
	
	/**
	 * inherited values are not synced in the DB to child category that inherit from them - but should be returned on the object.
	 * (values are copied upon update inhertance from inherited to manual)
	 */
	public function getPendingMembersCount()
	{
		if ($this->getInheritanceType() == InheritanceType::INHERIT)
		{
			$inheritPartner = $this->getInheritParent();
			if($inheritPartner->getId() != $this->getId())
				return $this->getInheritParent()->getPendingMembersCount();
		}
		
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
	
	public function setPuserId($puserId)
	{
		if ( self::getPuserId() == $puserId )  // same value - don't set for nothing
			return;

		parent::setPuserId($puserId);
		if (is_null($puserId))
		{
			$this->setKuserId(null);
			return;
		}
			
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
		$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $puserId);
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
		$privacyContexts = explode(',', $parentCategory->getPrivacyContexts());
		$privacyContexts[] = $v;
		
		$privacyContextsTrimed = array();
		foreach($privacyContexts as $privacyContext)
		{
			if(trim($privacyContext) != '')
				$privacyContextsTrimed[] = trim($privacyContext);
		}

		$privacyContextsTrimed = array_unique($privacyContextsTrimed);
		
		$this->setPrivacyContexts(trim(implode(',', $privacyContextsTrimed)));
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
		if ($this->getParentId() !== 0 && $this->getParentId() != null)
		{
			$parentCat = $this->getParentCategory();
			if($parentCat)
			{
				$this->setDepth($parentCat->getDepth() + 1);
				return;
			}
		}
		
		$this->setDepth(0);
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
			
		$parentCategory = $this->getParentCategory();
		if (!$parentCategory)
			return $this->getName();
		
		return $parentCategory->getActuallFullName() . categoryPeer::CATEGORY_SEPARATOR . $this->getName();
	}
	
	/**
	 * reset category's inherited parent id by calculate it.
	 */
	public function reSetInheritedParentId()
	{
		if($this->getInheritanceType() != InheritanceType::INHERIT)
			$this->setInheritedParentId(null);
		else
			$this->setInheritedParentId($this->getActuallInheritedParentId());
	}
	
	private function getActuallInheritedParentId()
	{
		if (!$this->getParentId() || $this->getInheritanceType() != InheritanceType::INHERIT)
			return $this->getId();
		
		$parentCategory = $this->getParentCategory();
		if($parentCategory)
			return $parentCategory->getActuallInheritedParentId();
			
		return $this->getId();
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
	}
	
	/**
	 * reset category's directEntriesCount by calculate it.
	 */
	public function reSetDirectEntriesCount()
	{
		$criteria = KalturaCriteria::create(categoryEntryPeer::OM_CLASS);
		$criteria->addAnd(categoryEntryPeer::CATEGORY_ID, $this->getId());
		$count = categoryEntryPeer::doCount($criteria);

		$this->setDirectEntriesCount($count);
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
	 * reset category's priacyContexts by calculate it.
	 */
	public function reSetPrivacyContext()
	{
		$this->setPrivacyContext($this->getPrivacyContext());
		
		if($this->getPrivacyContexts() == '')
		{
			$this->setPrivacy(PrivacyType::ALL);
			$this->setDisplayInSearch(DisplayInSearchType::PARTNER_ONLY);
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
	
	/**
	 * to be set when category is indexing - recalculating inheritance fields.
	 */
	public function setIsIndex($v)
	{
		$this->is_index = $v;
	}
	
	/**
	 * if category is reindexing - recalculating inheritance fields.
	 * no need to add all batch job,
	 * because some of the batch jobs are already done by the parent category.
	 */
	protected function getIsIndex()
	{
		return $this->is_index;
	}
	
	public function copyCategoryUsersFromParent($categoryId)
	{
		$this->addCopyCategoryKuserJob($categoryId);
	}
	
	protected function setDefaultUnEntitlmenetCategory()
	{
		//default non-entitlement fields
		$this->setPrivacy(PrivacyType::ALL);
		$this->setDisplayInSearch(DisplayInSearchType::PARTNER_ONLY);
		$this->setInheritanceType(InheritanceType::MANUAL);
		$this->setKuserId(null);
		$this->setUserJoinPolicy(UserJoinPolicyType::NOT_ALLOWED);
		$this->setContributionPolicy(ContributionPolicyType::ALL);
		$this->setDefaultPermissionLevel(CategoryKuserPermissionLevel::MEMBER);
	}
	
	public function reSetDirectSubCategoriesCount()
	{
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
		
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS);
		$c->add (categoryPeer::STATUS, array(CategoryStatus::DELETED, CategoryStatus::PURGED), Criteria::NOT_IN);
		$c->add (categoryPeer::PARENT_ID, $this->getId(), Criteria::EQUAL);
			
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$c->applyFilters();
		KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$this->setDirectSubCategoriesCount($c->getRecordsCount());
	}
	
	public function getSearchIndexPrivacyContext()
	{
		if(is_null($this->getPrivacyContext()) || trim($this->getPrivacyContext()) == '')
			return '';
		
		$privacyContext = explode(',', $this->getPrivacyContext());
		$privacyContext[] = kEntitlementUtils::NOT_DEFAULT_CONTEXT;
			
		return implode(' ', $privacyContext);
	}
	
	public function getSearchIndexPrivacyContexts()
	{
		if(is_null($this->getPrivacyContexts()) || trim($this->getPrivacyContexts()) == '')
			return kEntitlementUtils::DEFAULT_CONTEXT . $this->getPartnerId();
			
		$privacyContexts = explode(',', $this->getPrivacyContexts());
			
		return implode(' ', $privacyContexts);
	}
	
	public function getSearchIndexfullName()
	{
		$fullName = $this->getFullName();
		$fullNameLowerCase = strtolower($fullName);
		
		$fullNameArr = explode(categoryPeer::CATEGORY_SEPARATOR, $fullNameLowerCase);
		
		$parsedFullName = $fullNameLowerCase. " ";
		$fullName = '';
		foreach ($fullNameArr as $categoryName)
		{
			if($fullName == '')
			{
				$fullName = $categoryName;
			}
			else
			{
				
				$parsedFullName .= md5($fullName . categoryPeer::CATEGORY_SEPARATOR) . ' ';
				$fullName .= '>' . $categoryName;
			}
			
			$parsedFullName .= md5($fullName) . ' ';
		}
		
		$parsedFullName .= md5($fullNameLowerCase . category::FULL_NAME_EQUAL_MATCH_STRING);

		return $parsedFullName;
	}
	
	public function getSearchIndexfullIds()
	{
		$fullIds = $this->getFullIds();
		$fullIdsArr = explode(categoryPeer::CATEGORY_SEPARATOR, $fullIds);
		
		$parsedFullId = '';
		$fullIds = '';
		foreach ($fullIdsArr as $categoryId)
		{
			if($fullIds == '')
			{
				$fullIds = $categoryId;
			}
			else
			{
				$parsedFullId .= md5($fullIds . categoryPeer::CATEGORY_SEPARATOR) . ' ';
				$fullIds .= '>' . $categoryId;
			}
			
			$parsedFullId .= md5($fullIds) . ' ';
		}
		
		$parsedFullId .= md5($fullIds . category::FULL_IDS_EQUAL_MATCH_STRING);
		
		return $parsedFullId ;
	}
	
	/**
	 * Force modifiedColumns to be affected even if the value not changed
	 *
	 * @see Basecategory::setUpdatedAt()
	 */
	public function setUpdatedAt($v)
	{
		parent::setUpdatedAt($v);
		if(!in_array(categoryPeer::UPDATED_AT, $this->modifiedColumns, false))
			$this->modifiedColumns[] = categoryPeer::UPDATED_AT;
			
		return $this;
	}
	
	public static $sphinxFieldsEscapeType = array(
		'full_ids' => SearchIndexFieldEscapeType::NO_ESCAPE,
	);
	
	public function getSearchIndexFieldsEscapeType($fieldName)
	{
		if(!isset(self::$sphinxFieldsEscapeType[$fieldName]))
			return SearchIndexFieldEscapeType::DEFAULT_ESCAPE;
			
		return self::$sphinxFieldsEscapeType[$fieldName];
	}
}
