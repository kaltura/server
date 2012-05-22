<?php

/**
 * Add & Manage CategoryUser - membership of a user in a category
 *
 * @service categoryUser
 */
class CategoryUserService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		parent::applyPartnerFilterForClass(new categoryKuserPeer());
	}
	
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
		
		$category = categoryPeer::retrieveByPK($categoryUser->categoryId);
		if (!$category)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryUser->categoryId);						
		
		$currentKuserCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndActiveKuserId($categoryUser->categoryId, kCurrentContext::$ks_kuser_id);		
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
			
		if($override == KalturaUpdateMethodType::AUTOMATIC && $dbCategoryKuser->getUpdateMethod() == KalturaUpdateMethodType::MANUAL)
			throw new KalturaAPIException(KalturaErrors::CANNOT_OVERRIDE_MANUAL_CHANGES);
		
		$dbCategoryKuser = $categoryUser->toUpdatableObject($dbCategoryKuser);
		$dbCategoryKuser->setUpdateMethod($override);
		
		$category = categoryPeer::retrieveByPK($categoryUser->categoryId);
		if (!$category)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryUser->categoryId);

		if($dbCategoryKuser->getKuserId() == $category->getKuserId())
			throw new KalturaAPIException(KalturaErrors::CANNOT_UPDATE_CATEGORY_USER_OWNER);
		
		$currentKuserCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndActiveKuserId($categoryUser->categoryId, kCurrentContext::$ks_kuser_id);
		if(!$currentKuserCategoryKuser || $currentKuserCategoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MANAGER)
			throw new KalturaAPIException(KalturaErrors::CANNOT_UPDATE_CATEGORY_USER, $categoryUser->categoryId);
			
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
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);
			
		$dbCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndKuserId($categoryId, $kuser->getId());
		if (!$dbCategoryKuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_CATEGORY_USER_ID, $categoryId, $userId);
			
		$category = categoryPeer::retrieveByPK($categoryId);
		if (!$category)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryId);						

		if ($category->getInheritanceType() == InheritanceType::INHERIT)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_INHERIT_MEMBERS, $categoryId);		
		
		// only manager can remove memnger or users remove himself
		$currentKuserCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndKuserId($dbCategoryKuser->getCategoryId(), kCurrentContext::$ks_kuser_id);
		if((!$currentKuserCategoryKuser || 
			($currentKuserCategoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MANAGER &&
			 kCurrentContext::$ks_uid != $userId)) &&
			 kCurrentContext::$ks_partner_id != Partner::BATCH_PARTNER_ID &&
			 kEntitlementUtils::getEntitlementEnforcement())
			throw new KalturaAPIException(KalturaErrors::CANNOT_UPDATE_CATEGORY_USER);
		
		if($dbCategoryKuser->getKuserId() == $category->getKuserId() &&
			kCurrentContext::$ks_partner_id != Partner::BATCH_PARTNER_ID)
			throw new KalturaAPIException(KalturaErrors::CANNOT_UPDATE_CATEGORY_USER_OWNER);
			
		$dbCategoryKuser->delete();		
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
		
		$currentKuserCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndActiveKuserId($dbCategoryKuser->getCategoryId(), kCurrentContext::$ks_kuser_id);
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
		
		$currentKuserCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndActiveKuserId($dbCategoryKuser->getCategoryId(), kCurrentContext::$ks_kuser_id);
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
		if(!($filter->categoryIdEqual || $filter->categoryIdIn || $filter->userIdIn || $filter->userIdEqual))
			throw new KalturaAPIException(KalturaErrors::MUST_FILTER_USERS_OR_CATEGORY);			
		
		if (!$filter)
			$filter = new KalturaCategoryUserFilter();

		if (!$pager)
			$pager = new kalturaFilterPager();

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
				$usersIds[] = $kuser->getId();
				
			$filter->userIdIn = implode(',', $usersIds);
		}
		
		if($filter->userIdEqual)
		{
			$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
			$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $filter->userIdEqual);
			if (!$kuser)
				throw new kCoreException('Invalid user id', kCoreException::INVALID_USER_ID);
				
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
			
			if(is_null($category))
				continue;
				
			if($category->getInheritanceType() == InheritanceType::INHERIT)
			{
				//if category inheris members - chnage filter to -> inherited from parent id = category->getIheritedParent
				$categoriesInheritanceRoot[$category->getInheritedParentId()] = $category->getInheritedParentId();
			}
			else
			{
				$categoriesInheritanceRoot[$category->getId()] = $category->getId();
			}
		}
		
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
		$c->applyFilters();
		
		$totalCount = categoryKuserPeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$list = categoryKuserPeer::doSelect($c);
		
		$newList = KalturaCategoryUserArray::fromDbArray($list);
		
		$response = new KalturaCategoryUserListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		
		return $response;
	}
	
	/**
	 * Copy all memeber from parent category
	 * 
	 * @action copyFromCaregory
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
	 * @serverOnly
	 */
	public function indexAction($userId, $categoryId, $shouldUpdate = true)
	{
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
		$kuser = kuserPeer::getActiveKuserByPartnerAndUid($partnerId, $userId);

		if(!$kuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
			
		$dbCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndActiveKuserId($categoryId, $kuser->getId());
		if(!$dbCategoryKuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_CATEGORY_USER_ID);
			
		if (!$shouldUpdate)
		{
			$dbCategoryKuser->setUpdatedAt(time());
			$dbCategoryKuser->save();
			
			return $dbCategoryKuser->getId();
		}
				
		$dbCategoryKuser->reSetCategoryFullIds();
		$dbCategoryKuser->save();
		
		return $dbCategoryKuser->getId();
	}
}