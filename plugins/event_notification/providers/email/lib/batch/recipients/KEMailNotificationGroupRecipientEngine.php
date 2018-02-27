<?php
/**
 * Engine which retrieves a list of user assigned to specific group 
 * 
 * @package plugins.emailNotification
 * @subpackage Scheduler
 */
class KEMailNotificationGroupRecipientEngine extends  KEmailNotificationRecipientEngine
{
	/* (non-PHPdoc)
	 * @see KEmailNotificationRecipientEngine::getRecipients()
	 */
	function getRecipients(array $contentParameters) 
	{
		if(is_array($contentParameters) && count($contentParameters))
		{
			$groupId = str_replace(array_keys($contentParameters), $contentParameters, $this->recipientJobData->groupId);
		}
		
		//list users in group
		$groupFilter = new KalturaGroupUserFilter();
		$groupFilter->groupIdEqual = $groupId;
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		
		$recipients = array();
		$groupUserList = KBatchBase::$kClient->groupUser->listAction($groupFilter, $pager);
		foreach ($groupUserList->objects as $user)
		{
			$user = KBatchBase::$kClient->user->get($user->userId);
			if($user)
				$recipients[$user->email] = $user->firstName. ' ' . $user->lastName;
		}
		
		return $recipients;
	}

	
}