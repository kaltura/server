<?php
/**
 * @package plugins.bulkUploadCsv
 * @subpackage lib.model
 */
class kBulkGroupUserSyncCsv
{

	const NAME = 'name';
	const TMP_NAME = 'tmp_name';
	const MODE = 'mode';
	const TYPE = 'type';
	const ACTION = '*action';
	const USER_ID = 'userId';
	const GROUP_ID = 'group';
	const CAPABILITIES = 'capabilities';

	protected $userMap;
	protected $kuser;
	protected $groupIds;

	public function __construct($kuser, $groupIds)
	{
		$this->kuser = $kuser;
		$this->groupIds = $groupIds;
		$this->userMap = array();
	}

	public function getSyncGroupUsersCsvFile($removeFromExistingGroups, $createNewGroups, $capabilities = null)
	{
		list($groupIdsToRemove, $groupIdsToAdd) = $this->getSyncGroupUsers($removeFromExistingGroups, $createNewGroups);
		//if no groups to add/remove don't add the job
		if(empty($groupIdsToRemove) && empty($groupIdsToAdd))
			return null;

		$csvFile = $this->buildUsersCsv($groupIdsToRemove, $groupIdsToAdd, $capabilities);
		$fileData = array(
			self::NAME => basename($csvFile),
			self::TMP_NAME => $csvFile
		);

		return $fileData;
	}

	protected function buildUserMap($users)
	{
		foreach ($users as $user)
		{
			/**@var kuser $user*/
			$this->userMap[$user->getId()][self::MODE] = $user->getUserMode();
			$this->userMap[trim($user->getPuserId())][self::MODE] = $user->getUserMode();
			$this->userMap[$user->getId()][self::TYPE] = $user->getType();
			$this->userMap[trim($user->getPuserId())][self::TYPE] = $user->getType();
		}
	}

	public function getSyncGroupUsers($removeFromExistingGroups, $createNewGroups)
	{
		$requestGroupIds = array_map('trim', $this->groupIds);
		$partnerId = $this->kuser->getPartnerId();
		$currentUserGroups = KuserKgroupPeer::retrieveByKuserIds(array($this->kuser->getId()));
		$currentKgroupsIds = array();
		$currentPgroupsIds = array();
		foreach ($currentUserGroups as $currentUserGroup)
		{
			$currentKgroupsIds[] = $currentUserGroup->getKgroupId();
			$currentPgroupsIds[] = $currentUserGroup->getPgroupId();
		}

		$groupsPuserIds = array_merge($currentPgroupsIds, $requestGroupIds);
		$users = kuserPeer::getKuserByPartnerAndUids($partnerId, $groupsPuserIds);

		$this->buildUserMap($users);
		$groupsToRemove = array();
		$groupsToAdd = array();

		foreach ($currentUserGroups as $currentUserGroup)
		{
			if($this->shouldRemoveGroup($currentUserGroup, $requestGroupIds, $removeFromExistingGroups))
				$groupsToRemove[] = $currentUserGroup->getPgroupId();
		}

		foreach ($requestGroupIds as $requestPGroupId)
		{
			if($this->shouldAddGroup($requestPGroupId, $currentPgroupsIds, $createNewGroups))
				$groupsToAdd[] = $requestPGroupId;
		}
		return array($groupsToRemove, $groupsToAdd);
	}

	protected function shouldRemoveGroup($currentUserGroup, &$requestGroupIds, $removeFromExistingGroups)
	{
		if(!in_array($currentUserGroup->getPgroupId(), $requestGroupIds) && //the group is not in the sync group list
			$currentUserGroup->getCreationMode() == GroupUserCreationMode::AUTOMATIC && //the group creation mode is automatic
			array_key_exists($currentUserGroup->getKgroupId(), $this->userMap) && //if the the group exists and removeFromExistingGroups flag is true and the group is not protected
			$removeFromExistingGroups &&
			$this->isGroupAndNotProtected($currentUserGroup->getKgroupId()))
			return true;

		return false;
	}

	protected function shouldAddGroup($requestPGroupId, &$currentPgroupsIds, $createNewGroups)
	{
		if(!in_array($requestPGroupId, $currentPgroupsIds) &&
			((!array_key_exists($requestPGroupId, $this->userMap) && $createNewGroups) || //group doesn't exist and create new group flag is true
				($this->isGroupAndNotProtected($requestPGroupId)))) //group exists and not protected
			return true;

		return false;
	}

	protected function isGroupAndNotProtected($key)
	{
		if(array_key_exists(self::TYPE, $this->userMap[$key]) && $this->userMap[$key][self::TYPE] == KuserType::GROUP &&
			array_key_exists(self::MODE, $this->userMap[$key]) && $this->userMap[$key][self::MODE] != KuserMode::PROTECTED_USER)
			return true;
		return false;
	}

	protected function buildUsersCsv($groupsToRemove, $groupIdsToAdd, $capabilities = null)
	{
		$userId = $this->kuser->getPuserId();
		$csvPath = tempnam(sys_get_temp_dir(), 'csv');
		$csvData = array();
		foreach ($groupsToRemove as $removeGroupId)
		{
			$csvData[] = array(
				self::ACTION => BulkUploadAction::UPDATE,
				self::USER_ID => trim($userId),
				self::GROUP_ID => "-$removeGroupId"
			);
		}
		foreach ($groupIdsToAdd as $addGroupId)
		{
			$csvData[] = array(
				self::ACTION => BulkUploadAction::UPDATE,
				self::USER_ID => trim($userId),
				self::GROUP_ID => $addGroupId,
				self::CAPABILITIES => $capabilities
			);
		}
		$f = fopen($csvPath, 'w');
		fputcsv($f, array(self::ACTION, self::USER_ID, self::GROUP_ID, self::CAPABILITIES));
		foreach ($csvData as $csvLine)
		{
			fputcsv($f, $csvLine);
		}
		fclose($f);
		return $csvPath;
	}

}
