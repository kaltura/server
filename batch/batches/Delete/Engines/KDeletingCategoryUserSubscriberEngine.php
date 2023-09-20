<?php

/**
 * @package Scheduler
 * @subpackage Delete
 */
class KDeletingCategoryUserSubscriberEngine extends KDeletingEngine
{
	const CATEGORY_SUBSCRIBE = 'CATEGORY_SUBSCRIBE';
	
	/* (non-PHPdoc)
	 * @see KDeletingEngine::delete()
	 */
	protected function delete(KalturaFilter $filter)
	{
		$categoryId = $filter->categoryIdEqual;
		$groupId = $filter->userIdEqual;
		return $this->deleteCategoryUserSubscriber($categoryId, $groupId);
	}
	
	protected function deleteCategoryUserSubscriber($categoryId, $groupId)
	{
		$categoryUserFilter = new KalturaCategoryUserFilter();
		$categoryUserFilter->categoryIdEqual = $categoryId;
		
		$pager = new KalturaFilterPager();
		$this->pager->pageIndex = 0;
		$this->pager->pageSize = 100;
		
		$categoryUsersList = array();
		do
		{
			$categoryUsersPage = KBatchBase::$kClient->categoryUser->listAction($categoryUserFilter, $pager);
			$this->pager->pageIndex += 1;
			$categoryUsersList = array_merge($categoryUsersList, $categoryUsersPage->objects);
		} while (count($categoryUsersPage->objects) >= $this->pager->pageSize);
		
		$countResults = count($categoryUsersList);
		KalturaLog::debug("Retrieved [$countResults] categoryUsers for category [$categoryId]");
		
		$subscribersList = array();
		$groupsInCategory = array();
		foreach ($categoryUsersList as $categoryUser)
		{
			/** @var $categoryUser KalturaCategoryUser */
			if ($categoryUser->userId == $groupId)
			{
				continue;
			}
			
			$kuser = KBatchBase::$kClient->user->get($categoryUser->userId);
			/** @var $kuser KalturaUser */
			if ($kuser->type == KalturaUserType::GROUP)
			{
				$groupsInCategory[] = $kuser->id;
			}
			
			if ($categoryUser->permissionNames == self::CATEGORY_SUBSCRIBE)
			{
				$subscribersList[] = $categoryUser->userId;
			}
		}
		
		$countGroups = count($groupsInCategory);
		$countSubscribers = count($subscribersList);
		KalturaLog::debug("Found [$countGroups] groups out of [$countResults] categoryUsers for category [$categoryId]");
		KalturaLog::debug("Found [$countSubscribers] subscribers out of [$countResults] categoryUsers for category [$categoryId]");
		
		foreach ($subscribersList as $subscriberId)
		{
			$deleteSubscriber = true;
			
			foreach ($groupsInCategory as $groupId)
			{
				$groupUserFilter = new KalturaGroupUserFilter();
				$groupUserFilter->groupIdEqual = $groupId;
				$groupUserFilter->userIdEqual = $subscriberId;
				$groupUser = KBatchBase::$kClient->groupUser->listAction($groupUserFilter);
				if ($groupUser->objects && count($groupUser->objects) > 0)
				{
					$deleteSubscriber = false;
					break;
				}
			}
			
			if ($deleteSubscriber)
			{
				KBatchBase::$kClient->categoryUser->delete($categoryId, $subscriberId);
			}
		}
		
		return 0;
	}
}
