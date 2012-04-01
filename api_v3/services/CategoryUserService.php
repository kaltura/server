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
		
		$currentKuserCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndActiveKuserId($categoryUser->categoryId, kCurrentContext::$ks_uid);
		if($currentKuserCategoryKuser && $currentKuserCategoryKuser->getPermissionLevel() == CategoryKuserPermissionLevel::MANAGER)
		{
			//Current Kuser is manager
			$dbCategoryKuser->setStatus(CategoryKuserStatus::ACTIVE);
		}
		elseif ($category->getUserJoinPolicy() == UserJoinPolicyType::NOT_ALLOWED)
		{
			throw new KalturaAPIException(KalturaErrors::CATEGORY_USER_JOIN_NOT_ALLOWED, $categoryUser->categoryId);
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
		$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $userId);
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
		$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $userId);
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
		
		$currentKuserCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndActiveKuserId($categoryUser->categoryId, kCurrentContext::$ks_uid);
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
		$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $userId);
		if (!$kuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);
			
		$dbCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndKuserId($categoryId, $kuser->getId());
		if (!$dbCategoryKuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_CATEGORY_USER_ID, $categoryId, $kuser->getId());
			
		$category = categoryPeer::retrieveByPK($categoryUser->categoryId);
		if (!$category)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryUser->categoryId);						

		if ($category->getInheritanceType() == InheritanceType::INHERIT)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_INHERIT_MEMBERS, $categoryId);		
		
		// only manager can remove memnger or users remove himself
		$currentKuserCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndKuserId($dbCategoryKuser->getCategoryId(), kCurrentContext::$ks_uid);
		if(!$currentKuserCategoryKuser || 
			($currentKuserCategoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MANAGER &&
			 kCurrentContext::$ks_uid != $userId))
			throw new KalturaAPIException(KalturaErrors::CANNOT_UPDATE_CATEGORY_USER);
		
		if($dbCategoryKuser->getKuserId() == $category->getKuserId())
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
		$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $userId);
		if (!$kuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);
			
		$dbCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndKuserId($categoryId, $kuser->getId());
		if (!$dbCategoryKuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_CATEGORY_USER_ID, $categoryId, $userId);
		
		$currentKuserCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndActiveKuserId($dbCategoryKuser->getCategoryId(), kCurrentContext::$ks_uid);
		if(!$currentKuserCategoryKuser || $currentKuserCategoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MANAGER)
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
		$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $userId);
		if (!$kuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);
			
		$dbCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndKuserId($categoryId, $kuser->getId());
		if (!$dbCategoryKuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_CATEGORY_USER_ID, $categoryId, $userId);
		
		$currentKuserCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndActiveKuserId($dbCategoryKuser->getCategoryId(), kCurrentContext::$ks_uid);
		if(!$currentKuserCategoryKuser || $currentKuserCategoryKuser->getPermissionLevel() != CategoryKuserPermissionLevel::MANAGER)
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
	 * @return KalturaCategoryUserListResponse
	 */
	function listAction(KalturaCategoryUserFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if(!($filter->categoryIdEqual || $filter->categoryIdIn || $filter->userIdIn || $filter->userIdEqual))
			throw new APIException(KalturaErrors::MUST_FILTER_USERS_OR_CATEGORY);			
		
		if (!$filter)
			$filter = new KalturaCategoryUserFilter();

		if (!$pager)
			$pager = new kalturaFilterPager();

		if($filter->userIdIn)
		{
			$usersIds = explode(',', $filter->userIdIn);

			$c = new Criteria();
			$c->add(kuserPeer::PARTNER_ID, kCurrentContext::$ks_partner_id, Criteria::EQUAL);
			$c->add(kuserPeer::PUSER_ID, $usersIds, Criteria::IN);
			$kusers = kuserPeer::doSelect($c);
			
			$usersIds = array();
			foreach($kusers as $kuser)
				$usersIds[] = $kuser->getId();
				
			$filter->userIdIn = implode(',', $usersIds);
		}
		
		if($filter->userIdEqual)
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $filter->userIdEqual);
			if (!$kuser)
				throw new kCoreException('Invalid user id', kCoreException::INVALID_USER_ID);
				
			$filter->userIdEqual = $kuser->getId();
		}	
		
		//TODO 
		//get category 
		//if category inheris members - chnage filter to -> inherited from parent id = category->getIheritedParent
		
		$categories = array();
		if ($filter->categoryIdEqual)
		{
			$categories[] = categoryPeer::retrieveByPK($filter->categoryIdEqual);
		}
		elseif($filter->categoryIdIn)
		{
			$categories = categoryPeer::retrieveByPKs($filter->categoryIdIn);
		}
		
		$categoriesInheritanceRoot = array();
		foreach ($categories as $category)
		{
			if($category->getInheritanceType() == InheritanceType::INHERIT)
			{
				$categoriesInheritanceRoot[$category->getInheritedParentId()] = $category->getInheritedParentId();
			}
			else
			{
				$categoriesInheritanceRoot[$category->getId()] = $category->getId();
			}
		}
			
		$categoryKuserFilter = new categoryKuserFilter();
		$filter->toObject($categoryKuserFilter);
		
		$c = new Criteria();
		$categoryKuserFilter->attachToCriteria($c);
		
		$totalCount = categoryKuserPeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$list = categoryKuserPeer::doSelect($c);
		
		$newList = KalturaCategoryUserArray::fromDbArray($list);
		
		$response = new KalturaCategoryUserListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		
		return $response;
	}
}