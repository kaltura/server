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
	function getRecipients(array $contentParameters)
	{
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;

		//Replace any content parameter tokens in the filter in the interest of supporting a dynamic filter based on the event context
		if(count($contentParameters))
		{
			foreach ((array)$this->recipientJobData->filter as $property => $value)
			{
				if ($this->recipientJobData->filter->$property)
				{
					$this->recipientJobData->filter->$property = str_replace(array_keys($contentParameters), $contentParameters, $value);
				}
			}
		}
		
		//list users
		$userList = KBatchBase::$kClient->user->listAction($this->recipientJobData->filter, $pager);
		
		$recipients = array();
		foreach ($userList->objects as $user)
		{
			/* @var $user KalturaUser */
			$recipients[$user->email] = $user->firstName. ' ' . $user->lastName;
		}
		
		return $recipients;
	}

	
}
