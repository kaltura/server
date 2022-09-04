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
		$userFilter->idIn = $userIds;
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;

		$users = KBatchBase::$kClient->user->listAction($userFilter, $pager);

		if(!($users->totalCount > 0))
			return null;

		return $users->objects;
	}

	protected function getGroupUserIds($groupId)
	{
		$groupFilter = new KalturaGroupUserFilter();
		$groupFilter->groupIdEqual = $groupId;
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;

		$groupUserList = KBatchBase::$kClient->groupUser->listAction($groupFilter, $pager);
		if(!($groupUserList->totalCount > 0))
			return null;

		$groupUserIds = array();
		foreach ($groupUserList->objects as $user)
		{
			$groupUserIds[]= $user->userId;
		}

		$groupUserIdsString = implode(',',$groupUserIds);

		return $groupUserIdsString;
	}
}