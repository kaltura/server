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

		$maxUserPerCategory=kConf::get('max_users_per_category');
		if($category->getMembersCount() >= $maxUserPerCategory)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_MAX_USER_REACHED,$maxUserPerCategory);

		$lockKey = 'categoryUser_add_' . $categoryUser->categoryId . '_' . $categoryUser->userId;
		$dbCategoryKuser = kLock::runLocked($lockKey, array($this, 'addCategoryUserImpl'), array($categoryUser, $dbCategoryKuser, $category));

		$categoryUser->fromObject($dbCategoryKuser, $this->getResponseProfile());
		return $categoryUser;
	}

	function addCategoryUserImpl(KalturaCategoryUser $categoryUser, $dbCategoryKuser, $category)
	{
		$currentKuserCategoryKuser = categoryKuserPeer::retrievePermittedKuserInCategory($categoryUser->categoryId, kCurrentContext::getCurrentKsKuserId());
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
		return $dbCategoryKuser;
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
		$categoryUser->fromObject($dbCategoryKuser, $this->getResponseProfile());
		
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
		
		$categoryUser->fromObject($dbCategoryKuser, $this->getResponseProfile());
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
		$currentKuserCategoryKuser = categoryKuserPeer::retrievePermittedKuserInCategory($dbCategoryKuser->getCategoryId());
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
		
		$currentKuserCategoryKuser = categoryKuserPeer::retrievePermittedKuserInCategory($dbCategoryKuser->getCategoryId(), kCurrentContext::getCurrentKsKuserId());
		if(kEntitlementUtils::getEntitlementEnforcement() &&
			(!$currentKuserCategoryKuser || $currentKuserCategoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MANAGER))
			throw new KalturaAPIException(KalturaErrors::CANNOT_UPDATE_CATEGORY_USER);
		
		$dbCategoryKuser->setStatus(CategoryKuserStatus::ACTIVE);
		$dbCategoryKuser->save();
		
		$categoryUser = new KalturaCategoryUser();
		$categoryUser->fromObject($dbCategoryKuser, $this->getResponseProfile());
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
		
		$currentKuserCategoryKuser = categoryKuserPeer::retrievePermittedKuserInCategory($dbCategoryKuser->getCategoryId(), kCurrentContext::getCurrentKsKuserId());
		if(kEntitlementUtils::getEntitlementEnforcement() &&
			(!$currentKuserCategoryKuser || 
			($currentKuserCategoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MANAGER &&
			 kCurrentContext::$ks_uid != $userId)))
			throw new KalturaAPIException(KalturaErrors::CANNOT_UPDATE_CATEGORY_USER);
		
		$dbCategoryKuser->setStatus(CategoryKuserStatus::NOT_ACTIVE);
		$dbCategoryKuser->save();
		
		$categoryUser = new KalturaCategoryUser();
		$categoryUser->fromObject($dbCategoryKuser, $this->getResponseProfile());
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
		if (!$filter || !($filter->categoryIdEqual || $filter->categoryIdIn || $filter->categoryFullIdsStartsWith || $filter->categoryFullIdsEqual || $filter->userIdIn || $filter->userIdEqual || $filter->relatedGroupsByUserId))
			throw new KalturaAPIException(KalturaErrors::MUST_FILTER_USERS_OR_CATEGORY);	
			
		if(!$pager)
			$pager = new KalturaFilterPager();		
			
		return $filter->getListResponse($pager, $this->getResponseProfile());
	}
	
	/**
	 * Copy all member from parent category
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
			
		$dbCategoryKuser = categoryKuserPeer::retrievePermittedKuserInCategory($categoryId, $kuser->getId(), null, false);
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
