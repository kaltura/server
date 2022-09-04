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
		
		$recipients = array();
		
		$groupUserIds = $this->getGroupUserIds($groupId);
		if(!$groupUserIds)
			return $recipients;
		
		$groupUsers = $this->getUsersByUserIds($groupUserIds);
		if(!$groupUsers)
			return $recipients;
		
		foreach($groupUsers as $groupUser)
		{
			$recipients[$groupUser->email] = $groupUser->firstName. ' ' . $groupUser->lastName;
		}
		
		return $recipients;
	}

}
