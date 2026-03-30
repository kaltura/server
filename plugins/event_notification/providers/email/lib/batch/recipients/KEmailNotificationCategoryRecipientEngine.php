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

		if(isset(KBatchBase::$taskConfig->params->maxPagesToScan))
		{
			$maxPagesToScan = KBatchBase::$taskConfig->params->maxPagesToScan;
		}
		$maxRecipients = $pager->pageSize * $maxPagesToScan;

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

			foreach ($userList->objects as $user)
			{
				if($user->type == KalturaUserType::USER)
				{
					if (count($recipients) < $maxRecipients)
					{
						$recipients[$user->email] = $user->firstName. ' ' . $user->lastName;
					}
					else
					{
						return $recipients;
					}
				}

				else if($user->type == KalturaUserType::GROUP || $user->type == KalturaUserType::APPLICATIVE_GROUP)
				{
					$groupUsers = $this->getUsersOfGroupByGroupId($user->id);

					foreach($groupUsers as $groupUser)
					{
						if (count($recipients) < $maxRecipients)
						{
							$recipients[$groupUser->email] = $groupUser->firstName . ' ' . $groupUser->lastName;
						}
						else
						{
							return $recipients;
						}
					}
				}
			}
			$pager->pageIndex ++;
		}
		while ( $pager->pageIndex <= $maxPagesToScan );
		return $recipients;
	}
}
