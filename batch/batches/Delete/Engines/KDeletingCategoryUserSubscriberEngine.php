<?php

/**
 * @package Scheduler
 * @subpackage Delete
 */
class KDeletingCategoryUserSubscriberEngine extends KDeletingEngine
{
	const CATEGORY_SUBSCRIBE = 'CATEGORY_SUBSCRIBE';
	const PAGE_SIZE = 500;
	const LIST_SIZE = 150;
	
	/* (non-PHPdoc)
	 * @see KDeletingEngine::delete()
	 */
	protected function delete(KalturaFilter $filter)
	{
		$categoryId = $filter->categoryIdEqual;
		$groupId = $filter->userIdEqual;
		if (!$categoryId || !$groupId)
		{
			KalturaLog::err("Missing categoryId or groupId in filter");
			return 0;
		}
		return $this->deleteCategoryUserSubscribers($categoryId, $groupId);
	}
	
	protected function deleteCategoryUserSubscribers($categoryId, $groupId)
	{
		list($groupsInCategory, $subscribersInCategory) = $this->createListsOfGroupsAndSubscribers($categoryId, $groupId);
		$this->deleteSubscribersWithoutGroup($subscribersInCategory, $groupsInCategory, $categoryId);
		
		return 0;
	}
	
	protected function createListsOfGroupsAndSubscribers($categoryId, $groupId)
	{
		$categoryUserFilter = new KalturaCategoryUserFilter();
		$categoryUserFilter->categoryIdEqual = $categoryId;
		
		$pager = new KalturaFilterPager();
		$this->pager->pageIndex = 0;
		$this->pager->pageSize = self::PAGE_SIZE;
		
		$subscribersInCategory = array();
		$groupsInCategory = array();
		$countResults = 0;
		do
		{
			try
			{
				$categoryUsersPage = KBatchBase::$kClient->categoryUser->listAction($categoryUserFilter, $pager);
			}
			catch (Exception $e)
			{
				KalturaLog::err("Failed to retrieve list of categoryUsers for categoryId [$categoryId]");
				return 0;
			}
			
			if ($categoryUsersPage && $categoryUsersPage->objects)
			{
				$this->addToListsGroupsAndSubscribers($groupsInCategory, $subscribersInCategory, $categoryUsersPage->objects, $groupId);
				$countResults += count($categoryUsersPage->objects);
			}
			else
			{
				break;
			}
			
			$this->pager->pageIndex += 1;
		} while (count($categoryUsersPage->objects) >= $this->pager->pageSize);
		
		$countGroups = count($groupsInCategory);
		$countSubscribers = count($subscribersInCategory);
		KalturaLog::debug("Found [$countGroups] groups out of [$countResults] categoryUsers for category [$categoryId]");
		KalturaLog::debug("Found [$countSubscribers] subscribers out of [$countResults] categoryUsers for category [$categoryId]");
		
		return array($groupsInCategory, $subscribersInCategory);
	}
	
	protected function addToListsGroupsAndSubscribers(&$groupsInCategory, &$subscribersInCategory, $categoryUsersPage, $groupId)
	{
		$countForList = 0;
		$listOfUserIds = '';
		$userIdsSubscribersMap = array();
		
		foreach ($categoryUsersPage as $categoryUser)
		{
			/** @var $categoryUser KalturaCategoryUser */
			$userId = $categoryUser->userId;
			if ($userId == $groupId)
			{
				continue;
			}
			
			if ($categoryUser->permissionNames == self::CATEGORY_SUBSCRIBE)
			{
				$userIdsSubscribersMap[$userId] = true;
			}
			else
			{
				$userIdsSubscribersMap[$userId] = false;
			}
			
			$countForList += 1;
			$listOfUserIds .= $userId . ',';
			
			if ($countForList != self::LIST_SIZE)
			{
				continue;
			}
			
			$this->retrieveGroupsAndSubscribersFromUsersIds($groupsInCategory, $subscribersInCategory, $listOfUserIds, $userIdsSubscribersMap);
			
			$countForList = 0;
			$listOfUserIds = '';
			$userIdsSubscribersMap = array();
		}
		
		if ($countForList)
		{
			$this->retrieveGroupsAndSubscribersFromUsersIds($groupsInCategory, $subscribersInCategory, $listOfUserIds, $userIdsSubscribersMap);
		}
	}
	
	protected function retrieveGroupsAndSubscribersFromUsersIds(&$groupsInCategory, &$subscribersInCategory, $listOfIds, $userIdsSubscribersMap)
	{
		try
		{
			$filter = new KalturaUserFilter();
			$filter->idIn = $listOfIds;
			$pager = new KalturaFilterPager();
			$pager->pageSize = self::LIST_SIZE;
			$kusersList = KBatchBase::$kClient->user->listAction($filter, $pager);
		}
		catch (Exception $e)
		{
			KalturaLog::err('Failed to retrieve list of users');
			return;
		}
		
		if (!$kusersList || !$kusersList->objects)
		{
			return;
		}
		
		foreach ($kusersList->objects as $kuser)
		{
			/** @var $kuser KalturaUser */
			if ($kuser->type == KalturaUserType::GROUP)
			{
				$groupsInCategory[] = $kuser->id;
			}
			if ($userIdsSubscribersMap[$kuser->id])
			{
				$subscribersInCategory[] = $kuser->id;
			}
		}
	}
	
	protected function deleteSubscribersWithoutGroup($subscribersInCategory, $groupsInCategory, $categoryId)
	{
		$groupUserFilter = new KalturaGroupUserFilter();
		
		foreach ($subscribersInCategory as $subscriberId)
		{
			$deleteSubscriber = true;
			
			$groupUserFilter->userIdEqual = $subscriberId;
			$groupUserFilter->groupIdIn = '';
			$countForList = 0;
			
			foreach ($groupsInCategory as $groupId)
			{
				$groupUserFilter->groupIdIn .= $groupId . ',';
				$countForList += 1;
				
				if ($countForList != self::LIST_SIZE)
				{
					continue;
				}
				
				try
				{
					$groupUsers = $this->retrieveGroupUsersFromFilter($groupUserFilter);
				}
				catch (Exception $e)
				{
					KalturaLog::err("Failed to retrieve list of groupUsers for groupId [$groupId] and userId [$subscriberId]");
				}
				
				if ($groupUsers && $groupUsers->objects && count($groupUsers->objects) > 0)
				{
					$deleteSubscriber = false;
					$countForList = 0;
					break;
				}
				
				$groupUserFilter->userIdEqual = $subscriberId;
				$groupUserFilter->groupIdIn = '';
				$countForList = 0;
			}
			
			if ($countForList)
			{
				try
				{
					$groupUsers = $this->retrieveGroupUsersFromFilter($groupUserFilter);
				}
				catch (Exception $e)
				{
					KalturaLog::err("Failed to retrieve list of groupUsers for groupId [$groupId] and userId [$subscriberId]");
				}
				
				if ($groupUsers && $groupUsers->objects && count($groupUsers->objects) > 0)
				{
					$deleteSubscriber = false;
				}
			}
			
			if ($deleteSubscriber)
			{
				try
				{
					KBatchBase::$kClient->categoryUser->delete($categoryId, $subscriberId);
				}
				catch (Exception $e)
				{
					KalturaLog::err("Failed to delete categoryUser with categoryId [$categoryId] and userId [$subscriberId]");
				}
			}
		}
	}
	
	protected function retrieveGroupUsersFromFilter($groupUserFilter)
	{
		$pager = new KalturaFilterPager();
		$pager->pageSize = self::LIST_SIZE;
		
		$groupUsers = KBatchBase::$kClient->groupUser->listAction($groupUserFilter, $pager);
		
		return $groupUsers;
	}
}
