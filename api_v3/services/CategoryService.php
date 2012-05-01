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
		if ($category->parentId != null && //batch to index categories or to move categories might miss this category to be moved or index
			$this->getPartner()->getFeaturesStatusByType(FeatureStatusType::CATEGORY_LOCK))
			throw new KalturaAPIException(KalturaErrors::CATEGORIES_LOCKED);
			
		try
		{
			$categoryDb = new category();
			$category->toInsertableObject($categoryDb);
			$categoryDb->setPartnerId($this->getPartnerId());
			$categoryDb->save();
		}
		catch(Exception $ex)
		{
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
			
		//it is possible to not all of the sub tree is updated, 
		//and updateing fileds that will add batch job to reindex categories - might not update all sub categories.
		//batch to index categories or to move categories might miss this category to be moved or index
		if (($category->parentId != null && $category->parentId !=  $categoryDb->getParentId()) && 
			$this->getPartner()->getFeaturesStatusByType(FeatureStatusType::CATEGORY_LOCK))
			throw new KalturaAPIException(KalturaErrors::CATEGORIES_LOCKED);

		$category->id = $id; // for KalturaCategory->ValidateForUpdate
		
		if (kEntitlementUtils::getEntitlementEnforcement())
		{
			$currentKuserCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndActiveKuserId($categoryDb->getId(), kCurrentContext::$ks_kuser_id);
		
			if(!$currentKuserCategoryKuser || $currentKuserCategoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MANAGER)
				throw new KalturaAPIException(KalturaErrors::NOT_ENTITLED_TO_UPDATE_CATEGORY);
		}

		$category->toUpdatableObject($categoryDb);
		
		try
		{
			$categoryDb->save();
		}
		catch(Exception $ex)
		{
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
		if ($this->getPartner()->getFeaturesStatusByType(FeatureStatusType::CATEGORY_LOCK))
			throw new KalturaAPIException(KalturaErrors::CATEGORIES_LOCKED);
			
		$categoryDb = categoryPeer::retrieveByPK($id);
		if (!$categoryDb)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $id);
			
		if (kEntitlementUtils::getEntitlementEnforcement())
		{
			$currentKuserCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndActiveKuserId($categoryDb->getCategoryId(), kCurrentContext::$ks_kuser_id);
			if(!$currentKuserCategoryKuser || $currentKuserCategoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MANAGER)
				throw new KalturaAPIException(KalturaErrors::NOT_ENTITLED_TO_UPDATE_CATEGORY);
		}
		
		$this->getPartner()->addFeaturesStatus(FeatureStatusType::CATEGORY_LOCK, 1);
		
		try
		{
			$categoryDb->setDeletedAt(time());	
			$this->getPartner()->removeFeaturesStatus(FeatureStatusType::CATEGORY_LOCK);
		}
		catch(Exception $ex)
		{
			$this->getPartner()->removeFeaturesStatus(FeatureStatusType::CATEGORY_LOCK);
			throw $ex;
		}
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
		$pager->attachToCriteria($c);
		$dbList = categoryPeer::doSelect($c);
		$totalCount = $c->getRecordsCount();
		
		$list = KalturaCategoryArray::fromCategoryArray($dbList);
		
		$response = new KalturaCategoryListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;
	}
	
	/**
	 * Index Category by id
	 * 
	 * @action index
	 * @param int $id
	 * @param bool $shouldUpdate
	 * @return int category int id
	 */
	function indexAction($id, $shouldUpdate = true)
	{
		$categoryDb = categoryPeer::retrieveByPK($id);
		if (!$categoryDb)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $id);
			
		if (!$shouldUpdate)
		{
			$categoryDb->setUpdatedAt(time());
			$categoryDb->save();
			
			return $categoryDb->getId();
		}
		
		$categoryDb->setIsIndex(true);
		
		//update category full ids and inherited parent id should come first.
		$categoryDb->reSetFullIds();
		$categoryDb->reSetInheritedParentId();
		$categoryDb->reSetDepth();
		$categoryDb->reSetFullName();
		$categoryDb->reSetEntriesCount();
		$categoryDb->reSetPendingMembersCount();
		$categoryDb->reSetPendingMembersCount();

		//TODO should skip all category logic 
		$categoryDb->save();
		
		return $categoryDb->getId();
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
	
	/**
	 * Unlock categories - this is only for debuging - and should not be uploaded in flacon version
	 * 
	 * @action unlockCategories
	 */
	function unlockCategoriesAction()
	{
		//TODO - remove this action! should not be uploaded in Falcon version, this is only for QA and front team to make work easy
		$this->getPartner()->removeFeaturesStatus(FeatureStatusType::CATEGORY_LOCK);
	}
}