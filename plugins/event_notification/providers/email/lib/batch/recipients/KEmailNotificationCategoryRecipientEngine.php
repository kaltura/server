<?php
/**
 * Engine which retrieves the email notification recipients for a category-related event.
 * 
 * @package plugins.emailNotification
 * @subpackage Scheduler
 */
class KEmailNotificationCategoryRecipientEngine extends KEmailNotificationRecipientEngine
{
	/** (non-PHPdoc)
	 * @see KEmailNotificationRecipientEngine::getRecipients()
	 **/
	function getRecipients(array $contentParameters)
	{
		$recipients = array();
		$pager = new KalturaFilterPager();
		$pager->pageSize = 150;
		$pager->pageIndex = 1;
		$userPager = new KalturaFilterPager();
		$userPager->pageSize = $pager->pageSize;
		$maxPagesToScan = 7;
		if (isset(KBatchBase::$taskConfig->params->maxPagesToScan))
		{
			$maxPagesToScan = KBatchBase::$taskConfig->params->maxPagesToScan;
		}

		do
		{
			$categoryUserList = KBatchBase::$kClient->categoryUser->listAction($this->recipientJobData->categoryUserFilter, $pager);

			if(count($categoryUserList->objects) == 0)
			{
				break;
			}

			$categoryUserIds = array();
			foreach ($categoryUserList->objects as $categoryUser)
			{
				$categoryUserIds[] = $categoryUser->userId;
			}

			$userFilter = new KalturaUserFilter();
			$userFilter->idIn = implode(',', $categoryUserIds);
			$userList = KBatchBase::$kClient->user->listAction($userFilter, $userPager);
			/* @var $user KalturaUser */
			foreach ($userList->objects as $user)
			{
				if($user->type == KalturaUserType::GROUP)
				{
					$groupPager = new KalturaFilterPager();
					$groupPager->pageSize = 500;
					$groupPager->pageIndex = 1;
					$groupUserFilter = new KalturaGroupUserFilter();
					$groupUserFilter->groupIdEqual = $user->id;
					$groupUserList = KBatchBase::$kClient->groupUser->listAction($groupUserFilter, $groupPager);
					if($groupUserList->totalCount > 0)
					{
						$groupUsersIds = array();
						/* @var $groupUser KalturaGroupUser */
						foreach ($groupUserList->objects as $groupUser)
						{
							$groupUsersIds[] = $groupUser->userId;
						}
						$userFilter = new KalturaUserFilter();
						$userFilter->idIn = implode(',', $groupUsersIds);
						$userListFromGroup = KBatchBase::$kClient->user->listAction($userFilter, $groupPager);
						/* @var $userFromGroup KalturaUser */
						foreach ($userListFromGroup->objects as $userFromGroup)
						{
							$recipients[$userFromGroup->email] = $userFromGroup->firstName. ' ' . $userFromGroup->lastName;
						}
					}
					continue;
				}
				$recipients[$user->email] = $user->firstName. ' ' . $user->lastName;
			}
			$pager->pageIndex ++;
		}
		while ( $pager->pageIndex <= $maxPagesToScan );

		return $recipients;
	}
}
