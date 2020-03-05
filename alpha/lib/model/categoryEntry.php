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
class categoryEntry extends BasecategoryEntry implements IRelatedObject
{
	
	/* (non-PHPdoc)
	 * @see lib/model/om/Basecategory#preSave()
	 */
	public function preSave(PropelPDO $con = null)
	{
		if ($this->getStatus() != CategoryEntryStatus::DELETED)
		{
			$category = categoryPeer::retrieveByPK($this->getCategoryId());
			if(!$category)
				return false;
			
			$this->setCategoryFullIds($category->getFullIds());
		}
			
		return parent::preSave();
	}

	/*
	 * set privacy context from category object before the insert
	 */
	public function preInsert(PropelPDO $con = null)
	{
		$category = categoryPeer::retrieveByPK($this->getCategoryId());
		if($category)
			$this->setPrivacyContext($category->getPrivacyContexts());		
		return parent::preInsert($con);
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
			
		if (!$this->alreadyInSave)
			kEventsManager::raiseEvent(new kObjectAddedEvent($this));
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
		
		if($this->getStatus() == CategoryEntryStatus::PENDING &&
			$this->getColumnsOldValue(categoryEntryPeer::STATUS) == CategoryEntryStatus::REJECTED)
			$category->incrementPendingEntriesCount();
			
		if($this->getStatus() == CategoryEntryStatus::DELETED)
		{
			if($this->getColumnsOldValue(categoryEntryPeer::STATUS) == CategoryEntryStatus::ACTIVE)
			{
				$category->decrementEntriesCount($this->getEntryId());
				$category->decrementDirectEntriesCount($this->getEntryId());
		
				if($entry && !categoryEntryPeer::getSkipSave()) //entry might be deleted - and delete job remove the categoryEntry object
				{
					$categories = array();
					if(trim($entry->getCategories()) != '')
					{
						$categories = explode(entry::ENTRY_CATEGORY_SEPARATOR, $entry->getCategories());
						
						foreach($categories as $index => $entryCategoryFullName)
						{
							if($entryCategoryFullName == $category->getFullName())
								unset($categories[$index]);
						}
					}
					
					$categoriesIds = array();
					if(trim($entry->getCategoriesIds()) != '')
					{
						$categoriesIds = explode(entry::ENTRY_CATEGORY_SEPARATOR, $entry->getCategoriesIds());
					
						foreach($categories as $index => $entryCategoryId)
						{
							if($entryCategoryId == $category->getId())
								unset($categoriesIds[$index]);
						}	
					}
					
					$entry->setCategories(implode(entry::ENTRY_CATEGORY_SEPARATOR, $categories));
					$entry->setCategoriesIds(implode(entry::ENTRY_CATEGORY_SEPARATOR, $categoriesIds));
					$entry->save();
				}
				kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
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
		$category->incrementEntriesCount($this->getEntryId());
		$category->incrementDirectEntriesCount($this->getEntryId());
		
		//if was pending - decrease pending entries count!
		if($this->getColumnsOldValue(categoryEntryPeer::STATUS) == CategoryEntryStatus::PENDING)
			$category->decrementPendingEntriesCount();
			
		$category->save();

		//only categories with no context are saved on entry - this is only for backward compatiblity
		if($entry && !categoryEntryPeer::getSkipSave()) 
		{
			if( (trim($category->getPrivacyContexts()) == '' || $category->getPrivacyContexts() == null))
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
			else
			{
				$entry->setUpdatedAt(time());
				$entry->justSave();
				$entry->indexToSearchIndex();
			}
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
		return array("categoryEntry:entryId=".strtolower($this->getEntryId()), "categoryEntry:categoryId=".strtolower($this->getCategoryId()));
	}
	
	public function setBulkUploadId ( $bulkUploadId )	{		$this->putInCustomData ( "bulk_upload_id" , $bulkUploadId );	}
	public function getBulkUploadId (  )	{		return $this->getFromCustomData( "bulk_upload_id" );	}
	
	public function getCreatorPuserId () 
	{
		return $this->getFromCustomData('creatorPuserId');
	}
	
	public function setCreatorPuserId ($v)
	{
		$this->putInCustomData('creatorPuserId', $v);
	}

	/**
	 * @param $entryId
	 * @param $categoryId
	 * @throws KalturaErrors::CATEGORY_NOT_FOUND
	 * @throws KalturaErrors::MAX_CATEGORIES_FOR_ENTRY_REACHED
	 * @throws KalturaErrors::CANNOT_ASSIGN_ENTRY_TO_CATEGORY
	 * @throws KalturaErrors::CATEGORY_ENTRY_ALREADY_EXISTS*
	 * @throws KalturaErrors::INVALID_ENTRY_ID
	 * @throws Exception
	 * @return categoryEntry $categoryEntry
	 */
	public function add($entryId, $categoryId)
	{
		/*=========================================================*/
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
		{
//			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_ID, $entryId);
			throw new kCoreException("Invalid Entry ID: {$entryId}",kCoreException::INVALID_ENTRY_ID);
		}

		$category = categoryPeer::retrieveByPK($categoryId);
		if(!$category)
		{
//			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryId);
			throw new kCoreException("Category ID: {$categoryId} not found", kCoreException::CATEGORY_NOT_FOUND);
		}
		/*=========================================================*/
		$categoryEntries = categoryEntryPeer::retrieveActiveAndPendingByEntryId($entryId);

		$maxCategoriesPerEntry = $entry->getMaxCategoriesPerEntry();

		if(count($categoryEntries) >= $maxCategoriesPerEntry)
		{
//			throw new KalturaAPIException(KalturaErrors::MAX_CATEGORIES_FOR_ENTRY_REACHED, $maxCategoriesPerEntry);
			throw new kCoreException("Max categories per entry reached, Allowed: {$maxCategoriesPerEntry}", kCoreException::MAX_CATEGORIES_PER_ENTRY);
		}
		/*=========================================================*/
		$currentKsKuserId = kCurrentContext::getCurrentKsKuserId();

		// validate user is entitled to assign entry to this category
		if(kEntitlementUtils::getEntitlementEnforcement() && $category->getContributionPolicy() != ContributionPolicyType::ALL)
		{
			$categoryKuser = categoryKuserPeer::retrievePermittedKuserInCategory($categoryId, $currentKsKuserId); // TODO ask Moshe explanation for diff $entry->getKuserId()

			if(!$categoryKuser)
			{
				KalturaLog::err("User '{$currentKsKuserId}' is not a member in category Id '{$categoryId}'"); //TODO: consider removing, kCoreException already prints the 'message' to KalturaLog:err
//				throw new KalturaAPIException(KalturaErrors::CANNOT_ASSIGN_ENTRY_TO_CATEGORY);
				throw new kCoreException("User '{$currentKsKuserId}' is not a member in category Id '{$categoryId}'", kCoreException::CANNOT_ASSIGN_ENTRY_TO_CATEGORY);
			}

			if($categoryKuser->getPermissionLevel() == CategoryKuserPermissionLevel::MEMBER)
			{
				KalturaLog::err('User [' . $currentKsKuserId . '] permission level in category Id [' . $categoryId . '] is [' . CategoryKuserPermissionLevel::MEMBER . '] not allowed to add entry to category'); //TODO: consider removing, kCoreException already prints the 'message' to KalturaLog:err
//				throw new KalturaAPIException(KalturaErrors::CANNOT_ASSIGN_ENTRY_TO_CATEGORY);
				throw new kCoreException("User '{$currentKsKuserId}' permission level in category Id '{$categoryId}' is 'MEMBER' and is not allowed to add entry to category", kCoreException::CANNOT_ASSIGN_ENTRY_TO_CATEGORY);
			}

			if(!$categoryKuser->hasPermission(PermissionName::CATEGORY_EDIT) &&
				!$categoryKuser->hasPermission(PermissionName::CATEGORY_CONTRIBUTE) &&
				!$entry->isEntitledKuserEdit($currentKsKuserId) &&
				$entry->getCreatorKuserId() != $currentKsKuserId)
			{
//				throw new KalturaAPIException(KalturaErrors::CANNOT_ASSIGN_ENTRY_TO_CATEGORY);
				throw new kCoreException("Cannot assign entry to category", kCoreException::CANNOT_ASSIGN_ENTRY_TO_CATEGORY);
			}
		}
		/*=========================================================*/
		$categoryEntryExists = categoryEntryPeer::retrieveByCategoryIdAndEntryId($categoryId, $entryId);
		if($categoryEntryExists && $categoryEntryExists->getStatus() == CategoryEntryStatus::ACTIVE) // TODO: what about PENDING?
		{
//			throw new KalturaAPIException(KalturaErrors::CATEGORY_ENTRY_ALREADY_EXISTS);
			throw new kCoreException("Category-Entry object already exist", kCoreException::CATEGORY_ENTRY_ALREADY_EXISTS);
		}

		if(!$categoryEntryExists)
		{
			$categoryEntry = new categoryEntry();
		}
		else
		{
			$categoryEntry = $categoryEntryExists;
		}
		/*=========================================================*/
		$apiCategoryEntry = new KalturaCategoryEntry();
		$apiCategoryEntry->entryId = $entryId;
		$apiCategoryEntry->categoryId = $categoryId;
		$apiCategoryEntry->toInsertableObject($categoryEntry); // TODO: Is there a better way to access "toInsertableObject"?
		/*=========================================================*/
		$categoryEntry->setStatus(CategoryEntryStatus::ACTIVE);
		/*=========================================================*/
		if(kEntitlementUtils::getEntitlementEnforcement() && $category->getModeration())
		{
			$categoryKuser = categoryKuserPeer::retrievePermittedKuserInCategory($categoryId, $currentKsKuserId);
			if(!$categoryKuser || ($categoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MEMBER &&
					$categoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MODERATOR))
			{
				$categoryEntry->setStatus(CategoryEntryStatus::PENDING);
			}
		}

		if($category->getModeration() && (kEntitlementUtils::getCategoryModeration() ||
				$category->getPartner()->getEnabledService(KalturaPermissionName::FEATURE_BLOCK_CATEGORY_MODERATION_SELF_APPROVE))) //TODO: notice changed '$this->getPartner()->getEnabledService' ($this = CategoryEntryService) with $category->getPartner()->getEnabledService
		{
			$categoryEntry->setStatus(CategoryEntryStatus::PENDING);
		}
		/*=========================================================*/
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id; // TODO: why not take PID from Entry / Category?
		$categoryEntry->setPartnerId($partnerId);

		$kuser = kCurrentContext::getCurrentKsKuser();

		if($kuser)
		{
			$categoryEntry->setCreatorKuserId($kuser->getId());
			$categoryEntry->setCreatorPuserId($kuser->getPuserId());
		}
		KalturaLog::info("New categoryEntry add works");
		return $categoryEntry;
	}
	
} // categoryEntry
