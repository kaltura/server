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

		if($actionName == 'add')
		{
			categoryPeer::setIgnoreDeleted(true);
		}
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
		if($category->owner == '')
			$category->owner = null;
			
		if ($category->parentId != null && //batch to index categories or to move categories might miss this category to be moved or index
			$this->getPartner()->getFeaturesStatusByType(IndexObjectType::LOCK_CATEGORY))
			throw new KalturaAPIException(KalturaErrors::CATEGORIES_LOCKED);
			
		if($category->privacyContext != null && 
		   $category->privacyContext != '')
	   	{
			$privacyContexts = explode(',', $category->privacyContext);  
		  	
			foreach($privacyContexts as $privacyContext)
		  	{
		  		if(!preg_match('/^[a-zA-Z\d]+$/', $privacyContext) || strlen($privacyContext) < 4)
		  		{
		  			KalturaLog::err('Invalid privacy context: ' . print_r($privacyContext, true));
		   			throw new KalturaAPIException(KalturaErrors::PRIVACY_CONTEXT_INVALID_STRING, $privacyContext);
		  		}
		  	}
	   	}
			
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
		$category->fromObject($categoryDb, $this->getResponseProfile());
		
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
		$category->fromObject($categoryDb, $this->getResponseProfile());
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
		if($category->owner == '')
			$category->owner = null;
			
		$categoryDb = categoryPeer::retrieveByPK($id);
		if (!$categoryDb)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $id );
		
		if ($category->privacyContext != null && $category->privacyContext != '') 
		{
			$privacyContexts = explode ( ',', $category->privacyContext );
			
			foreach ( $privacyContexts as $privacyContext )
			{
				if (! preg_match ( '/^[a-zA-Z\d]+$/', $privacyContext ) || strlen ( $privacyContext ) < 4) 
				{
					KalturaLog::err ( 'Invalid privacy context: ' . print_r ( $privacyContext, true ) );
					throw new KalturaAPIException ( KalturaErrors::PRIVACY_CONTEXT_INVALID_STRING, $privacyContext );
				}
			}
		}
			
		//it is possible to not all of the sub tree is updated, 
		//and updateing fileds that will add batch job to reindex categories - might not update all sub categories.
		//batch to index categories or to move categories might miss this category to be moved or index
		if (($category->parentId != null && $category->parentId !=  $categoryDb->getParentId()) && 
			$this->getPartner()->getFeaturesStatusByType(IndexObjectType::LOCK_CATEGORY))
			throw new KalturaAPIException(KalturaErrors::CATEGORIES_LOCKED);

		if (kEntitlementUtils::getEntitlementEnforcement())
		{
			$currentKuserCategoryKuser = categoryKuserPeer::retrievePermittedKuserInCategory($categoryDb->getId(), kCurrentContext::getCurrentKsKuserId(), array(PermissionName::CATEGORY_EDIT));
			if(!$currentKuserCategoryKuser || $currentKuserCategoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MANAGER)
				throw new KalturaAPIException(KalturaErrors::NOT_ENTITLED_TO_UPDATE_CATEGORY);
		}
		try
		{		
			$category->toUpdatableObject($categoryDb);
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
		$category->fromObject($categoryDb, $this->getResponseProfile());
		return $category;
	}
	
	/**
	 * Delete a Category
	 * 
	 * @action delete
	 * @param int $id
	 * @param KalturaNullableBoolean $moveEntriesToParentCategory
	 */
	function deleteAction($id, $moveEntriesToParentCategory = KalturaNullableBoolean::TRUE_VALUE)
	{
		if ($this->getPartner()->getFeaturesStatusByType(IndexObjectType::LOCK_CATEGORY))
			throw new KalturaAPIException(KalturaErrors::CATEGORIES_LOCKED);

		$categoryDb = categoryPeer::retrieveByPK($id);
		if (!$categoryDb)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $id);

		if (kEntitlementUtils::getEntitlementEnforcement())
		{
			$currentKuserCategoryKuser = categoryKuserPeer::retrievePermittedKuserInCategory($categoryDb->getId(), kCurrentContext::getCurrentKsKuserId());
			if(!$currentKuserCategoryKuser || $currentKuserCategoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MANAGER)
				throw new KalturaAPIException(KalturaErrors::NOT_ENTITLED_TO_UPDATE_CATEGORY);
		}

		$this->getPartner()->addFeaturesStatus(IndexObjectType::LOCK_CATEGORY);

		try
		{
			if($moveEntriesToParentCategory)
				$categoryDb->setDeletedAt(time());
			else 
				$categoryDb->setDeletedAt(time(), 0);
				
			$categoryDb->save();	
			$this->getPartner()->removeFeaturesStatus(IndexObjectType::LOCK_CATEGORY);
		}
		catch(Exception $ex)
		{
			$this->getPartner()->removeFeaturesStatus(IndexObjectType::LOCK_CATEGORY);
			throw $ex;
		}
	} 
	
	/**
	 * List all categories
	 * 
	 * @action list
	 * @param KalturaCategoryFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaCategoryListResponse
	 */
	function listAction(KalturaCategoryFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if ($filter === null)
			$filter = new KalturaCategoryFilter();
	
		if ($pager == null)
		{
			$pager = new KalturaFilterPager();
			//before falcon we didn't have a pager for action category->list,
			//and since we added a pager - and remove the limit for partners categories,
			//for backward compatibility this will be the page size. 
			$pager->pageIndex = 1;
			$pager->pageSize = Partner::MAX_NUMBER_OF_CATEGORIES;
			KalturaCriteria::setMaxRecords(Partner::MAX_NUMBER_OF_CATEGORIES);
			baseObjectFilter::setMaxInValues(Partner::MAX_NUMBER_OF_CATEGORIES);
		}
		
		return $filter->getListResponse($pager, $this->getResponseProfile());
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
		if(kEntitlementUtils::getEntitlementEnforcement())
			throw new KalturaAPIException(KalturaErrors::CANNOT_INDEX_OBJECT_WHEN_ENTITLEMENT_IS_ENABLE);
			
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
		$categoryDb->reSetMembersCount();
		$categoryDb->reSetPendingMembersCount();
		$categoryDb->reSetPrivacyContext();
		$categoryDb->reSetDirectSubCategoriesCount();
		$categoryDb->reSetDirectEntriesCount();

		//TODO should skip all category logic 
		if(!$categoryDb->save())
			$categoryDb->indexToSearchIndex();
		
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
				
			case kCoreException::DISABLE_CATEGORY_LIMIT_MULTI_PRIVACY_CONTEXT_FORBIDDEN:
				throw new KalturaAPIException(KalturaErrors::CANNOT_SET_MULTI_PRIVACY_CONTEXT);
				
			default:
				throw $ex;
		}
	}
	
	/**
	 * Move categories that belong to the same parent category to a target categroy - enabled only for ks with disable entitlement
	 * 
	 * @action move
	 * @param string $categoryIds
	 * @param int $targetCategoryParentId
	 * @return KalturaCategoryListResponse
	 */
	function moveAction($categoryIds, $targetCategoryParentId)
	{
		if(kEntitlementUtils::getEntitlementEnforcement())
			throw new KalturaAPIException(KalturaErrors::CANNOT_MOVE_CATEGORIES_FROM_DIFFERENT_PARENT_CATEGORY);
		
		if ($this->getPartner()->getFeaturesStatusByType(IndexObjectType::LOCK_CATEGORY))
			throw new KalturaAPIException(KalturaErrors::CATEGORIES_LOCKED);
		
		$categories = explode(',', $categoryIds);
		$dbCategories = array();
		$parentId = category::CATEGORY_ID_THAT_DOES_NOT_EXIST;
		
		foreach($categories as $categoryId)
		{
			if($categoryId == '')
				continue;
				
			$dbCategory = categoryPeer::retrieveByPK($categoryId);
			if (!$dbCategory)
				throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryId);
			
			if($parentId == category::CATEGORY_ID_THAT_DOES_NOT_EXIST)
				$parentId = $dbCategory->getParentId();
				
			if($parentId != $dbCategory->getParentId())
				throw new KalturaAPIException(KalturaErrors::CANNOT_MOVE_CATEGORIES_FROM_DIFFERENT_PARENT_CATEGORY);
				
			$dbCategories[] = $dbCategory;
		}
		
		// if $targetCategoryParentId = 0 - it means that categories should be with no parent category
		if($targetCategoryParentId != 0)
		{
			$dbTargetCategory = categoryPeer::retrieveByPK($targetCategoryParentId);
			if (!$dbTargetCategory)
				throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $targetCategoryParentId);
		}		
		
		foreach ($dbCategories as $dbCategory)
		{
			$dbCategory->setParentId($targetCategoryParentId);
			$dbCategory->save();		
		}
		
	}
	/**
	 * Unlock categories
	 * 
	 * @action unlockCategories
	 */
	function unlockCategoriesAction()
	{
		$this->getPartner()->removeFeaturesStatus(IndexObjectType::LOCK_CATEGORY);
	}
}