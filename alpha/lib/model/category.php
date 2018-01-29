<?php

/**
 * Subclass for representing a row from the 'category' table.
 *
 *
 *
 * @package Core
 * @subpackage model
 */
class category extends Basecategory implements IIndexable, IRelatedObject, IElasticIndexable
{
	protected $childs_for_save = array();
	
	protected $depth = 0;
	
	protected $parent_category;
	
	protected $old_parent_id = null;
	
	protected $old_inheritance_type = null;
	
	protected $is_index = false;
	
	protected $move_entries_to_parent_category = null;

	const CATEGORY_ID_THAT_DOES_NOT_EXIST = 0;
	
	const IS_AGGREGATION_CATEGORY = 'isAggregationCategory';
	
	const AGGREGATION_CATEGORIES = 'aggregationCategories';
	
	const FULL_NAME_EQUAL_MATCH_STRING = 'fullnameequalmatchstring';
	
	const FULL_IDS_EQUAL_MATCH_STRING = 'fullidsequalmatchstring';
	
	const EXCEEDED_ENTRIES_COUNT_CACHE_PREFIX = "DONT_COUNT_ENTRIES_CAT_";
	
	// Cache expires after 4 hours.
	const EXCEEDED_ENTRIES_COUNT_CACHE_EXPIRY = 14400; 
	
	const MAX_NUMBER_OF_ENTRIES_PER_CATEGORY = 10000;
	
	/**
	 * Array of entries that decremented in the current session and maybe not indexed yet
	 * @var array
	 */
	protected static $decrementedEntryIds = array();
	
	/**
	 * Array of entries that incremented in the current session and maybe not indexed yet
	 * @var array
	 */
	protected static $incrementedEntryIds = array();
	
	/**
	 * @return bool
	 */
	public function getIsAggregationCategory() {
		return $this->getFromCustomData(self::IS_AGGREGATION_CATEGORY, null, false);
	}

	/**
	 * @return string
	 */
	public function getAggregationCategories() {
		return $this->getFromCustomData(self::AGGREGATION_CATEGORIES, null, "");
	}
	
	/**
	 * @return string
	 */
	public function getAggregationCategoriesIndexEngine () {
		$categories = str_replace(",", " ", $this->getAggregationCategories());
		return $categories;
	}

	/**
	 * @param bool $v
	 */
	public function setIsAggregationCategory($v) {
		$this->putInCustomData(self::IS_AGGREGATION_CATEGORY, $v);
	}

	/**
	 * @param string $value
	 */
	public function setAggregationCategories($value) {
		$this->putInCustomData(self::AGGREGATION_CATEGORIES, $value);
	}

	public function save(PropelPDO $con = null)
	{
		if ($this->isNew())
		{
			$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
				
			if (!PermissionPeer::isValidForPartner(PermissionName::DYNAMIC_FLAG_KMC_CHUNKED_CATEGORY_LOAD, $partnerId))
			{
				$c = KalturaCriteria::create(categoryPeer::OM_CLASS);
				$c->add (categoryPeer::STATUS, CategoryStatus::DELETED, Criteria::NOT_EQUAL);
				$c->add (categoryPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
				
				KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
				$numOfCatsForPartner = categoryPeer::doCount($c);
				KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
	
				$chunkedCategoryLoadThreshold = kConf::get('kmc_chunked_category_load_threshold');
				if ($numOfCatsForPartner >= $chunkedCategoryLoadThreshold)
					PermissionPeer::enableForPartner(PermissionName::DYNAMIC_FLAG_KMC_CHUNKED_CATEGORY_LOAD, PermissionType::SPECIAL_FEATURE);
			}

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
		
		if (!$this->isNew() && $this->isColumnModified(categoryPeer::PRIVACY_CONTEXTS))
		{
			$this->addSyncCategoryPrivacyContextJob();
		}
		
		$this->childs_for_save = array();

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
		
		$c = new Criteria();
		$c->add(categoryKuserPeer::CATEGORY_ID, $this->getId());
		if(!categoryKuserPeer::doSelectOne($c)) {
			return;
		}
		
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

		if ($objectDeleted)
		{
			$this->deleteChildCategories();
		}

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
		$v = categoryPeer::getParsedName($v);
		parent::setName($v);
	}
	
	/**
	 * @return partner
	 */
	public function getPartner()	{		return PartnerPeer::retrieveByPK( $this->getPartnerId() );	}
	
	public function setParentId($v)
	{
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
	 * Execute before incrementing entries count or direct entries count
	 */
	public function preIncrement($entryId)
	{
		if(!isset(self::$incrementedEntryIds[$this->getId()]))
			self::$incrementedEntryIds[$this->getId()] = array();
		self::$incrementedEntryIds[$this->getId()][$entryId] = $entryId;
		
		if(isset(self::$decrementedEntryIds[$this->getId()]) && isset(self::$decrementedEntryIds[$this->getId()][$entryId]))
			unset(self::$decrementedEntryIds[$this->getId()][$entryId]);
	}
	
	/**
	 * Execute before decrementing entries count or direct entries count
	 */
	public function preDecrement($entryId)
	{
		if(!isset(self::$decrementedEntryIds[$this->getId()]))
			self::$decrementedEntryIds[$this->getId()] = array();
		self::$decrementedEntryIds[$this->getId()][$entryId] = $entryId;
		
		if(isset(self::$incrementedEntryIds[$this->getId()]) && isset(self::$incrementedEntryIds[$this->getId()][$entryId]))
			unset(self::$incrementedEntryIds[$this->getId()][$entryId]);
	}
	
	/**
	 * Increment direct entries count
	 */
	public function incrementDirectEntriesCount($entryId)
	{
		KalturaLog::debug("incrementing $entryId to direct entries count " . $this->getFullName());
		$this->preIncrement($entryId);
		
		$count = $this->countEntriesByCriteria(self::$incrementedEntryIds[$this->getId()], true);
		if(!is_null($count))
		{
			$count += count(self::$incrementedEntryIds[$this->getId()]);
			$this->setDirectEntriesCount($count);
		}
		else
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
	 * Decrement direct entries count (will decrement recursively the parent categories too)
	 */
	public function decrementDirectEntriesCount($entryId)
	{
		KalturaLog::debug("decrementing $entryId from direct entries count " . $this->getFullName());
		$this->preDecrement($entryId);
		
		$count = $this->countEntriesByCriteria(self::$decrementedEntryIds[$this->getId()], true);
		if(!is_null($count))
			$this->setDirectEntriesCount($count);
		else	
			$this->setDirectEntriesCount(max(0, $this->getDirectEntriesCount() - 1));
	}
      
	/**
	* Decrement direct pending entries count
	*/
	public function decrementPendingEntriesCount()
	{
		$this->setPendingEntriesCount(max(0, $this->getPendingEntriesCount() - 1));
		$this->save();
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

	public function setDeletedAt($v, $moveEntriesToParentCategory = false)
	{
		parent::setDeletedAt($v);
		$this->setStatus(CategoryStatus::DELETED);
		$this->move_entries_to_parent_category = $moveEntriesToParentCategory;
	}

	// instead of deleting a category and its sub-categories recursively this method
	// will update all the deleted info in the db at once and will iterate through all of the sub-categories and invoke delete actions.
	//1. get all category childes ids for sub-categories from db in one call
	//2. update all retrieved category childes ids to be deleted in one call
	//3. iterate on categories one by one and invoke the delete event
	//4. delete sub-categories-entries and move all to parent category / also delete sub-categories kusers
	public function deleteChildCategories()
	{
		$fullIds = $this->getFullIds();
		$categoriesIds = $this->getDescendantCategoriesIds();

		$now = time();
		if (isset($categoriesIds) && !empty($categoriesIds))
		{
			$update = KalturaCriteria::create(categoryPeer::OM_CLASS);
			$update->add(categoryPeer::DELETED_AT, $now);
			$update->add(categoryPeer::UPDATED_AT, $now);
			$update->add(categoryPeer::STATUS, CategoryStatus::DELETED);
			$update->add(categoryPeer::ID, $categoriesIds, KalturaCriteria::IN);
			categoryPeer::doUpdate($update);
		}

		categoryPeer::setUseCriteriaFilter(false);
		$categories = categoryPeer::retrieveByPKs($categoriesIds);
		categoryPeer::setUseCriteriaFilter(true);

		foreach ($categories as $categoryToDelete) {
			$categoryToDelete->setDeletedAtInternally($now);
			$categoryToDelete->setUpdatedAt($now);
			$categoryToDelete->setStatus(CategoryStatus::DELETED);
			kEventsManager::raiseEvent(new kObjectDeletedEvent($categoryToDelete));
			kEventsManager::raiseEventDeferred(new kObjectReadyForIndexEvent($categoryToDelete));
		}

		if ($this->getInheritanceType() == InheritanceType::MANUAL)
			$this->addDeleteCategoryTreeKuserJob($fullIds);
		if ($this->move_entries_to_parent_category && $this->parent_id!=0)
			$this->addMoveEntriesToCategoryJob($this->parent_id);
		else
			$this->addDeleteCategoryTreeEntryJob($fullIds);
	}

	public function setDeletedAtInternally($v)
	{
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

	protected function addDeleteCategoryTreeKuserJob($fullIds)
	{
		$filter = new categoryKuserFilter();
		$filter->setFullIdsStartsWith($fullIds);

		kJobsManager::addDeleteJob($this->getPartnerId(), DeleteObjectType::CATEGORY_USER, $filter);
	}

	protected function addDeleteCategoryKuserJob($categoryId, $deleteCategoryDirectMembersOnly = false)
	{
		$filter = new categoryKuserFilter();
		$filter->setCategoryIdEqual($categoryId);
		$filter->setDirectMembers($deleteCategoryDirectMembersOnly);

		$c = new Criteria();
		$c->add(categoryKuserPeer::CATEGORY_ID, $categoryId);
		if(!categoryKuserPeer::doSelectOne($c)) {
			return;
		}
		
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

	protected function addDeleteCategoryTreeEntryJob($fullIds)
	{
		$filter = new categoryEntryFilter();
		$filter->setFullIdsStartsWith($fullIds);

		kJobsManager::addDeleteJob($this->getPartnerId(), DeleteObjectType::CATEGORY_ENTRY, $filter);
	}

	protected function addDeleteCategoryEntryJob($categoryId)
	{
		$filter = new categoryEntryFilter();
		$filter->setCategoryIdEqual($categoryId);
		
		$c = new Criteria();
		$c->add(categoryEntryPeer::CATEGORY_ID, $categoryId);
		if(!categoryEntryPeer::doSelectOne($c)) {
			return;
		}

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
		kJobsManager::addMoveCategoryEntriesJob(null, $this->getPartnerId(), $this->getId(), $destCategoryId, true, $this->getFullIds());
	}
	
	protected function addSyncCategoryPrivacyContextJob()
	{
		kJobsManager::addSyncCategoryPrivacyContextJob(null, $this->getPartnerId(), $this->getId());
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
		return array("category:id=".strtolower($this->getId()), "category:partnerId=".strtolower($this->getPartnerId()));
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
	
	public function getIndexObjectName() {
		return "categoryIndex";
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
		$permissionNamesByMembers = array();

		/* @var $member categoryKuser */
		while ($member = array_pop($members))
		{
			if(isset($membersIdsByPermission[$member->getPermissionLevel()]))
				$membersIdsByPermission[$member->getPermissionLevel()][] = $member->getKuserId();
			else
				$membersIdsByPermission[$member->getPermissionLevel()] = array ($member->getKuserId());

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
		KalturaLog::notice("members - " . print_r($members, true));
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
		$objectName = $this->getIndexObjectName();
		return $objectName::getSphinxMatchOptimizations($this);
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
		$privacyContexts = $this->buildPrivacyContexts($v);
		$this->setPrivacyContexts($privacyContexts);
		parent::setPrivacyContext($v);
	}
	
	private function buildPrivacyContexts($privacyContext)
	{
		if (!$this->getParentId())
		{
			$this->validatePrivacyContexts(explode(',',$privacyContext));
			return $privacyContext;
		}
		
		$privacyContexts = array();
		$parentCategory = $this->getParentCategory();
		if($parentCategory)
			$privacyContexts = explode(',', $parentCategory->getPrivacyContexts());
		$privacyContexts[] = $privacyContext;
		
		$privacyContextsTrimed = array();
		foreach($privacyContexts as $privacyContext)
		{
			if(trim($privacyContext) != '')
				$privacyContextsTrimed[] = trim($privacyContext);
		}

		$privacyContextsTrimed = array_unique($privacyContextsTrimed);
		$this->validatePrivacyContexts($privacyContextsTrimed);
		
		return trim(implode(',', $privacyContextsTrimed));		
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
	
	protected function countEntriesByCriteria($entryIds, $directOnly = false) {
		
		// Try to retrieve from cache
		$cacheKey =  category::EXCEEDED_ENTRIES_COUNT_CACHE_PREFIX . $this->getId();
		$cacheStore = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_LOCK_KEYS);
		if ($cacheStore)
		{
			$countExceeded = $cacheStore->get($cacheKey);
			if ($countExceeded)
				return null;
		}
		
		// Query for entry count
		$baseCriteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$filter = new entryFilter();
		$filter->setPartnerSearchScope(baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE);
		
		if($directOnly)
			$filter->setCategoriesIdsMatchAnd($this->getId());
		else
			$filter->setCategoryAncestorId($this->getId());
		
		if($entryIds)
			$filter->setIdNotIn($entryIds);
		$filter->setLimit(1);
		$filter->attachToCriteria($baseCriteria);
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);
		$baseCriteria->applyFilters();
		
		$count = $baseCriteria->getRecordsCount();
		KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);
		
		// Save the result within the cache		
		if($count >= category::MAX_NUMBER_OF_ENTRIES_PER_CATEGORY) {
			if ($cacheStore)
				$cacheStore->set($cacheKey, true, category::EXCEEDED_ENTRIES_COUNT_CACHE_EXPIRY);
		}
		
		return $count;
	}
	
	/**
	 * reset category's entriesCount by calculate it.
	 */
	public function reSetEntriesCount()
	{
		$count = $this->countEntriesByCriteria(null);
		if(is_null($count))
			return;
		$this->setEntriesCount($count);
	}
	
	/**
	 * reset category's pendingEntriesCount by calculate it.
	 */
	public function reSetPendingEntriesCount()
	{
		$criteria = new Criteria();
		$criteria->add(categoryEntryPeer::CATEGORY_ID, $this->getId());
		$criteria->add(categoryEntryPeer::STATUS, CategoryEntryStatus::PENDING);
			
		$count = categoryEntryPeer::doCount($criteria);
		$this->setPendingEntriesCount($count);
	}
	
	/**
	 * Decrement category's entriesCount by calculate it.
	 */
	public function decrementEntriesCount($entryId)
	{
		KalturaLog::debug("decrementing $entryId from " . $this->getFullName());
		$this->preDecrement($entryId);
		
		$count = $this->countEntriesByCriteria(self::$decrementedEntryIds[$this->getId()]);
		if(!is_null($count))
			$this->setEntriesCount($count);

		if($this->getParentId())
		{
			$parentCat = $this->getParentCategory();
			if($parentCat)
			{
				$parentCat->decrementEntriesCount($entryId);
				$parentCat->save();
			}
		}
	}
	
	/**
	 * Decrement category's entriesCount by calculate it.
	 */
	public function incrementEntriesCount($entryId)
	{
		KalturaLog::debug("incrementing $entryId to " . $this->getFullName());
		$this->preIncrement($entryId);
		
		$count = $this->countEntriesByCriteria(self::$incrementedEntryIds[$this->getId()]);
		if(!is_null($count)) {
			$count += count(self::$incrementedEntryIds[$this->getId()]);
			$this->setEntriesCount($count);
		}
	
		if($this->getParentId())
		{
			$parentCat = $this->getParentCategory();
			if($parentCat)
			{
				$parentCat->incrementEntriesCount($entryId);
				$parentCat->save();
			}
		}
	}
	
	/**
	 * reset category's directEntriesCount by calculate it.
	 */
	public function reSetDirectEntriesCount()
	{
		$baseCriteria = KalturaCriteria::create(entryPeer::OM_CLASS);
		$filter = new entryFilter();
		$filter->setPartnerSearchScope(baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE);
		$filter->setCategoriesIdsMatchAnd($this->getId());
		$filter->setLimit(1);
		$filter->attachToCriteria($baseCriteria);
		
		$baseCriteria->applyFilters();
		
		$count = $baseCriteria->getRecordsCount();

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

	protected function getElasticSearchIndexPrivacyContext()
	{
		if(is_null($this->getPrivacyContext()) || trim($this->getPrivacyContext()) == '')
			return null;

		$privacyContexts = explode(',', $this->getPrivacyContext());
		$privacyContexts[] = kEntitlementUtils::NOT_DEFAULT_CONTEXT;
		$privacyContexts = kEntitlementUtils::addPrivacyContextsPrefix( $privacyContexts, $this->getPartnerId() );
		return $privacyContexts;
	}

	protected function getElasticSearchIndexPrivacyContexts()
	{
		if(is_null($this->getPrivacyContexts()) || trim($this->getPrivacyContexts()) == '')
			return kEntitlementUtils::getDefaultContextString( $this->getPartnerId() );

		$privacyContexts = explode(',', $this->getPrivacyContexts());
		$privacyContexts = kEntitlementUtils::addPrivacyContextsPrefix( $privacyContexts, $this->getPartnerId() );

		return $privacyContexts;
	}
	
	public function getSearchIndexPrivacyContext()
	{
		if(is_null($this->getPrivacyContext()) || trim($this->getPrivacyContext()) == '')
			return '';

		$privacyContexts = explode(',', $this->getPrivacyContext());
		$privacyContexts[] = kEntitlementUtils::NOT_DEFAULT_CONTEXT;
		$privacyContexts = kEntitlementUtils::addPrivacyContextsPrefix( $privacyContexts, $this->getPartnerId() );

		return implode(' ',$privacyContexts);
	}

	public function getSearchIndexPrivacyContexts()
	{
		if(is_null($this->getPrivacyContexts()) || trim($this->getPrivacyContexts()) == '')
			return kEntitlementUtils::getDefaultContextString( $this->getPartnerId() );

		$privacyContexts = explode(',', $this->getPrivacyContexts());
		$privacyContexts = kEntitlementUtils::addPrivacyContextsPrefix( $privacyContexts, $this->getPartnerId() );

		return implode(' ',$privacyContexts);
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
	
	public function getOptimizedDisplayInSearchIndex()
	{
		return $this->display_in_search . "P" . $this->getPartnerId();
	}
	
	public function validatePrivacyContexts($privacyContexts)
	{
		if(PermissionPeer::isValidForPartner(PermissionName::FEATURE_DISABLE_CATEGORY_LIMIT, $this->getPartnerId()))
		{
			if(count($privacyContexts) > 1)
			{
				throw new kCoreException("Only one privacy context allowed when Disable Category Limit feature turned on", kCoreException::DISABLE_CATEGORY_LIMIT_MULTI_PRIVACY_CONTEXT_FORBIDDEN);
			}
		}
		return true;
	}

	public function addIndexCategoryInheritedTreeJob()
	{
		$featureStatusToRemoveIndex = new kFeatureStatus();
		$featureStatusToRemoveIndex->setType(IndexObjectType::CATEGORY);

		$featureStatusesToRemove = array();
		$featureStatusesToRemove[] = $featureStatusToRemoveIndex;

		$filter = new categoryFilter();
		$filter->setFullIdsStartsWith($this->getFullIds());
		$filter->setInheritanceTypeEqual(InheritanceType::INHERIT);

		$c = KalturaCriteria::create(categoryPeer::OM_CLASS);
		$filter->attachToCriteria($c);
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$categories = categoryPeer::doSelect($c);
		KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);

		if(count($categories))
		{
			kJobsManager::addIndexJob($this->getPartnerId(), IndexObjectType::CATEGORY, $filter, true, $featureStatusesToRemove);
		}

	}

	public function indexCategoryInheritedTree()
	{
		kEventsManager::raiseEventDeferred(new kObjectReadyForIndexInheritedTreeEvent($this));
	}

	/**
	 * @param $fullIds
	 * @return array
	 */
	private function getDescendantCategoriesIds()
	{
		$fullIds = $this->getFullIds();
		$fullIds = $fullIds.'>';

		$filter = new categoryFilter();
		$filter->setFullIdsStartsWith($fullIds);
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS);
		$filter->attachToCriteria($c);
		$c->applyFilters();
		$categoryIds = $c->getFetchedIds();
		return $categoryIds;
	}

	/**
	 * return the name of the elasticsearch index for this object
	 */
	public function getElasticIndexName()
	{
		return ElasticIndexMap::ELASTIC_CATEGORY_INDEX;
	}

	/**
	 * return the name of the elasticsearch type for this object
	 */
	public function getElasticObjectType()
	{
		return ElasticIndexMap::ELASTIC_CATEGORY_TYPE;
	}

	/**
	 * return the elasticsearch id for this object
	 */
	public function getElasticId()
	{
		return $this->getId();
	}

	/**
	 * return the elasticsearch parent id or null if no parent
	 */
	public function getElasticParentId()
	{
		return null;
	}

	/**
	 * get the params we index to elasticsearch for this object
	 */
	public function getObjectParams($params = null)
	{
		$body = array(
			'doc' => array(
				'partner_id' => $this->getPartnerId(),
				'partner_status' => elasticSearchUtils::formatPartnerStatus($this->getPartnerId(), $this->getStatus()),
				'privacy' => self::formatPrivacy($this->getPrivacy(), $this->getPartnerId()),
				'privacy_context' => $this->getElasticSearchIndexPrivacyContext(),
				'privacy_contexts' => $this->getElasticSearchIndexPrivacyContexts(),
				'status' => $this->getStatus(),
				'parent_id' => $this->getParentId(),
				'depth' => $this->getDepth(),
				'name' => $this->getName(),
				'full_name' => $this->getFullName(),
				'full_ids' => explode(',',$this->getFullIds()),
				'entries_count' => $this->getEntriesCount(),
				'created_at' => $this->getCreatedAt(null),
				'updated_at' => $this->getUpdatedAt(null),
				'direct_entries_count' => $this->getDirectEntriesCount(),
				'direct_sub_categories_count' => $this->getDirectSubCategoriesCount(),
				'members_count' => $this->getMembersCount(),
				'pending_members_count' => $this->getPendingMembersCount(),
				'pending_entries_count' => $this->getPendingEntriesCount(),
				'description' => $this->getDescription(),
				'tags' => explode(',', $this->getTags()),
				'display_in_search' => $this->getDisplayInSearch(),
				'inheritance_type' => $this->getInheritanceType(),
				'kuser_id' => $this->getKuserId(),
				'reference_id' => $this->getReferenceId(),
				'inherited_parent_id' => $this->getInheritedParentId(),
				'moderation' => $this->getModeration(),
				'contribution_policy' => $this->getContributionPolicy(),
			),
			'doc_as_upsert' => true
		);
		return $body;
	}

	/**
	 * return the save method to elastic: ElasticMethodType::INDEX or ElasticMethodType::UPDATE
	 */
	public function getElasticSaveMethod()
	{
		return ElasticMethodType::UPDATE;
	}

	/**
	 * Index the object into elasticsearch
	 */
	public function indexToElastic($params = null)
	{
		kEventsManager::raiseEventDeferred(new kObjectReadyForElasticIndexEvent($this));
	}

	/**
	 * return true if the object needs to be deleted from elastic
	 */
	public function shouldDeleteFromElastic()
	{
		if($this->getStatus() == CategoryStatus::PURGED)
			return true;
		return false;
	}

	/**
	 * return the name of the object we are indexing
	 */
	public function getElasticObjectName()
	{
		return 'category';
	}
}