<?php
/**
 * Engine which retrieves the email notification recipients for a category-related event.
 * 
 * @package plugins.emailNotification
 * @subpackage Scheduler
 */
class KEmailNotificationCategoryRecipientEngine extends KEmailNotificationRecipientEngine
{
	/* (non-PHPdoc)
	 * @see KEmailNotificationRecipientEngine::getRecipients()
	 */ 
	function getRecipients(array $contentParameters)
	{
		$recipients = array();
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		$pager->pageIndex = 1;
		$categoryUserIds = array();
		$maxPagesToScan = 2;
		do
		{
			$categoryUserList = KBatchBase::$kClient->categoryUser->listAction($this->recipientJobData->categoryUserFilter, $pager);
			foreach ($categoryUserList->objects as $categoryUser)
				$categoryUserIds[] = $categoryUser->userId;

			$pager->pageIndex ++;
		} while (($pager->pageSize == count($categoryUserList->objects)) and ($pager->pageIndex <= $maxPagesToScan));

		if (count($categoryUserIds)==0)
	            return $recipients;

		$pager->pageIndex = 1;
		$userFilter = new KalturaUserFilter();
		$userFilter->idIn = implode(',', $categoryUserIds);
		do
		{
			$userList = KBatchBase::$kClient->user->listAction($userFilter, $pager);
			foreach ($userList->objects as $user)
				$recipients[$user->email] = $user->firstName. ' ' . $user->lastName;

			$pager->pageIndex ++;
		} while ($pager->pageSize == count($userList->objects));
		
        return $recipients;
	}
}
