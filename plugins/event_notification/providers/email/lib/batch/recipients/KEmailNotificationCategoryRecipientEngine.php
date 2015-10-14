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
		//List categoryKusers
		$categoryUserList = KBatchBase::$kClient->categoryUser->listAction($this->recipientJobData->categoryUserFilter, $pager);
		if (!count($categoryUserList->objects))
			return $recipients;
		
		$categoryUserIds = array();
		foreach ($categoryUserList->objects as $categoryUser)
			$categoryUserIds[] = $categoryUser->userId;
		
		$userFilter = new KalturaUserFilter();
		$userFilter->idIn = implode(',', $categoryUserIds);
		$userList = KBatchBase::$kClient->user->listAction($userFilter, $pager);
		foreach ($userList->objects as $user)
		{
			/* @var $user KalturaUser */
			$recipients[$user->email] = $user->firstName. ' ' . $user->lastName;
		}
		
		return $recipients;
	}
}