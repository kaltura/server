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
		//List categoryKusers
		$categoryUserList = $this->client->categoryUser->listAction($this->recipientJobData->categoryUserFilter, new KalturaFilterPager());
		if (!count($categoryUserList->objects))
			return $recipients;
		
		$categoryUserIds = array();
		foreach ($categoryUserList->objects as $categoryUser)
			$categoryUserIds[] = $categoryUser->userId;
		
		$userFilter = new KalturaUserFilter();
		$userFilter->idIn = implode(',', $categoryUserIds);
		$userList = $this->client->user->listAction($userFilter, new KalturaFilterPager());
		foreach ($userList->objects as $user)
		{
			/* @var $user KalturaUser */
			$recipients[$user->email] = $user->firstName. ' ' . $user->lastName;
		}
		
		return $recipients;
	}
}