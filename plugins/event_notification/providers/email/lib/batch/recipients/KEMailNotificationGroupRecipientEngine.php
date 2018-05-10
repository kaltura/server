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
	
	private function getUsersByUserIds($userIds)
	{
		$userFilter = new KalturaUserFilter();
		$userFilter->idIn = $userIds;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		
		$users = KBatchBase::$kClient->user->listAction($userFilter, $pager);
		
		if(!($users->totalCount > 0))
			return null;
		
		return $users->objects;
	}

	private function getGroupUserIds($groupId)
	{
		//list users in group
		$groupFilter = new KalturaGroupUserFilter();
		$groupFilter->groupIdEqual = $groupId;
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		
		$groupUserList = KBatchBase::$kClient->groupUser->listAction($groupFilter, $pager);
		
		if(!($groupUserList->totalCount > 0))
			return null;
		
		$groupUserIds = "";
		foreach ($groupUserList->objects as $user)
		{
			$groupUserIds .= $user->userId;
			if(next($groupUserList) == true)
				$groupUserIds .= ",";
		}
		
		return $groupUserIds;
	}
}