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
		
		if ($category->getPrivacyContext() == kEntitlementUtils::DEFAULT_CONTEXT)
		{
			$entry->setCategories($entry->getCategories() . ',' . $category->getFullName());
			$entry->save();	
		}
		else
		{
			$dbCategoryEntry = new categoryEntry();
			$categoryEntry->toInsertableObject($dbCategoryEntry);
			$dbCategoryEntry->setPartnerId(kCurrentContext::$ks_partner_id);
			$dbCategoryEntry->save();
		}

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
		$categoryEntry = new KalturaCategoryEntry();
		$categoryEntry->entryId = $entryId;
		$categoryEntry->categoryId = $categoryId;		
		
		$categoryEntry->validateForUpdate();
		
		$entry = entryPeer::retrieveByPK($categoryEntry->entryId);
		if (!$entry)
			throw new APIException(KalturaErrors::INVALID_ENTRY_ID, $categoryEntry->entryId);
			
		$category = categoryPeer::retrieveByPK($categoryEntry->categoryId);
		if (!$category)
			throw new APIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryEntry->categoryId);
		
		//validate user is entiteld to assign entry to this category 
		$categoryKuser = categoryKuserPeer::retrieveByCategoryIdAndActiveKuserId($categoryEntry->categoryId, kCurrentContext::$ks_kuser_id);
		if(kEntitlementUtils::getEntitlementEnforcement() && (!$categoryKuser || $categoryKuser->getPermissionLevel() == CategoryKuserPermissionLevel::MEMBER))
			throw new ApiException(KalturaErrors::CANNOT_REMOVE_ENTRY_FROM_CATEGORY);
			
			
		if ($category->getPrivacyContext() == kEntitlementUtils::DEFAULT_CONTEXT)
		{
			$categories = $entry->getCategories();
		
			$categoriesArr = explode(entry::ENTRY_CATEGORY_SEPARATOR, $categories);
	
			$keyToRemove = false;
			foreach ($categoriesArr as $key => $categoryOnEntey)
			{
				if($categoryOnEntey == $category->getFullName())
				{
					$keyToRemove = true;
					break;
				}
			}
			
			if($keyToRemove)
				unset($categoriesArr[$key]);
			
			$entry->setCategories(implode(entry::ENTRY_CATEGORY_SEPARATOR, $categoriesArr));
			$entry->save();
				
		}
		else
		{
			$dbCategoryEntry = categoryEntryPeer::retrieveByCategoryIdAndEntryId($categoryEntry->categoryId, $categoryEntry->entryId);
			if(!$dbCategoryEntry)
				throw new APIException(KalturaErrors::ENTRY_IS_NOT_ASSIGNED_TO_CATEGORY);
			
			$dbCategoryEntry->delete();
		}
		
		return $categoryEntry;
	}
	
	/**
	 * List all categoryEntry
	 * 
	 * @action list
	 * @return KalturaCategoryEntryListResponse
	 */
	function listAction(KalturaCategoryEntryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if ($filter === null)
			$filter = new KalturaCategoryEntryFilter();
			
		if ($pager == null)
			$pager = new KalturaFilterPager();
		
		if ($filter->entryIdEqual == null && 
			$filter->entryIdIn == null &&
			$filter->categoryIdEqual == null &&
			$filter->categoryIdIn == null)
			throw new APIException(KalturaErrors::MUST_FILTER_ENTRY_OR_CATEGORY);
			
		$categoryEntryFilter = new categoryEntryFilter();
		$filter->toObject($categoryEntryFilter);

		$c = new Criteria();
		$categoryEntryFilter->attachToCriteria($c);
		$dbList = categoryEntryPeer::doSelect($c);
		$totalCount = categoryEntryPeer::doCount($c);
		
		$list = KalturaCategoryEntryArray::fromCategoryEntryArray($dbList);
		$response = new KalturaCategoryEntryListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;
	}
}