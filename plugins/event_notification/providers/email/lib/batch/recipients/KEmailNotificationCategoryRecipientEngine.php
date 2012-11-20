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
		//List categoryKusers
		//TODO add paging logic
		$categoryUserFilter = new KalturaCategoryUserFilter();
		$categoryUserFilter->categoryIdEqual = $this->recipientProvider->categoryId;
		$categoryUserFilter->permissionNamesMatchOr = 'CATEGORY_PERMISSION_SUBSCRIBE';
		$categoryUserList = $this->client->categoryUser->listAction($categoryUserFilter, new KalturaFilterPager());
		
		$categoryUserIds = array();
		foreach ($categoryUserList->objects as $categoryUser)
			$categoryUserIds[] = $categoryUser->id;
		$userFilter = new KalturaUserFilter();
		$userFilter->idIn = implode(',', $categoryUserIds);
		$userList = $this->client->user->listAction($userFilter, new KalturaFilterPager);
		
		$recipients = array();
		foreach ($userList->objects as $user)
		{
			$recipients[$user->email] = $user->firstName. ' ' . $user->lastName;
		}
		
		return $recipients;
	}
}