<?php

/**
 * Add & Manage CategoryEntry - assign entry to category
 *
 * @service categoryEntry
 */
class CategoryEntryService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		$this->applyPartnerFilterForClass('category');
		$this->applyPartnerFilterForClass('entry');	
	}
	
	/**
	 * Add new CategoryEntry
	 * 
	 * @action add
	 * @param KalturaCategoryEntry $categoryEntry
	 * @throws KalturaErrors::INVALID_ENTRY_ID
	 * @throws KalturaErrors::CATEGORY_NOT_FOUND
	 * @throws KalturaErrors::CANNOT_ASSIGN_ENTRY_TO_CATEGORY
	 * @throws KalturaErrors::CATEGORY_ENTRY_ALREADY_EXISTS
	 * @return KalturaCategoryEntry
	 */
	function addAction(KalturaCategoryEntry $categoryEntry)
	{
		$categoryEntry->validateForInsert();
		
		$entry = entryPeer::retrieveByPK($categoryEntry->entryId);
		if (!$entry)
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_ID, $categoryEntry->entryId);
			
		$category = categoryPeer::retrieveByPK($categoryEntry->categoryId);
		if (!$category)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryEntry->categoryId);
			
		$categoryEntries = categoryEntryPeer::retrieveActiveAndPendingByEntryId($categoryEntry->entryId);
		
		$maxCategoriesPerEntry = $entry->getMaxCategoriesPerEntry();
			
		if (count($categoryEntries) >= $maxCategoriesPerEntry)
			throw new KalturaAPIException(KalturaErrors::MAX_CATEGORIES_FOR_ENTRY_REACHED, $maxCategoriesPerEntry);
			
		//validate user is entiteld to assign entry to this category 
		if (kEntitlementUtils::getEntitlementEnforcement() && $category->getContributionPolicy() != ContributionPolicyType::ALL)
		{
			$categoryKuser = categoryKuserPeer::retrievePermittedKuserInCategory($categoryEntry->categoryId, kCurrentContext::getCurrentKsKuserId());
			if(!$categoryKuser)
			{
				KalturaLog::err("User [" . kCurrentContext::getCurrentKsKuserId() . "] is not a member of the category [{$categoryEntry->categoryId}]");
				throw new KalturaAPIException(KalturaErrors::CANNOT_ASSIGN_ENTRY_TO_CATEGORY);
			}
			if($categoryKuser->getPermissionLevel() == CategoryKuserPermissionLevel::MEMBER)
			{
				KalturaLog::err("User [" . kCurrentContext::getCurrentKsKuserId() . "] permission level [" . $categoryKuser->getPermissionLevel() . "] on category [{$categoryEntry->categoryId}] is not member [" . CategoryKuserPermissionLevel::MEMBER . "]");
				throw new KalturaAPIException(KalturaErrors::CANNOT_ASSIGN_ENTRY_TO_CATEGORY);
			}
				
			if(!$categoryKuser->hasPermission(PermissionName::CATEGORY_EDIT) && !$categoryKuser->hasPermission(PermissionName::CATEGORY_CONTRIBUTE) &&
				!$entry->isEntitledKuserEdit(kCurrentContext::getCurrentKsKuserId()) &&
				$entry->getCreatorKuserId() != kCurrentContext::getCurrentKsKuserId())
				throw new KalturaAPIException(KalturaErrors::CANNOT_ASSIGN_ENTRY_TO_CATEGORY);				
		}
		
		$categoryEntryExists = categoryEntryPeer::retrieveByCategoryIdAndEntryId($categoryEntry->categoryId, $categoryEntry->entryId);
		if($categoryEntryExists && $categoryEntryExists->getStatus() == CategoryEntryStatus::ACTIVE)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_ENTRY_ALREADY_EXISTS);
		
		if(!$categoryEntryExists)
		{
			$dbCategoryEntry = new categoryEntry();
		}
		else
		{
			$dbCategoryEntry = $categoryEntryExists;
		}
		
		$categoryEntry->toInsertableObject($dbCategoryEntry);
		
		/* @var $dbCategoryEnry categoryEntry */
		$dbCategoryEntry->setStatus(CategoryEntryStatus::ACTIVE);
		
		if (kEntitlementUtils::getEntitlementEnforcement() && $category->getModeration())
		{
			$categoryKuser = categoryKuserPeer::retrievePermittedKuserInCategory($categoryEntry->categoryId, kCurrentContext::getCurrentKsKuserId());
			if(!$categoryKuser ||
				($categoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MANAGER && 
				$categoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MODERATOR))
				$dbCategoryEntry->setStatus(CategoryEntryStatus::PENDING);
		}
		
		if ($category->getModeration() && 
		   (kEntitlementUtils::getCategoryModeration() || $this->getPartner()->getEnabledService(KalturaPermissionName::FEATURE_BLOCK_CATEGORY_MODERATION_SELF_APPROVE)))
		{
			$dbCategoryEntry->setStatus(CategoryEntryStatus::PENDING);
		}
		
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
		$dbCategoryEntry->setPartnerId($partnerId);
		
		$kuser = kCurrentContext::getCurrentKsKuser();
		
		if ($kuser)
		{
			$dbCategoryEntry->setCreatorKuserId($kuser->getId());
			$dbCategoryEntry->setCreatorPuserId($kuser->getPuserId());
		}
		
		$dbCategoryEntry->save();
		
		//need to select the entry again - after update
		$entry = entryPeer::retrieveByPK($categoryEntry->entryId);		
		myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE, $entry);
		
		$categoryEntry = new KalturaCategoryEntry();
		$categoryEntry->fromObject($dbCategoryEntry, $this->getResponseProfile());

		return $categoryEntry;
	}
	
	/**
	 * Delete CategoryEntry
	 * 
	 * @action delete
	 * @param string $entryId
	 * @param int $categoryId
	 * @throws KalturaErrors::INVALID_ENTRY_ID
	 * @throws KalturaErrors::CATEGORY_NOT_FOUND
	 * @throws KalturaErrors::CANNOT_REMOVE_ENTRY_FROM_CATEGORY
	 * @throws KalturaErrors::ENTRY_IS_NOT_ASSIGNED_TO_CATEGORY
	 * 
	 */
	function deleteAction($entryId, $categoryId)
	{
		$entry = entryPeer::retrieveByPK($entryId);

		if (!$entry)
		{
			if (kCurrentContext::$master_partner_id != Partner::BATCH_PARTNER_ID)
				throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_ID, $entryId);
		}
			
		$category = categoryPeer::retrieveByPK($categoryId);
		if (!$category && kCurrentContext::$master_partner_id != Partner::BATCH_PARTNER_ID)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryId);

		//validate user is entitled to remove entry from category
		if(kEntitlementUtils::getEntitlementEnforcement() &&
			!$entry->isEntitledKuserEdit(kCurrentContext::getCurrentKsKuserId()) &&
			$entry->getCreatorKuserId() != kCurrentContext::getCurrentKsKuserId())
		{
			$kuserIsEntitled = false;
			$kuser = categoryKuserPeer::retrievePermittedKuserInCategory($categoryId, kCurrentContext::getCurrentKsKuserId());

			// First pass: check if kuser is a manager
			if ( $kuser )
			{
				if ( $kuser->getPermissionLevel() == CategoryKuserPermissionLevel::MANAGER )
				{
					$kuserIsEntitled = true;
				}
			}
			else
			{
				$kuser = kuserPeer::retrieveByPK( kCurrentContext::getCurrentKsKuserId() );
			}

			// Second pass: check if kuser is a co-publisher
			if ( ! $kuserIsEntitled
					&& $kuser
					&& $entry->isEntitledKuserPublish($kuser->getKuserId()))
			{
				$kuserIsEntitled = true;
			}

			if ( ! $kuserIsEntitled )
			{
				throw new KalturaAPIException(KalturaErrors::CANNOT_REMOVE_ENTRY_FROM_CATEGORY);
			}
		}
			
		$dbCategoryEntry = categoryEntryPeer::retrieveByCategoryIdAndEntryId($categoryId, $entryId);
		if(!$dbCategoryEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_IS_NOT_ASSIGNED_TO_CATEGORY);
		
		$dbCategoryEntry->setStatus(CategoryEntryStatus::DELETED);
		$dbCategoryEntry->save();
		
		//need to select the entry again - after update
		$entry = entryPeer::retrieveByPK($entryId);		
		myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_ENTRY_UPDATE, $entry);
	}
	
	/**
	 * List all categoryEntry
	 * 
	 * @action list
	 * @param KalturaCategoryEntryFilter $filter
	 * @param KalturaFilterPager $pager
	 * @throws KalturaErrors::MUST_FILTER_ENTRY_ID_EQUAL
	 * @throws KalturaErrors::MUST_FILTER_ON_ENTRY_OR_CATEGORY
	 * @return KalturaCategoryEntryListResponse
	 */
	function listAction(KalturaCategoryEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaCategoryEntryFilter();
			
		if(!$pager)
			$pager = new KalturaFilterPager();
			
		return $filter->getListResponse($pager, $this->getResponseProfile());
	}
	
	/**
	 * Index CategoryEntry by Id
	 * 
	 * @action index
	 * @param string $entryId
	 * @param int $categoryId
	 * @param bool $shouldUpdate
	 * @throws KalturaErrors::ENTRY_IS_NOT_ASSIGNED_TO_CATEGORY
	 * @return int
	 */
	function indexAction($entryId, $categoryId, $shouldUpdate = true)
	{
		if(kEntitlementUtils::getEntitlementEnforcement())
			throw new KalturaAPIException(KalturaErrors::CANNOT_INDEX_OBJECT_WHEN_ENTITLEMENT_IS_ENABLE);
		
		$dbCategoryEntry = categoryEntryPeer::retrieveByCategoryIdAndEntryId($categoryId, $entryId);
		if(!$dbCategoryEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_IS_NOT_ASSIGNED_TO_CATEGORY);
			
		if (!$shouldUpdate)
		{
			$dbCategoryEntry->setUpdatedAt(time());
			$dbCategoryEntry->save();
			
			return $dbCategoryEntry->getIntId();
		}
		
		$dbCategoryEntry->reSetCategoryFullIds();
		$dbCategoryEntry->save();
		
		
		$entry = entryPeer::retrieveByPK($dbCategoryEntry->getEntryId());	
		if($entry)
		{
			$categoryEntries = categoryEntryPeer::retrieveActiveByEntryId($entryId);
			
			$categoriesIds = array();
			foreach($categoryEntries as $categoryEntry)
			{
				$categoriesIds[] = $categoryEntry->getCategoryId();
			}
			
			$categories = categoryPeer::retrieveByPKs($categoriesIds);
			
			$isCategoriesModified = false;
			$categoriesFullName = array();
			foreach($categories as $category)
			{
				if($category->getPrivacyContexts() == null)
				{
					$categoriesFullName[] = $category->getFullName();
					$isCategoriesModified = true;
				}
			} 
				
			$entry->setCategories(implode(',', $categoriesFullName));
			categoryEntryPeer::syncEntriesCategories($entry, $isCategoriesModified);
			$entry->save();
		}
		
		return $dbCategoryEntry->getId();
				
	}

	private static function applyStatusOnChildren($dbEntry, $categoryId, $status)
	{
		$relatedEntries = entryPeer::retrieveChildEntriesByEntryIdAndPartnerId($dbEntry->getId(), $dbEntry->getPartnerId());
		foreach ($relatedEntries as $relatedEntry)
		{
			$dbCategoryEntry = categoryEntryPeer::retrieveByCategoryIdAndEntryIdNotRejected($categoryId, $relatedEntry->getId());
			if($dbCategoryEntry)
			{
				$dbCategoryEntry->setStatus($status);
				$dbCategoryEntry->save();
			}
		}
	}

	/**
	 * activate CategoryEntry when it is pending moderation
	 * 
	 * @action activate
	 * @param string $entryId
	 * @param int $categoryId
	 * @throws KalturaErrors::INVALID_ENTRY_ID
	 * @throws KalturaErrors::CATEGORY_NOT_FOUND
	 * @throws KalturaErrors::ENTRY_IS_NOT_ASSIGNED_TO_CATEGORY
	 * @throws KalturaErrors::CANNOT_ACTIVATE_CATEGORY_ENTRY
	 */
	function activateAction($entryId, $categoryId)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if (!$entry)
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_ID, $entryId);
			
		$category = categoryPeer::retrieveByPK($categoryId);
		if (!$category)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryId);
		
		$dbCategoryEntry = categoryEntryPeer::retrieveByCategoryIdAndEntryIdNotRejected($categoryId, $entryId);
		if(!$dbCategoryEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_IS_NOT_ASSIGNED_TO_CATEGORY);
			
		//validate user is entiteld to activate entry from category 
		if(kEntitlementUtils::getEntitlementEnforcement())
		{
			$categoryKuser = categoryKuserPeer::retrievePermittedKuserInCategory($categoryId, kCurrentContext::getCurrentKsKuserId());
			if(!$categoryKuser || 
				($categoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MANAGER && 
				 $categoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MODERATOR))
					throw new KalturaAPIException(KalturaErrors::CANNOT_ACTIVATE_CATEGORY_ENTRY);
					
		}
		
		if (kCurrentContext::getCurrentKsKuserId() == $dbCategoryEntry->getCreatorKuserId() &&
			$this->getPartner()->getEnabledService(KalturaPermissionName::FEATURE_BLOCK_CATEGORY_MODERATION_SELF_APPROVE))
		{
			throw new KalturaAPIException(KalturaErrors::CANNOT_ACTIVATE_CATEGORY_ENTRY);
		}

		if($dbCategoryEntry->getStatus() != CategoryEntryStatus::PENDING)
			throw new KalturaAPIException(KalturaErrors::CANNOT_ACTIVATE_CATEGORY_ENTRY_SINCE_IT_IS_NOT_PENDING);
			
		$dbCategoryEntry->setStatus(CategoryEntryStatus::ACTIVE);
		$dbCategoryEntry->save();

		self::applyStatusOnChildren($entry, $categoryId, CategoryEntryStatus::ACTIVE);
	}

	/**
	 * activate CategoryEntry when it is pending moderation
	 *
	 * @action reject
	 * @param string $entryId
	 * @param int $categoryId
	 * @throws KalturaErrors::INVALID_ENTRY_ID
	 * @throws KalturaErrors::CATEGORY_NOT_FOUND
	 * @throws KalturaErrors::ENTRY_IS_NOT_ASSIGNED_TO_CATEGORY
	 * @throws KalturaErrors::CANNOT_ACTIVATE_CATEGORY_ENTRY
	 */
	function rejectAction($entryId, $categoryId)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if (!$entry)
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_ID, $entryId);
			
		$category = categoryPeer::retrieveByPK($categoryId);
		if (!$category)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryId);
		
		$dbCategoryEntry = categoryEntryPeer::retrieveByCategoryIdAndEntryId($categoryId, $entryId);
		if(!$dbCategoryEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_IS_NOT_ASSIGNED_TO_CATEGORY);
			
		//validate user is entiteld to reject entry from category 
		if(kEntitlementUtils::getEntitlementEnforcement())
		{
			$categoryKuser = categoryKuserPeer::retrievePermittedKuserInCategory($categoryId, kCurrentContext::getCurrentKsKuserId());
			if(!$categoryKuser || 
				($categoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MANAGER && 
				 $categoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MODERATOR))
					throw new KalturaAPIException(KalturaErrors::CANNOT_REJECT_CATEGORY_ENTRY);
					
		}
			
		if (kCurrentContext::getCurrentKsKuserId() == $dbCategoryEntry->getCreatorKuserId() &&
			$this->getPartner()->getEnabledService(KalturaPermissionName::FEATURE_BLOCK_CATEGORY_MODERATION_SELF_APPROVE))
		{
			throw new KalturaAPIException(KalturaErrors::CANNOT_REJECT_CATEGORY_ENTRY);
		}

		if($dbCategoryEntry->getStatus() != CategoryEntryStatus::PENDING)
			throw new KalturaAPIException(KalturaErrors::CANNOT_REJECT_CATEGORY_ENTRY_SINCE_IT_IS_NOT_PENDING);
			
		$dbCategoryEntry->setStatus(CategoryEntryStatus::REJECTED);
		$dbCategoryEntry->save();

		self::applyStatusOnChildren($entry, $categoryId, CategoryEntryStatus::REJECTED);
	}
	
	/**
	 * update privacy context from the category
	 * 
	 * @action syncPrivacyContext
	 * @param string $entryId
	 * @param int $categoryId
	 * @throws KalturaErrors::INVALID_ENTRY_ID
	 * @throws KalturaErrors::CATEGORY_NOT_FOUND
	 * @throws KalturaErrors::ENTRY_IS_NOT_ASSIGNED_TO_CATEGORY
	 */
	function syncPrivacyContextAction($entryId, $categoryId)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if (!$entry)
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_ID, $entryId);
			
		$category = categoryPeer::retrieveByPK($categoryId);
		if (!$category)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryId);
		
		$dbCategoryEntry = categoryEntryPeer::retrieveByCategoryIdAndEntryId($categoryId, $entryId);
		if(!$dbCategoryEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_IS_NOT_ASSIGNED_TO_CATEGORY);
		
		$dbCategoryEntry->setPrivacyContext($category->getPrivacyContexts());
		$dbCategoryEntry->save();
	}
}
