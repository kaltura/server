<?php

/**
 * @package Scheduler
 * @subpackage Delete
 */

class KDeletingUserGroupSubscribtionEngine extends KDeletingEngine
{
	const PAGE_SIZE = 500;

	protected function delete(KalturaFilter $filter)
	{
		$userId = $filter->userIdEqual;
		$groupId = $filter->groupIdEqual;
		if (!$userId || !$groupId)
		{
			KalturaLog::err("Missing userId or groupId in filter");
			return 0;
		}
		$categoryIds = $this->getUserSubscribedCategories($groupId, $userId);
		return $this->deleteUserCategoriesSubscription($categoryIds,$userId);
	}

	protected function deleteUserCategoriesSubscription($categories,$userId)
	{
		$deletedCount = 0; // Initializing a counter for deleted objects
		foreach ($categories as $categoryId)
		{
			$filter = new KalturaCategoryUserFilter();
			$filter->categoryIdEqual = $categoryId;
			$filter->relatedGroupsByUserId = $userId;
			$filter->statusEqual = KalturaCategoryUserStatus::ACTIVE;

			// Check if the user is associated with any group that is part of the category
			try
			{
				$result = KBatchBase::$kClient->categoryUser->listAction($filter);
				if ($result->totalCount == 1) {
					// If the user is not part of the category by a group, delete the user from the category
					try {
						KBatchBase::$kClient->categoryUser->delete($categoryId, $userId);
						$deletedCount++; // Increment the counter after successful deletion
					} catch (Exception $e) {
						KalturaLog::err("Failed to delete user with id [$userId] from category [$categoryId]");
					}
				} else {
					KalturaLog::debug("User $userId is part of category $categoryId, skipping delete.");
				}
			}
			catch (Exception $e)
			{
				KalturaLog::err("Failed to retrieve list of categoryUsers for userId [$userId] and categoryId [$categoryId]");
			}
		}
		return $deletedCount;
	}

	protected function getUserSubscribedCategories($groupId, $userId)
	{
		$listOfUserSubscribedCategoryIds = array();
		$filter = new KalturaCategoryUserFilter();
		$filter->statusEqual = KalturaCategoryUserStatus::ACTIVE;
		$filter->userIdEqual = $groupId;

		$pager = new KalturaFilterPager();
		$pager->pageSize = self::PAGE_SIZE;
		$pager->pageIndex = 1;

		do {
			try
			{
				$groupCategoriesPage = KBatchBase::$kClient->categoryUser->listAction($filter, $pager);
				$groupCategories = $groupCategoriesPage->objects;
				$categoryIds = array_map(function ($categoryUser) {
					return $categoryUser->categoryId;
				}, $groupCategories);

				// Create a new filter for user subscriptions
				if (empty($categoryIds)) {
					KalturaLog::debug("No categories found for groupId [$groupId]");
					break;
				}
				$categoryUserFilter = new KalturaCategoryUserFilter();
				$categoryUserFilter->statusEqual = KalturaCategoryUserStatus::ACTIVE;
				$categoryUserFilter->userIdEqual = $userId;
				$categoryUserFilter->categoryIdIn = implode(',', $categoryIds);
				$categoryUserFilter->permissionNamesMatchAnd = "CATEGORY_SUBSCRIBE";
				$categoryUserFilter->permissionLevelEqual = KalturaCategoryUserPermissionLevel::NONE;

				try
				{

					$userSubscribedCategoriesPage = KBatchBase::$kClient->categoryUser->listAction($categoryUserFilter, new KalturaFilterPager());
					$userSubscribedCategoryIds = array_map(function ($categoryUser) {
						return $categoryUser->categoryId;
					}, $userSubscribedCategoriesPage->objects);

					// Merge the results with the final list
					$listOfUserSubscribedCategoryIds = array_merge($listOfUserSubscribedCategoryIds, $userSubscribedCategoryIds);
				}
				catch (Exception $e)
				{
					KalturaLog::err("Failed to retrieve list of categoryUsers for userId [$userId]");
					break;
				}

				$pager->pageIndex++;
			}
			catch (Exception $e)
			{
				KalturaLog::err("Failed to retrieve list of categoryUsers for groupId [$groupId]");
				break;
			}
		} while (count($groupCategoriesPage->objects) == $pager->pageSize);

		return $listOfUserSubscribedCategoryIds;
	}

}
