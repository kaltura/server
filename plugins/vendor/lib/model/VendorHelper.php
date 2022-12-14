<?php
/**
 * @package plugins.vendor
 * @subpackage model
 */
class VendorHelper
{
	const MAX_PUSER_LENGTH = 100;
	const ERROR_PARENT_CATEGORY_NOT_FOUND = 'Could not find parent category id';
	
	/**
	 * Redirects to new URL
	 * @param $url
	 */
	public static function redirect($url)
	{
		$redirect  = new kRendererRedirect($url);
		$redirect->output();
		KExternalErrors::dieGracefully();
	}
	
	public static function objectChanged()
	{
	
	}
	
	public static function getDropFolderRelatedInfo()
	{
	
	}

	public static function setDefaultValuesIntegration()
	{
	
	}
	
	public static function verifyAndSetDropFolderConfig()
	{
	
	}
	
	public static function setDeletePolicy()
	{
	
	}
	
	public static function getDropFolderStatus($status)
	{
		switch ($status)
		{
			case VendorIntegrationStatus::DISABLED:
			{
				return DropFolderStatus::DISABLED;
			}
			case VendorIntegrationStatus::ACTIVE:
			{
				return DropFolderStatus::ENABLED;
			}
			case VendorIntegrationStatus::DELETED:
			{
				return DropFolderStatus::DELETED;
			}
			default:
			{
				return DropFolderStatus::ERROR;
			}
		}
	}
	
	/**
	 * @param $partnerId
	 * @param $accountId
	 * @param $ks
	 * @throws Exception
	 */
	public static function loadSubmitPage($partnerId, $accountId, $ks, $filePath)
	{
		if (!file_exists($filePath))
		{
			KalturaLog::err("Submit page not found at $filePath");
			throw new KalturaAPIException(KalturaVendorIntegrationErrors::SUBMIT_PAGE_NOT_FOUND);
		}
		
		$page = file_get_contents($filePath);
		$page = str_replace('@ks@', $ks->getOriginalString(), $page);
		$page = str_replace('@BaseServiceUrl@', requestUtils::getHost(), $page);
		$page = str_replace('@partnerId@', $partnerId, $page);
		$page = str_replace('@accountId@', $accountId, $page);
		
		echo $page;
		die();
	}
	
	//
	//
	// Create category for vendor integration (From service)
	
	/**
	 * @param int $partnerId
	 * @param string $categoryFullName
	 * @param bool $createIfNotExist
	 * @return int id;
	 * @throws PropelException
	 * @throws Exception
	 */
	public static function createCategoryForVendorIntegration($partnerId, $categoryFullName, $vendorIntegration = null)
	{
		$category = categoryPeer::getByFullNameExactMatch($categoryFullName, null, $partnerId);
		if ($category)
		{
			KalturaLog::debug('Category: ' . $categoryFullName . ' already exist for partner: ' . $partnerId);
			return $category->getId();
		}
		
		$categoryDb = new category();
		
		//Check if this is a root category or child , if child get its parent ID
		$categoryNameArray = explode(categoryPeer::CATEGORY_SEPARATOR, $categoryFullName);
		$categoryName = end($categoryNameArray);
		if (count($categoryNameArray) > 1)
		{
			$parentCategoryFullNameArray = array_slice($categoryNameArray,0,-1);
			$parentCategoryFullName = implode(categoryPeer::CATEGORY_SEPARATOR, $parentCategoryFullNameArray);
			$parentCategory = categoryPeer::getByFullNameExactMatch($parentCategoryFullName, null, $partnerId);
			if (!$parentCategory)
			{
				self::exitWithError(self::ERROR_PARENT_CATEGORY_NOT_FOUND . $parentCategoryFullName, $vendorIntegration);
			}
			
			$parentCategoryId = $parentCategory->getId();
			$categoryDb->setParentId($parentCategoryId);
		}
		
		$categoryDb->setName($categoryName);
		$categoryDb->setFullName($categoryFullName);
		$categoryDb->setPartnerId($partnerId);
		$categoryDb->save();
		return $categoryDb->getId();
	}
	
	//
	//
	// Create categoryEntry for vendor integration (From batch)
	
	public static function addEntryToCategory($categoryName, $entryId, $partnerId)
	{
		KBatchBase::impersonate($partnerId);
		$categoryId = self::findCategoryIdByName($categoryName);
		if ($categoryId)
		{
			self::addCategoryEntry($categoryId, $entryId);
		}
		KBatchBase::unimpersonate();
	}
	
	public static function findCategoryIdByName($categoryName)
	{
		$isFullPath = self::isFullPath($categoryName);
		
		$categoryFilter = new KalturaCategoryFilter();
		if ($isFullPath)
		{
			$categoryFilter->fullNameEqual = $categoryName;
		}
		else
		{
			$categoryFilter->nameOrReferenceIdStartsWith = $categoryName;
		}
		
		$categoryResponse = KBatchBase::$kClient->category->listAction($categoryFilter, new KalturaFilterPager());
		$categoryId = null;
		if ($isFullPath)
		{
			if ($categoryResponse->objects && count($categoryResponse->objects) == 1)
			{
				$categoryId = $categoryResponse->objects[0]->id;
			}
		}
		else
		{
			$categoryIds = array();
			foreach ($categoryResponse->objects as $category)
			{
				if ($category->name === $categoryName)
				{
					$categoryIds[] = $category->id;
				}
			}
			$categoryId = (count($categoryIds) == 1) ? $categoryIds[0] : null;
		}
		return $categoryId;
	}
	
	public static function isFullPath($categoryName)
	{
		$numCategories = count(explode('>', $categoryName));
		return ($numCategories > 1);
	}
	
	public static function addCategoryEntry($categoryId, $entryId)
	{
		$categoryEntry = new KalturaCategoryEntry();
		$categoryEntry->categoryId = $categoryId;
		$categoryEntry->entryId = $entryId;
		KBatchBase::$kClient->categoryEntry->add($categoryEntry);
	}
	
	public static function exitWithError($errMsg, $vendorIntegration)
	{
		KalturaLog::err($errMsg);
		if ($vendorIntegration)
		{
			$vendorIntegration->saveLastError($errMsg);
		}
		
		KExternalErrors::dieGracefully();
	}
	
	//
	//
	// Opt-in / Opt-out groups (From batch)
	
	public static function isUserNotMemberOfGroups($userId, $partnerId, $participationGroupList)
	{
		$userFilter = new KalturaGroupUserFilter();
		$userFilter->userIdEqual = $userId;
		
		KBatchBase::impersonate($partnerId);
		$userGroupsResponse = KBatchBase::$kClient->groupUser->listAction($userFilter);
		KBatchBase::unimpersonate();
		
		$userGroupsArray = $userGroupsResponse->objects;
		
		$userGroupNamesArray = array();
		foreach ($userGroupsArray as $group)
		{
			array_push($userGroupNamesArray, $group->groupId);
		}
		
		KalturaLog::debug('User with id ['.$userId.'] is a member of the following groups ['.print_r($userGroupNamesArray, true).']');
		
		$intersection = array_intersect($userGroupNamesArray, $participationGroupList);
		return empty($intersection);
	}
	
	public static function getValidatedUsers($users, $partnerId, $createIfNotFound, $userToExclude)
	{
		if (!$users)
		{
			return $users;
		}
		
		$validatedUsers = array();
		foreach ($users as $user)
		{
			/* @var $user kVendorUser */
			/* @var $kUser KalturaUser */
			$kUser = self::getKalturaUser($partnerId, $user);
			if ($kUser)
			{
				if (strtolower($kUser->id) !== $userToExclude)
				{
					$validatedUsers[] = $kUser->id;
				}
			}
			elseif ($createIfNotFound)
			{
				self::createNewVendorUser($partnerId, $user->getProcessedName());
				$validatedUsers[] = $user->getProcessedName();
			}
		}
		return $validatedUsers;
	}
	
	public static function getKalturaUser($partnerId, kVendorUser $vendorUser)
	{
		$pager = new KalturaFilterPager();
		$pager->pageSize = 1;
		$pager->pageIndex = 1;
		
		$filter = new KalturaUserFilter();
		$filter->partnerIdEqual = $partnerId;
		$filter->idEqual = $vendorUser->getProcessedName();
		$kalturaUser = KBatchBase::$kClient->user->listAction($filter, $pager);
		if (!$kalturaUser->objects)
		{
			$email = $vendorUser->getOriginalName();
			$filterUser = new KalturaUserFilter();
			$filterUser->partnerIdEqual = $partnerId;
			$filterUser->emailStartsWith = $email;
			$kalturaUser = KBatchBase::$kClient->user->listAction($filterUser, $pager);
			if (!$kalturaUser->objects || strcasecmp($kalturaUser->objects[0]->email, $email) != 0)
			{
				return null;
			}
		}
		
		if($kalturaUser->objects)
		{
			return $kalturaUser->objects[0];
		}
		return null;
	}
	
	public static function createNewVendorUser($partnerId, $puserId)
	{
		if (!is_null($puserId))
		{
			$puserId = substr($puserId, 0, self::MAX_PUSER_LENGTH);
		}
		
		$user = new KalturaUser();
		$user->id = $puserId;
		$user->screenName = $puserId;
		$user->firstName = $puserId;
		$user->isAdmin = false;
		$user->type = KalturaUserType::USER;
		$kalturaUser = KBatchBase::$kClient->user->add($user);
		return $kalturaUser;
	}
}
