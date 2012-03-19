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
		
		if(!PermissionPeer::isValidForPartner(PermissionName::FEATURE_ENTITLEMENT, kCurrentContext::$ks_partner_id))
			throw new KalturaAPIException ( APIErrors::SERVICE_FORBIDDEN, $this->serviceId.'->'.$this->actionName);	
			
	}
	
	/**
	 * Add new CategoryUser
	 * 
	 * @action add
	 * @param KalturaCategoryEntry $categoryEntry
	 * @return KalturaCategoryEntry
	 */
	function addAction($categoryEntry)
	{
		$categoryEntry->validateForInsert();
		
		$entry = entryPeer::retrieveByPK($categoryEntry->entryId);
		if (!$entry)
			throw new APIException(KalturaErrors::INVALID_ENTRY_ID, $categoryEntry->entryId);
			
		$category = categoryPeer::retrieveByPK($categoryEntry->categoryId);
		if (!$category)
			throw new APIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryEntry->categoryId);
		
		$entry->setCategories($entry->getCategories() . ',' . $category->getFullName());
		$entry->save();

		return $categoryEntry;
	}
	
	/**
	 * Add new CategoryUser
	 * 
	 * @action delete
	 * @param KalturaCategoryEntry $categoryEntry
	 * @return KalturaCategoryEntry
	 */
	function deleteAction($categoryEntry)
	{
		$categoryEntry->validateForUpdate();
		
		$entry = entryPeer::retrieveByPK($categoryEntry->entryId);
		if (!$entry)
			throw new APIException(KalturaErrors::INVALID_ENTRY_ID, $categoryEntry->entryId);
			
		$category = categoryPeer::retrieveByPK($categoryEntry->categoryId);
		if (!$category)
			throw new APIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryEntry->categoryId);
		
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

		return $categoryEntry;
	}
}