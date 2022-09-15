<?php
/**
 * Abstract engine which retrieves a list of the email notification recipients.
 * 
 * @package plugins.emailNotification
 * @subpackage Scheduler
 */
abstract class KEmailNotificationRecipientEngine
{
	/**
	 * Job data for the email notification recipients
	 * @var KalturaEmailNotificationRecipientJobData
	 */
	protected $recipientJobData;
	
	public function __construct(KalturaEmailNotificationRecipientJobData $recipientJobData)
	{
		$this->recipientJobData = $recipientJobData;
		
	}
	
	/**
	 * Function retrieves instance of recipient job data
	 * @param KalturaEmailNotificationRecipientJobData $recipientJobData
	 * @param KalturaClient $kClient
	 * @return KEmailNotificationRecipientEngine
	 */
	public static function getEmailNotificationRecipientEngine(KalturaEmailNotificationRecipientJobData $recipientJobData)
	{
		return KalturaPluginManager::loadObject('KEmailNotificationRecipientEngine', $recipientJobData->providerType, array($recipientJobData));
	}

	
	/**
	 * Function returns an array of the recipients who should receive the email notification regarding the category
	 * @param array $contentParameters
	 */
	abstract function getRecipients (array $contentParameters);

	protected function getUsersByUserIds($userIds)
	{
		$userFilter = new KalturaUserFilter();
		$pager = new KalturaFilterPager();
		$maxIdIn = 150;
		$pager->pageSize = $maxIdIn;
		$idsArray = explode(',', $userIds);
		$offset = 0;
		$usersListResponse = array();
		$allUsers = array();

		while($offset * $maxIdIn < count($idsArray))
		{
			$currentUserArrIds = array_slice($idsArray, $offset * $maxIdIn, $maxIdIn);
			if(count($currentUserArrIds) == 1)
			{
				$currentUserStrIds = $currentUserArrIds[0];
			}
			else
			{
				$currentUserStrIds = implode(',',$currentUserArrIds);
			}
			$userFilter->idIn = $currentUserStrIds;
			$usersListResponse[] = (KBatchBase::$kClient->user->listAction($userFilter, $pager))->objects;
			$allUsers = array_merge($allUsers, $usersListResponse[$offset]);
			$offset++;
		}

		if(!(count($allUsers) > 0))
		{
			return null;
		}

		return $allUsers;
	}

	protected function getGroupUserIds($groupId)
	{
		$groupFilter = new KalturaGroupUserFilter();
		$groupFilter->groupIdEqual = $groupId;
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;

		$groupUserList = KBatchBase::$kClient->groupUser->listAction($groupFilter, $pager);
		if(!($groupUserList->totalCount > 0))
		{
			return null;
		}

		$groupUserIds = array();
		foreach ($groupUserList->objects as $user)
		{
			$groupUserIds[]= $user->userId;
		}

		$groupUserIdsString = implode(',',$groupUserIds);

		return $groupUserIdsString;
	}
}