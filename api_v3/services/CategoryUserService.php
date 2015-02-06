<?php

/**
 * Add & Manage CategoryUser - membership of a user in a category
 *
 * @service categoryUser
 */
class CategoryUserService extends KalturaBaseService
{
	/**
	 * Add new CategoryUser
	 * 
	 * @action add
	 * @param KalturaCategoryUser $categoryUser
	 * @return KalturaCategoryUser
	 */
	function addAction(KalturaCategoryUser $categoryUser)
	{
		$dbCategoryKuser = $categoryUser->toInsertableObject();
		/* @var $dbCategoryKuser categoryKuser */
		$category = categoryPeer::retrieveByPK($categoryUser->categoryId);
		if (!$category)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryUser->categoryId);						
		
		$currentKuserCategoryKuser = categoryKuserPeer::isKuserHasPermissionInCategory($categoryUser->categoryId, kCurrentContext::getCurrentKsKuserId());
		if (!kEntitlementUtils::getEntitlementEnforcement())
		{
			$dbCategoryKuser->setStatus(CategoryKuserStatus::ACTIVE);	
			$dbCategoryKuser->setPermissionLevel($categoryUser->permissionLevel);
		}
		elseif ($currentKuserCategoryKuser && $currentKuserCategoryKuser->getPermissionLevel() == CategoryKuserPermissionLevel::MANAGER)
		{
			//Current Kuser is manager
			$dbCategoryKuser->setStatus(CategoryKuserStatus::ACTIVE);
		}
		elseif ($category->getUserJoinPolicy() == UserJoinPolicyType::AUTO_JOIN)
		{
			$dbCategoryKuser->setPermissionLevel($category->getDefaultPermissionLevel());
			$dbCategoryKuser->setStatus(CategoryKuserStatus::ACTIVE);
		}
		elseif ($category->getUserJoinPolicy() == UserJoinPolicyType::REQUEST_TO_JOIN)
		{
			$dbCategoryKuser->setPermissionLevel($category->getDefaultPermissionLevel());
			$dbCategoryKuser->setStatus(CategoryKuserStatus::PENDING);
		}
		else
		{
			throw new KalturaAPIException(KalturaErrors::CATEGORY_USER_JOIN_NOT_ALLOWED, $categoryUser->categoryId);	
		}
				
		$dbCategoryKuser->setCategoryFullIds($category->getFullIds());
		$dbCategoryKuser->setPartnerId($this->getPartnerId());
		$dbCategoryKuser->save();
		
		$categoryUser->fromObject($dbCategoryKuser);
		return $categoryUser;
	}
	
	/**
	 * Get CategoryUser by id
	 * 
	 * @action get
	 * @param int $categoryId
	 * @param string $userId
	 * @return KalturaCategoryUser
	 */
	function getAction($categoryId, $userId)
	{
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
		$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $userId);
		if (!$kuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);
			
		$category = categoryPeer::retrieveByPK($categoryId);
		if (!$category)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryId);						

		if($category->getInheritanceType() == InheritanceType::INHERIT)
			$categoryId = $category->getInheritedParentId();
					
		$dbCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndKuserId($categoryId, $kuser->getId());
		if (!$dbCategoryKuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_CATEGORY_USER_ID, $categoryId, $userId);
			
		$categoryUser = new KalturaCategoryUser();
		$categoryUser->fromObject($dbCategoryKuser);
		
		return $categoryUser;
	}
	
	/**
	 * Update CategoryUser by id
	 * 
	 * @action update
	 * @param int $categoryId
	 * @param string $userId
	 * @param KalturaCategoryUser $categoryUser
	 * @param bool $override - to override manual changes
	 * @return KalturaCategoryUser
	 */
	function updateAction($categoryId, $userId, KalturaCategoryUser $categoryUser, $override = false)
	{
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
		$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $userId);
		if (!$kuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);
		
		$dbCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndKuserId($categoryId, $kuser->getId());
		if (!$dbCategoryKuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_CATEGORY_USER_ID, $categoryId, $userId);
			
		if(!$override && 
			($categoryUser->updateMethod == null || $categoryUser->updateMethod == KalturaUpdateMethodType::AUTOMATIC) && 
			$dbCategoryKuser->getUpdateMethod() == KalturaUpdateMethodType::MANUAL)
			throw new KalturaAPIException(KalturaErrors::CANNOT_OVERRIDE_MANUAL_CHANGES);
		
		$dbCategoryKuser = $categoryUser->toUpdatableObject($dbCategoryKuser);
				
		$dbCategoryKuser->save();
		
		$categoryUser->fromObject($dbCategoryKuser);
		return $categoryUser;
		
	}
	
	/**
	 * Delete a CategoryUser
	 * 
	 * @action delete
	 * @param int $categoryId
	 * @param string $userId
	 */
	function deleteAction($categoryId, $userId)
	{
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
		$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $userId);
		
		if (!$kuser)
		{	
			if (kCurrentContext::$master_partner_id != Partner::BATCH_PARTNER_ID)
				throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);
			
			kuserPeer::setUseCriteriaFilter(false);
			$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $userId);
			kuserPeer::setUseCriteriaFilter(true);
			
			if (!$kuser)
				throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);
		}
			
		$dbCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndKuserId($categoryId, $kuser->getId());
		/* @var $dbCategoryKuser categoryKuser */
		if (!$dbCategoryKuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_CATEGORY_USER_ID, $categoryId, $userId);
			
		$category = categoryPeer::retrieveByPK($categoryId);
		if (!$category)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryId);						

		if ($category->getInheritanceType() == InheritanceType::INHERIT && kCurrentContext::$master_partner_id != Partner::BATCH_PARTNER_ID)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_INHERIT_MEMBERS, $categoryId);		
		
		// only manager can remove memnger or users remove himself
		$currentKuserCategoryKuser = categoryKuserPeer::isKuserHasPermissionInCategory($dbCategoryKuser->getCategoryId());
		if((!$currentKuserCategoryKuser || 
			($currentKuserCategoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MANAGER &&
			 kCurrentContext::$ks_uid != $userId)) &&
			 kCurrentContext::$ks_partner_id != Partner::BATCH_PARTNER_ID &&
			 kEntitlementUtils::getEntitlementEnforcement())
			throw new KalturaAPIException(KalturaErrors::CANNOT_UPDATE_CATEGORY_USER);
		
		if($dbCategoryKuser->getKuserId() == $category->getKuserId() &&
			kCurrentContext::$ks_partner_id != Partner::BATCH_PARTNER_ID)
			throw new KalturaAPIException(KalturaErrors::CANNOT_UPDATE_CATEGORY_USER_OWNER);
			
		$dbCategoryKuser->setStatus(CategoryKuserStatus::DELETED);
		$dbCategoryKuser->save();
	} 
	
	/**
	 * activate CategoryUser
	 * 
	 * @action activate
	 * @param int $categoryId
	 * @param string $userId
	 * @return KalturaCategoryUser
	 */
	function activateAction($categoryId, $userId)
	{
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
		$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $userId);
		if (!$kuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);
			
		$dbCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndKuserId($categoryId, $kuser->getId());
		if (!$dbCategoryKuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_CATEGORY_USER_ID, $categoryId, $userId);
		
		$currentKuserCategoryKuser = categoryKuserPeer::isKuserHasPermissionInCategory($dbCategoryKuser->getCategoryId(), kCurrentContext::getCurrentKsKuserId());
		if(kEntitlementUtils::getEntitlementEnforcement() &&
			(!$currentKuserCategoryKuser || $currentKuserCategoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MANAGER))
			throw new KalturaAPIException(KalturaErrors::CANNOT_UPDATE_CATEGORY_USER);
		
		$dbCategoryKuser->setStatus(CategoryKuserStatus::ACTIVE);
		$dbCategoryKuser->save();
		
		$categoryUser = new KalturaCategoryUser();
		$categoryUser->fromObject($dbCategoryKuser);
		return $categoryUser;
	} 
	
	/**
	 * reject CategoryUser
	 * 
	 * @action deactivate
	 * @param int $categoryId
	 * @param string $userId
	 * @return KalturaCategoryUser
	 */
	function deactivateAction($categoryId, $userId)
	{
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
		$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $userId);
		if (!$kuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);
			
		$dbCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndKuserId($categoryId, $kuser->getId());
		if (!$dbCategoryKuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_CATEGORY_USER_ID, $categoryId, $userId);
		
		$currentKuserCategoryKuser = categoryKuserPeer::isKuserHasPermissionInCategory($dbCategoryKuser->getCategoryId(), kCurrentContext::getCurrentKsKuserId());
		if(kEntitlementUtils::getEntitlementEnforcement() &&
			(!$currentKuserCategoryKuser || 
			($currentKuserCategoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MANAGER &&
			 kCurrentContext::$ks_uid != $userId)))
			throw new KalturaAPIException(KalturaErrors::CANNOT_UPDATE_CATEGORY_USER);
		
		$dbCategoryKuser->setStatus(CategoryKuserStatus::NOT_ACTIVE);
		$dbCategoryKuser->save();
		
		$categoryUser = new KalturaCategoryUser();
		$categoryUser->fromObject($dbCategoryKuser);
		return $categoryUser;
	} 
	
	
	/**
	 * List all categories
	 * 
	 * @action list
	 * @param KalturaCategoryUserFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaCategoryUserListResponse
	 * @throws KalturaErrors::MUST_FILTER_USERS_OR_CATEGORY
	 */
	function listAction(KalturaCategoryUserFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if(!($filter->categoryIdEqual || $filter->categoryIdIn || $filter->categoryFullIdsStartsWith || $filter->categoryFullIdsEqual || $filter->userIdIn || $filter->userIdEqual || $filter->relatedGroupsByUserId))
			throw new KalturaAPIException(KalturaErrors::MUST_FILTER_USERS_OR_CATEGORY);			
		
		if (!$filter)
			$filter = new KalturaCategoryUserFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();

		if($filter->userIdIn)
		{
			$usersIds = explode(',', $filter->userIdIn);
			$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;

			$c = new Criteria();
			$c->add(kuserPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
			$c->add(kuserPeer::PUSER_ID, $usersIds, Criteria::IN);
			$kusers = kuserPeer::doSelect($c);
			
			$usersIds = array();
			foreach($kusers as $kuser)
			{
				/* @var $kuser kuser */
				$usersIds[] = $kuser->getId();
			}
				
			$filter->userIdIn = implode(',', $usersIds);
		}

		if ($filter->relatedGroupsByUserId){
			$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
			$userIds = array();
			$kgroupIds = KuserKgroupPeer::retrieveKgroupIdsByKuserId($filter->relatedGroupsByUserId);
			if (!is_null($kgroupIds) && is_array($kgroupIds))
				$userIds = $kgroupIds;
			$userIds[] = $filter->relatedGroupsByUserId;
			$c = new Criteria();
			$c->add(kuserPeer::PARTNER_ID, $partnerId);
			$c->add(kuserPeer::PUSER_ID, $userIds, Criteria::IN);

			$kusers = kuserPeer::doSelect($c);

			$userIds = array();
			foreach($kusers as $kuser)
			{
				/* @var $kuser kuser */
				$userIds[] = $kuser->getId();
			}

			// if userIdIn is also set in the filter need to intersect the two arrays.
			if(isset($filter->userIdIn)){
				$curUserIds = explode(',',$filter->userIdIn);
				$userIds = array_intersect($curUserIds, $userIds);
			}

			$filter->userIdIn = implode(',', $userIds);
		}
		
		if($filter->userIdEqual)
		{
			$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
			
			$c = new Criteria();
			$c->add(kuserPeer::PARTNER_ID, $partnerId);
			$c->add(kuserPeer::PUSER_ID, $filter->userIdEqual);
			
			if (kCurrentContext::$ks_partner_id == Partner::BATCH_PARTNER_ID) //batch should be able to get categoryUser of deleted users.
				kuserPeer::setUseCriteriaFilter(false);

			// in case of more than one deleted kusers - get the last one
			$c->addDescendingOrderByColumn(kuserPeer::UPDATED_AT);

			$kuser = kuserPeer::doSelectOne($c);
			kuserPeer::setUseCriteriaFilter(true);
			
			if (!$kuser)
			{
				KalturaLog::debug('User not found');
				$response = new KalturaCategoryUserListResponse();
        		$response->objects = new KalturaCategoryUserArray();
        		$response->totalCount = 0;
        		
        		return $response;
			}
				
			$filter->userIdEqual = $kuser->getId();
		}	

		$categories = array();
		if ($filter->categoryIdEqual)
		{
			$categories[] = categoryPeer::retrieveByPK($filter->categoryIdEqual);
		}
		elseif($filter->categoryIdIn)
		{
			$categories = categoryPeer::retrieveByPKs(explode(',', $filter->categoryIdIn));
		}
		
		$categoriesInheritanceRoot = array();
		foreach ($categories as $category)
		{
			/* @var $category category */
			if(is_null($category))
				continue;
				
			if($category->getInheritanceType() == InheritanceType::INHERIT)
			{
				if($filter->categoryDirectMembers && kCurrentContext::$master_partner_id == Partner::BATCH_PARTNER_ID)
				{
					$categoriesInheritanceRoot[$category->getId()] = $category->getId();
				}
				else
				{
					//if category inheris members - change filter to -> inherited from parent id = category->getIheritedParent
					$categoriesInheritanceRoot[$category->getInheritedParentId()] = $category->getInheritedParentId();	
				}
			}
			else
			{
				$categoriesInheritanceRoot[$category->getId()] = $category->getId();
			}
		}
		$filter->categoryDirectMembers = null;
		$filter->categoryIdEqual = null;
		$filter->categoryIdIn = implode(',', $categoriesInheritanceRoot);

		//if filter had categories that doesn't exists or not entitled - should return 0 objects. 
		if(count($categories) && !count($categoriesInheritanceRoot))
		{
			$response = new KalturaCategoryUserListResponse();
			$response->totalCount = 0;
			
			return $response;
		}
		
		$categoryKuserFilter = new categoryKuserFilter();
		$filter->toObject($categoryKuserFilter);
		
		$c = KalturaCriteria::create(categoryKuserPeer::OM_CLASS);
		$categoryKuserFilter->attachToCriteria($c);
		$pager->attachToCriteria($c);
		$c->applyFilters();
		
		$list = categoryKuserPeer::doSelect($c);
		$totalCount = $c->getRecordsCount();
		
		$newList = KalturaCategoryUserArray::fromDbArray($list);
		
		$response = new KalturaCategoryUserListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		
		return $response;
	}
	
	/**
	 * Copy all memeber from parent category
	 * 
	 * @action copyFromCategory
	 * @param int $categoryId
	 */
	public function copyFromCategoryAction($categoryId)
	{
		if (kEntitlementUtils::getEntitlementEnforcement())
			throw new KalturaAPIException(KalturaErrors::CANNOT_UPDATE_CATEGORY_USER);
		
		$categoryDb = categoryPeer::retrieveByPK($categoryId);
		if (!$categoryDb)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryId);

		if($categoryDb->getParentId() == null)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_DOES_NOT_HAVE_PARENT_CATEGORY);
		
		$categoryDb->copyCategoryUsersFromParent($categoryDb->getParentId());
	}
	
	/**
	 * Index CategoryUser by userid and category id
	 * 
	 * @action index
	 * @param string $userId
	 * @param int $categoryId
	 * @param bool $shouldUpdate
	 * @throws KalturaErrors::INVALID_CATEGORY_USER_ID
	 * @return int
	 */
	public function indexAction($userId, $categoryId, $shouldUpdate = true)
	{
		if(kEntitlementUtils::getEntitlementEnforcement())
			throw new KalturaAPIException(KalturaErrors::CANNOT_INDEX_OBJECT_WHEN_ENTITLEMENT_IS_ENABLE);
		
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
		$kuser = kuserPeer::getActiveKuserByPartnerAndUid($partnerId, $userId);

		if(!$kuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
			
		$dbCategoryKuser = categoryKuserPeer::isKuserHasPermissionInCategory($categoryId, $kuser->getId(), null, null, false);
		if(!$dbCategoryKuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_CATEGORY_USER_ID);
			
		if (!$shouldUpdate)
		{
			$dbCategoryKuser->setUpdatedAt(time());
			$dbCategoryKuser->save();
			
			return $dbCategoryKuser->getId();
		}
				
		$dbCategoryKuser->reSetCategoryFullIds();
		$dbCategoryKuser->reSetScreenName();
		$dbCategoryKuser->save();
		
		return $dbCategoryKuser->getId();
	}
}