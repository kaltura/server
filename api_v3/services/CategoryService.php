<?php

/**
 * Add & Manage Categories
 *
 * @service category
 */
class CategoryService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName); 	
	}
	
	/**
	 * Add new Category
	 * 
	 * @action add
	 * @param KalturaCategory $category
	 * @return KalturaCategory
	 */
	function addAction(KalturaCategory $category)
	{		
		$category->validatePropertyMinLength("name", 1);
		$category->validatePropertyMaxLength("name", categoryPeer::MAX_CATEGORY_NAME);
		$category->validateParentId($category);

		if ($this->getPartner()->isCategoriesLocked())
			throw new KalturaAPIException(KalturaErrors::CATEGORIES_LOCKED, Partner::CATEGORIES_LOCK_TIMEOUT);
			
		try
		{
			$this->getPartner()->lockCategories(); 
			$categoryDb = new category();
			$category->toInsertableObject($categoryDb);
			$categoryDb->setPartnerId($this->getPartnerId());
			$categoryDb->save();
			$this->getPartner()->unlockCategories();
		}
		catch(Exception $ex)
		{
			$this->getPartner()->unlockCategories();
			if ($ex instanceof kCoreException)
				$this->handleCoreException($ex, $categoryDb, $category);
			else
				throw $ex;
		}
		
		$category->fromObject($categoryDb);
		return $category;
	}
	
	/**
	 * Get Category by id
	 * 
	 * @action get
	 * @param int $id
	 * @return KalturaCategory
	 */
	function getAction($id)
	{
		$categoryDb = categoryPeer::retrieveByPK($id);
		if (!$categoryDb)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $id);
		
		$category = new KalturaCategory();
		$category->fromObject($categoryDb);
		return $category;
	}
	
	/**
	 * Update Category
	 * 
	 * @action update
	 * @param int $id
	 * @param KalturaCategory $category
	 * @return KalturaCategory
	 */
	function updateAction($id, KalturaCategory $category)
	{
		$categoryDb = categoryPeer::retrieveByPK($id);
		if (!$categoryDb)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $id);
			
		$category->id = $id; // for KalturaCategory->ValidateForUpdate
		
		if (kEntitlementUtils::getEntitlementEnforcement())
		{
			$currentKuserCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndActiveKuserId($categoryDb->getId(), kCurrentContext::$ks_kuser_id);
		
			if(!$currentKuserCategoryKuser || $currentKuserCategoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MANAGER)
				throw new KalturaAPIException(KalturaErrors::NOT_ENTITLED_TO_UPDATE_CATEGORY);
		}
		if ($category->name !== null)
		{
			$category->validatePropertyMinLength("name", 1);
			$category->validatePropertyMaxLength("name", categoryPeer::MAX_CATEGORY_NAME);
		}
		if ($category->parentId !== null)
			$category->validateParentId($category);
			
		if ($this->getPartner()->isCategoriesLocked())
			throw new KalturaAPIException(KalturaErrors::CATEGORIES_LOCKED, Partner::CATEGORIES_LOCK_TIMEOUT);
			
		
		$category->toUpdatableObject($categoryDb);
		
		$this->getPartner()->lockCategories();	
		try
		{
			$categoryDb->save();
			$this->getPartner()->unlockCategories();
		}
		catch(Exception $ex)
		{
			$this->getPartner()->unlockCategories();
			if ($ex instanceof kCoreException)
				$this->handleCoreException($ex, $categoryDb, $category);
			else
				throw $ex;
		}
		$category = new KalturaCategory();
		$category->fromObject($categoryDb);
		return $category;
	}
	
	/**
	 * Delete a Category
	 * 
	 * @action delete
	 * @param int $id
	 */
	function deleteAction($id)
	{
		$categoryDb = categoryPeer::retrieveByPK($id);
		if (!$categoryDb)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $id);
		
		if (kEntitlementUtils::getEntitlementEnforcement())
		{
			$currentKuserCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndActiveKuserId($categoryDb->getCategoryId(), kCurrentContext::$ks_kuser_id);
			if(!$currentKuserCategoryKuser || $currentKuserCategoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MANAGER)
				throw new KalturaAPIException(KalturaErrors::NOT_ENTITLED_TO_UPDATE_CATEGORY);
		}
			
		$categoryDb->setDeletedAt(time());
	} 
	
	/**
	 * List all categories
	 * 
	 * @action list
	 * @return KalturaCategoryListResponse
	 */
	function listAction(KalturaCategoryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if ($filter === null)
			$filter = new KalturaCategoryFilter();
			
		if ($pager == null)
		{
			$pager = new KalturaFilterPager();
			
			//before falcon we didn’t have a pager for action category->list, 
			//and since we added a pager – and remove the limit for partners categories, 
			//for backward compatibility this will be the page size. 
			$pager->pageIndex = 1;
			$pager->pageSize = partner::MAX_NUMBER_OF_CATEGORIES;
		}
			
		if ($filter->orderBy === null)
			$filter->orderBy = KalturaCategoryOrderBy::DEPTH_ASC;
			
		$categoryFilter = new categoryFilter();
		
		$filter->toObject($categoryFilter);

		$c = KalturaCriteria::create(categoryPeer::OM_CLASS);
		 
		$categoryFilter->attachToCriteria($c);
		
		$dbList = categoryPeer::doSelect($c);
		$totalCount = $c->getRecordsCount();
		
		$list = KalturaCategoryArray::fromCategoryArray($dbList);
		$response = new KalturaCategoryListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;
	}
	
	private function handleCoreException(kCoreException $ex, category $categoryDb, KalturaCategory $category)
	{
		switch($ex->getCode())
		{
			case kCoreException::DUPLICATE_CATEGORY:
				throw new KalturaAPIException(KalturaErrors::DUPLICATE_CATEGORY, $categoryDb->getFullName());
				
			case kCoreException::PARENT_ID_IS_CHILD:
				throw new KalturaAPIException(KalturaErrors::PARENT_CATEGORY_IS_CHILD, $category->parentId, $categoryDb->getId());
				
			default:
				throw $ex;
		}
	}
}