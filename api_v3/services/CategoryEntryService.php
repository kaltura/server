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
		parent::applyPartnerFilterForClass(new categoryPeer());
		parent::applyPartnerFilterForClass(new entryPeer());	
	}
	
	/**
	 * Add new CategoryUser
	 * 
	 * @action add
	 * @param KalturaCategoryEntry $categoryEntry
	 * @return KalturaCategoryEntry
	 */
	function addAction(KalturaCategoryEntry $categoryEntry)
	{
		$categoryEntry->validateForInsert();
		
		$entry = entryPeer::retrieveByPK($categoryEntry->entryId);
		if (!$entry)
			throw new APIException(KalturaErrors::INVALID_ENTRY_ID, $categoryEntry->entryId);
			
		$category = categoryPeer::retrieveByPK($categoryEntry->categoryId);
		if (!$category)
			throw new APIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryEntry->categoryId);
			
		//validate user is entiteld to assign entry to this category 
		if (kEntitlementUtils::getEntitlementEnforcement() && $category->getContributionPolicy() != ContributionPolicyType::ALL)
		{
			$categoryKuser = categoryKuserPeer::retrieveByCategoryIdAndActiveKuserId($categoryEntry->categoryId, kCurrentContext::$ks_kuser_id);
			if(!$categoryKuser || $categoryKuser->getPermissionLevel() == CategoryKuserPermissionLevel::MEMBER)
				throw new APIException(KalturaErrors::CANNOT_ASSIGN_ENTRY_TO_CATEGORY);
		}
		
		$categoryEntryExists = categoryEntryPeer::retrieveByCategoryIdAndEntryId($categoryEntry->categoryId, $categoryEntry->entryId);
		if($categoryEntryExists)
			throw new APIException(KalturaErrors::CATEGORY_ENTRY_ALREADY_EXISTS);
		
		$dbCategoryEntry = new categoryEntry();
		$categoryEntry->toInsertableObject($dbCategoryEntry);
		$dbCategoryEntry->setPartnerId(kCurrentContext::$ks_partner_id);
		$dbCategoryEntry->save();

		return $categoryEntry;
	}
	
	/**
	 * Add new CategoryUser
	 * 
	 * @action delete
	 * @param string $entryId
	 * @param int $categoryId
	 * @return KalturaCategoryEntry
	 */
	function deleteAction($entryId, $categoryId)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if (!$entry)
			throw new APIException(KalturaErrors::INVALID_ENTRY_ID, $entryId);
			
		$category = categoryPeer::retrieveByPK($categoryId);
		if (!$category)
			throw new APIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryId);
		
		//validate user is entiteld to assign entry to this category 
		$categoryKuser = categoryKuserPeer::retrieveByCategoryIdAndActiveKuserId($categoryId, kCurrentContext::$ks_kuser_id);
		if(kEntitlementUtils::getEntitlementEnforcement() && (!$categoryKuser || $categoryKuser->getPermissionLevel() == CategoryKuserPermissionLevel::MEMBER))
			throw new ApiException(KalturaErrors::CANNOT_REMOVE_ENTRY_FROM_CATEGORY);
			
			
		if ($category->getPrivacyContext() == kEntitlementUtils::DEFAULT_CONTEXT)
		{
			$categories = $entry->getCategories();
		
			$categoriesArr = explode(entry::ENTRY_CATEGORY_SEPARATOR, $categories);
	
			foreach ($categoriesArr as $key => $categoryOnEntey)
			{
				if($categoryOnEntey == $category->getFullName())
				{
					unset($categoriesArr[$key]);
					break;
				}
			}

			$entry->setCategories(implode(entry::ENTRY_CATEGORY_SEPARATOR, $categoriesArr));
			$entry->save();
				
		}
		else
		{
			$dbCategoryEntry = categoryEntryPeer::retrieveByCategoryIdAndEntryId($categoryId, $entryId);
			if(!$dbCategoryEntry)
				throw new APIException(KalturaErrors::ENTRY_IS_NOT_ASSIGNED_TO_CATEGORY);
			
			$dbCategoryEntry->delete();
		}
	}
	
	/**
	 * List all categoryEntry
	 * 
	 * @action list
	 * @return KalturaCategoryEntryListResponse
	 */
	function listAction(KalturaCategoryEntryFilter $filter = null)
	{
		if ($filter === null)
			$filter = new KalturaCategoryEntryFilter();
		
		if ($filter->entryIdEqual == null)
			throw new APIException(KalturaErrors::MUST_FILTER_ENTRY_ID_EQUAL);
			
		$categoryEntryFilter = new categoryEntryFilter();
		$filter->toObject($categoryEntryFilter);

		$c = KalturaCriteria::create(categoryEntryPeer::OM_CLASS);
		$categoryEntryFilter->attachToCriteria($c);
		$dbCategoriesEntry = categoryEntryPeer::doSelect($c);

		//remove unlisted categories: display in search is set to members only
		$categoriesIds = array();
		foreach ($dbCategoriesEntry as $dbCategoryEntry)
			$categoriesIds[] = $dbCategoryEntry->getCategoryId();
		
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS);
		$c->addSelectColumn(categoryPeer::ID);
		$c->addAnd(categoryPeer::ID, $categoriesIds, Criteria::IN);
		$stmt = categoryPeer::doSelectStmt($c);
		$categoryIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
		
		foreach ($dbCategoriesEntry as $key => $dbCategoryEntry)
		{
			if(!in_array($dbCategoryEntry->getCategoryId(), $categoryIds))
			{
				KalturaLog::debug('Category [' . print_r($dbCategoryEntry->getCategoryId(),true) . '] is not listed to user');
				unset($dbCategoriesEntry[$key]);
			}
		}
		
		$categoryEntrylist = KalturaCategoryEntryArray::fromCategoryEntryArray($dbCategoriesEntry);
		$response = new KalturaCategoryEntryListResponse();
		$response->objects = $categoryEntrylist;
		$response->totalCount = count($categoryEntrylist); // no pager since category entry is limited to ENTRY::MAX_CATEGORIES_PER_ENTRY
		return $response;
	}
}