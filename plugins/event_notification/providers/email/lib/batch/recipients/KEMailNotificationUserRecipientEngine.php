<?php
/**
 * Engine which retrieves a dynamic list of user recipients based on provided filter
 * 
 * @package plugins.emailNotification
 * @subpackage Scheduler
 */
class KEmailNotificationUserRecipientEngine extends  KEmailNotificationRecipientEngine
{
	/* (non-PHPdoc)
	 * @see KEmailNotificationRecipientEngine::getRecipients()
	 */
	function getRecipients(array $contentParameters) {
		//list users
		$userList = $this->client->user->listAction($this->recipientJobData->filter, new KalturaFilterPager());
		
		$recipients = array();
		foreach ($userList->objects as $user)
		{
			/* @var $user KalturaUser */
			$recipients[$user->email] = $user->firstName. ' ' . $user->lastName;
		}
		
		return $recipients;
	}

	
}