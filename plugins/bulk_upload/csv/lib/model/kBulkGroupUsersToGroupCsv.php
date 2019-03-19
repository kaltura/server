<?php
/**
 * @package plugins.bulkUploadCsv
 * @subpackage lib.model
 */
class kBulkGroupUsersToGroupCsv
{
	const NAME = 'name';
	const TMP_NAME = 'tmp_name';
	const ACTION = '*action';
	const USER_ID = 'userId';
	const GROUP_ID = 'group';
	const USER_ROLE = 'userRole';

	protected $kusers;
	protected $groupId;

	public function __construct($kusers, $groupId)
	{
		$this->kusers = $kusers;
		$this->groupId = $groupId;
	}

	public function AddGroupUserInBatch($groupIdsToAdd, $originalGroupId)
	{
		$csvFile = $this->buildGroupUsersCsv($groupIdsToAdd, $originalGroupId);
		$fileData = array(
			self::NAME => basename($csvFile),
			self::TMP_NAME => $csvFile
		);

		$bulkService = new BulkService();
		$bulkService->initService('bulkupload_bulk', 'bulk', 'addUsers');
		return $bulkService->addUsersAction($fileData);
	}

	public function buildGroupUsersCsv($groupUserIdsToAdd, $originalGroupId)
	{
		$csvPath = tempnam(sys_get_temp_dir(), 'csv');
		$csvData = array();
		foreach ($groupUserIdsToAdd as $addGroupUserId)
		{
			$originalGroupUser = KuserKgroupPeer::retrieveByKuserIdAndKgroupId($addGroupUserId->getId(),$originalGroupId);

			$csvData[] = array(
				self::ACTION => BulkUploadAction::UPDATE,
				self::USER_ID => $addGroupUserId->getPuserId(),
				self::GROUP_ID => trim($this->groupId),
				self::USER_ROLE => $originalGroupUser->getUserRole()
			);
		}
		$f = fopen($csvPath, 'w');
		fputcsv($f, array(self::ACTION, self::USER_ID, self::GROUP_ID, self::USER_ROLE));
		foreach ($csvData as $csvLine)
		{
			fputcsv($f, $csvLine);
		}
		fclose($f);
		return $csvPath;
	}

}