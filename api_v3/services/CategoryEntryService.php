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
	 * @throws KalturaErrors::MAX_CATEGORIES_FOR_ENTRY_REACHED
	 * @throws KalturaErrors::CATEGORY_ENTRY_ALREADY_EXISTS
	 * @throws Exception
	 * @return KalturaCategoryEntry
	 */
	function addAction(KalturaCategoryEntry $categoryEntry)
	{
		$categoryEntry->validateForInsert();

		try
		{
			$dbCategoryEntry = new categoryEntry();
			$dbCategoryEntry->add($categoryEntry->entryId, $categoryEntry->categoryId);
			$categoryEntry->toInsertableObject($dbCategoryEntry);
			$dbCategoryEntry->save();
		}
		catch (Exception $ex)
		{
			if ($ex instanceof kCoreException)
			{
				$this->handleCoreException($ex);
			}
			else
			{
				throw $ex;
			}

		}

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

	/**
	 * @param kCoreException $ex
	 * @throws KalturaAPIException
	 * @throws kCoreException
	 */
	protected function handleCoreException(kCoreException $ex)
	{
		switch ($ex->getCode())
		{
			case kCoreException::INVALID_ENTRY_ID:
				throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_ID, $ex->getData());

			case kCoreException::CATEGORY_NOT_FOUND:
				throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $ex->getData());

			case kCoreException::MAX_CATEGORIES_PER_ENTRY:
				throw new KalturaAPIException(KalturaErrors::MAX_CATEGORIES_FOR_ENTRY_REACHED, $ex->getData());

			case kCoreException::CANNOT_ASSIGN_ENTRY_TO_CATEGORY:
				throw new KalturaAPIException(KalturaErrors::CANNOT_ASSIGN_ENTRY_TO_CATEGORY);

			case kCoreException::CATEGORY_ENTRY_ALREADY_EXISTS:
				throw new KalturaAPIException(KalturaErrors::CATEGORY_ENTRY_ALREADY_EXISTS);

			default:
				throw $ex;
		}
	}
}
