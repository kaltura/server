<?php
/**
 * Abstract engine which retrieves a list of the email notification recipients.
 * 
 * @package plugins.emailNotification
 * @subpackage Scheduler
 */
const ID_QUERY_MAX_CHUNK_SIZE = 150;

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

	protected function getUsersOfGroupByGroupId($groupId)
	{
		$groupFilter = new KalturaGroupUserFilter();
		$groupFilter->groupIdEqual = $groupId;
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		$userFilter = new KalturaUserFilter();

		$groupUserList = KBatchBase::$kClient->groupUser->listAction($groupFilter, $pager);
		if(!($groupUserList->totalCount))
		{
			return null;
		}

		$groupUserIds = array();
		foreach ($groupUserList->objects as $user)
		{
			$groupUserIds[]= $user->userId;
		}

		$pager->pageSize = ID_QUERY_MAX_CHUNK_SIZE;
		$offset = 0;
		$allUsers = array();

		while($offset < count($groupUserIds))
		{
			$currentUserArrIds = array_slice($groupUserIds, $offset, ID_QUERY_MAX_CHUNK_SIZE);
			$currentUserStrIds = implode(',', $currentUserArrIds);
			$userFilter->idIn = $currentUserStrIds;
			$response = KBatchBase::$kClient->user->listAction($userFilter, $pager);
			if(!$response)
			{
				KalturaLog::debug("Failed to list users of group: ". $groupId);
				break;
			}
			$usersListObject = $response->objects;
			$totalCount = $response->totalCount;
			$allUsers = array_merge($allUsers, $usersListObject);
			$offset += min(ID_QUERY_MAX_CHUNK_SIZE, $totalCount);
		}

		if(!(count($allUsers) > 0))
		{
			return null;
		}

		return $allUsers;
	}
}