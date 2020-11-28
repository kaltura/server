<?php

/**
 * Add & Manage GroupUser
 *
 * @service groupUser
 */
class GroupUserService extends KalturaBaseService
{
	const USER_GROUP_SYNC_THRESHOLD_DEFUALT = '50';

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		$this->applyPartnerFilterForClass('KuserKgroup');
	}

	/**
	 * Add new GroupUser
	 *
	 * @action add
	 * @param KalturaGroupUser $groupUser
	 * @return KalturaGroupUser
	 * @throws KalturaAPIException
	 */
	function addAction(KalturaGroupUser $groupUser)
	{
		$this->checkPermissionsForGroupUser($groupUser->groupId);
		/* @var $dbGroupUser KuserKgroup*/
		$partnerId = $this->getPartnerId();

		//verify kuser exists
		$kuser = kuserPeer::getKuserByPartnerAndUid( $partnerId, $groupUser->userId);
		if ( !$kuser || $kuser->getType() != KuserType::USER)
			throw new KalturaAPIException ( KalturaErrors::USER_NOT_FOUND, $groupUser->userId );

		//verify kgroup exists
		$kgroup = kuserPeer::getKuserByPartnerAndUid( $partnerId, $groupUser->groupId);
		if ( !$kgroup || $kgroup->getType() != KuserType::GROUP)
			throw new KalturaAPIException ( KalturaErrors::GROUP_NOT_FOUND, $groupUser->userId );

		//verify kuser does not belongs to kgroup
		$kuserKgroup = KuserKgroupPeer::retrieveByKuserIdAndKgroupId($kuser->getId(), $kgroup->getId());
		if($kuserKgroup)
			throw new KalturaAPIException (KalturaErrors::GROUP_USER_ALREADY_EXISTS);
		
		$this->validateKuserkGroupCoExistence($kgroup, $kuser->getId());

		//verify user does not belongs to more than max allowed groups
		$criteria = new Criteria();
		$criteria->add(KuserKgroupPeer::KUSER_ID, $kuser->getId());
		$criteria->add(KuserKgroupPeer::STATUS, KuserKgroupStatus::ACTIVE);
		if (KuserKgroupPeer::doCount($criteria) > KuserKgroup::MAX_NUMBER_OF_GROUPS_PER_USER){
			throw new KalturaAPIException (KalturaErrors::USER_EXCEEDED_MAX_GROUPS);
		}


		$dbGroupUser = $groupUser->toInsertableObject();
		$dbGroupUser->setPartnerId($this->getPartnerId());
		$dbGroupUser->setStatus(KuserKgroupStatus::ACTIVE);
		$dbGroupUser->save();
		$groupUser->fromObject($dbGroupUser);

		return $groupUser;
	}

	/**
	 * update GroupUser
	 *
	 * @action update
	 * @param string $groupUserId
	 * @param KalturaGroupUser $groupUser
	 * @return KalturaGroupUser
	 * @throws KalturaAPIException
	 */
	function updateAction($groupUserId, KalturaGroupUser $groupUser)
	{
		$currentDBGroupUser = KuserKgroupPeer::retrieveByPK($groupUserId);
		if (!$currentDBGroupUser)
		{
			throw new KalturaAPIException(KalturaErrors::GROUP_USER_NOT_FOUND);
		}

		$this->checkPermissionsForGroupUser($currentDBGroupUser->getKgroupId());
		$dbGroupUser = $groupUser->toUpdatableObject($currentDBGroupUser);
		$dbGroupUser->save();
		$groupUser = new KalturaGroupUser();
		$groupUser->fromObject($dbGroupUser, $this->getResponseProfile());
		return $groupUser;
	}

	/**
	 * delete by userId and groupId
	 *
	 * @action delete
	 * @param string $userId
	 * @param string $groupId
	 * @throws KalturaAPIException
	 */
	function deleteAction($userId, $groupId)
	{
		$this->checkPermissionsForGroupUser($groupId);
		$partnerId = $this->getPartnerId();
		//verify kuser exists
		$kuser = kuserPeer::getKuserByPartnerAndUid( $partnerId, $userId);
		if (!$kuser)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);
		}


		//verify group exists
		$kgroup = kuserPeer::getKuserByPartnerAndUid(  $partnerId, $groupId);
		if (!$kgroup)
		{
			//if the delete worker was triggered due to group deletion
			if(kCurrentContext::$master_partner_id != Partner::BATCH_PARTNER_ID)
			{
				throw new KalturaAPIException(KalturaErrors::GROUP_NOT_FOUND, $groupId);
			}

			kuserPeer::setUseCriteriaFilter(false);
			$kgroup = kuserPeer::getKuserByPartnerAndUid($partnerId, $groupId);
			kuserPeer::setUseCriteriaFilter(true);

			if(!$kgroup)
			{
				throw new KalturaAPIException (KalturaErrors::GROUP_NOT_FOUND, $groupId);
			}
		}


		$dbKuserKgroup = KuserKgroupPeer::retrieveByKuserIdAndKgroupId($kuser->getId(), $kgroup->getId());
		if(!$dbKuserKgroup)
		{
			throw new KalturaAPIException(KalturaErrors::GROUP_USER_DOES_NOT_EXIST, $userId, $groupId);
		}

		$dbKuserKgroup->setStatus(KuserKgroupStatus::DELETED);
		$dbKuserKgroup->save();

		$groupUser = new KalturaGroupUser();
		$groupUser->fromObject($dbKuserKgroup);
	}

	/**
	 * List all GroupUsers
	 * 
	 * @action list
	 * @param KalturaGroupUserFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaGroupUserListResponse
	 */
	function listAction(KalturaGroupUserFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
		{
			$filter = new KalturaGroupUserFilter();
		}

		$this->checkPermissionsForList($filter);

		if (!$pager)
		{
			$pager = new KalturaFilterPager();
		}
			
		return $filter->getListResponse($pager, $this->getResponseProfile());
	}
	/**
	 * sync by userId and groupIds
	 *
	 * @action sync
	 * @param string $userId
	 * @param string $groupIds
	 * @param bool $removeFromExistingGroups
	 * @param bool $createNewGroups
	 * @return KalturaBulkUpload|null
	 * @throws KalturaAPIException
	 */
	public function syncAction($userId, $groupIds, $removeFromExistingGroups = true, $createNewGroups = true)
	{
		if(strpos($groupIds,';')===false)
		{
			$seperator = ',';
		}
		else
		{
			$seperator = ';';
		}

		$groupIdsList = explode($seperator, $groupIds);
		self::validateSyncGroupUserArgs($userId, $groupIdsList, $groupIds);

		$kUser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $userId);
		if (!$kUser || $kUser->getType() != KuserType::USER)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);
		}

		$groupLimit = kConf::get('user_groups_sync_threshold', 'local', self::USER_GROUP_SYNC_THRESHOLD_DEFUALT);
		$bulkUpload = null;
		$bulkGroupUserSyncCsv = new kBulkGroupUserSyncCsv($kUser, $groupIdsList);
		$shouldHandleGroupsInBatch = ($groupLimit < count($groupIdsList));
		if (!$shouldHandleGroupsInBatch)
		{
			list($groupIdsToRemove, $groupIdsToAdd) = $bulkGroupUserSyncCsv->getSyncGroupUsers($removeFromExistingGroups, $createNewGroups);
			$this->initService('groupuser', 'groupuser', 'add');
			$shouldHandleGroupsInBatch = $this->addUserGroups($userId, $groupIdsToAdd) || !empty($groupIdsToRemove);
		}
		if ($shouldHandleGroupsInBatch)
		{
			$bulkUpload = self::handleGroupUserInBatch($bulkGroupUserSyncCsv, $removeFromExistingGroups, $createNewGroups);
		}

		return $bulkUpload;
	}

	protected function getNumberOfUsersInGroup($group)
	{
		$numberOfUsersPerGroup = $group->getMembersCount();
		if(!$numberOfUsersPerGroup)
		{
			$criteria = new Criteria();
			$criteria->add(KuserKgroupPeer::KGROUP_ID, $group->getId());
			$criteria->add(KuserKgroupPeer::STATUS, KuserKgroupStatus::ACTIVE);
			$numberOfUsersPerGroup = KuserKgroupPeer::doCount($criteria);
		}
		return $numberOfUsersPerGroup;
	}

	protected static function handleGroupUserInBatch(kBulkGroupUserSyncCsv $bulkGroupUserSyncCsv, $removeFromExistingGroups, $createNewGroups)
	{
		$fileData = $bulkGroupUserSyncCsv->getSyncGroupUsersCsvFile($removeFromExistingGroups, $createNewGroups);
		if (!$fileData)
		{
			return null;
		}

		$bulkService = new BulkService();
		$bulkService->initService('bulkupload_bulk', 'bulk', 'addUsers');
		return $bulkService->addUsersAction($fileData);
	}

	/**
	 * @param $userId
	 * @param $groupIdsToAdd
	 * @return bool (true if errors occurred)
	 */
	protected function addUserGroups($userId, $groupIdsToAdd)
	{
		$shouldHandleGroupsInBatch = false;
		foreach ($groupIdsToAdd as $groupId)
		{
			try
			{
				$groupUser = new KalturaGroupUser();
				$groupUser->userId = $userId;
				$groupUser->groupId = $groupId;
				$groupUser->creationMode = KalturaGroupUserCreationMode::AUTOMATIC;
				$this->addAction($groupUser);
			}
			catch (Exception $e)
			{
				$shouldHandleGroupsInBatch = true;
			}
		}
		return $shouldHandleGroupsInBatch;
	}

	public function addGroupUsersToClonedGroup($kUsers, $newGroup, $originalGroupId)
	{
		$isAsync = false;
		$groupUsersLimit = kConf::get('user_groups_sync_threshold', 'local', self::USER_GROUP_SYNC_THRESHOLD_DEFUALT);
		$bulkGroupUserSyncCsv = new kBulkGroupUsersToGroupCsv($kUsers, $newGroup->getPuserId());
		$shouldHandleGroupsUsersInBatch = ($groupUsersLimit < count($kUsers));
		if (!$shouldHandleGroupsUsersInBatch)
		{
			$this->initService('groupuser', 'groupuser', 'add');
			list($shouldHandleGroupsUsersInBatch, $userToAddInBulk) = $this->addUserGroupsToGroup($kUsers, $newGroup, $originalGroupId);
			$kUsers = $userToAddInBulk;
		}
		if ($shouldHandleGroupsUsersInBatch)
		{
			$isAsync = true;
			$bulkGroupUserSyncCsv->AddGroupUserInBatch($kUsers, $originalGroupId);
		}
		return $isAsync;
	}

	/**
	 * @param $userIdsToAdd
	 * @param $groupId
	 * @return array(bool (true if errors occurred),$usersToAddInBulk - users that we failed while trying to add them to group)
	 */
	public function addUserGroupsToGroup($userToAdd, $group, $originalGroupId)
	{
		$usersToAddInBulk = array();
		$groupId = $group->getPuserId();
		$shouldHandleGroupsInBatch = false;
		foreach ($userToAdd as $user)
		{
			$originalGroupUser = KuserKgroupPeer::retrieveByKuserIdAndKgroupId($user->getId(),$originalGroupId);
			try
			{
				$groupUser = new KalturaGroupUser();
				$groupUser->userId = $user->getPuserId();
				$groupUser->groupId = $groupId;
				$groupUser->creationMode = KalturaGroupUserCreationMode::AUTOMATIC;
				$groupUser->userRole = $originalGroupUser->getUserRole();
				$this->addAction($groupUser);
			}
			catch (Exception $e)
			{
				$shouldHandleGroupsInBatch = true;
				$usersToAddInBulk[] = $user;
			}
		}
		return array($shouldHandleGroupsInBatch, $usersToAddInBulk);
	}

	/**
	 * @param $userId
	 * @param $groupIdsList
	 * @param $groupIds
	 * @throws KalturaAPIException
	 */
	protected static function validateSyncGroupUserArgs($userId, $groupIdsList, $groupIds)
	{
		if (!preg_match(kuser::PUSER_ID_REGEXP, $userId))
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_FIELD_VALUE, 'userId');
		}

		if(!strlen(trim($groupIds)))
		{
			throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, 'groupIds');
		}

		foreach ($groupIdsList as $groupId)
		{
			if (!preg_match(kuser::PUSER_ID_REGEXP, trim($groupId)))
			{
				throw new KalturaAPIException(KalturaErrors::INVALID_FIELD_VALUE, 'groupIds');
			}
		}
	}

	protected function throwServiceForbidden()
	{
		$e = new KalturaAPIException ( APIErrors::SERVICE_FORBIDDEN, $this->serviceId.'->'.$this->actionName);
		header("X-Kaltura:error-".$e->getCode());
		header("X-Kaltura-App: exiting on error ".$e->getCode()." - ".$e->getMessage());
		throw $e;
	}

	protected function checkPermissionsForGroupUser($groupId)
	{
		if(!$this->checkPermissionsForGroupUserFromKs() && !self::checkIfKsUserIsGroupManager($groupId))
		{
			$this->throwServiceForbidden();
		}
	}

	public static function checkIfKsUserIsGroupManager($pUserGroupId)
	{
		$kuserId = kCurrentContext::getCurrentKsKuserId();
		if($kuserId)
		{
			$groupUser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $pUserGroupId);
			if($groupUser)
			{
				$ksUserGroup = KuserKgroupPeer::retrieveByKuserIdAndKgroupId($kuserId, $groupUser->getKuserId());
				if ($ksUserGroup && $ksUserGroup->getUserRole() == GroupUserRole::MANAGER)
				{
					return true;
				}
			}
		}

		return false;
	}

	protected function checkPermissionsForGroupUserFromKs()
	{
		return (kCurrentContext::$is_admin_session || kCurrentContext::$ks_partner_id == Partner::BATCH_PARTNER_ID ||
			kPermissionManager::isPermitted("CONTENT_MANAGE_ASSIGN_USER_GROUP"));
	}

	/**
	 * @param KalturaGroupUserFilter $filter
	 * @throws KalturaAPIException
	 */
	protected function checkPermissionsForList($filter)
	{
		if(!$this->checkPermissionsForGroupUserFromKs())
		{
			if($filter->groupIdEqual == null && $filter->userIdEqual == null)
			{
				throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL,
					$filter->getFormattedPropertyNameWithClassName('userIdEqual') .
					'/' . $this->getFormattedPropertyNameWithClassName('groupIdEqual'));
			}
			else if($filter->userIdEqual != null)
			{
				$kuser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $filter->userIdEqual);
				if($kuser->getKuserId() != kCurrentContext::getCurrentKsKuserId())
				{
					$this->throwServiceForbidden();
				}
			}
			else if(!self::checkIfKsUserIsGroupManager($filter->groupIdEqual))
			{
				$this->throwServiceForbidden();
			}
		}
	}
	
	protected function validateKuserkGroupCoExistence(kuser $kGroup, $kuserId)
	{
		$coExistenceBlockedIds = $kGroup->getCoExistenceBlockedIdsArray();
		if(!count($coExistenceBlockedIds))
		{
			return;
		}
		
		$kuserGroups = KuserKgroupPeer::retrieveKgroupIdsByKuserIds($kuserId);
		if(!array_intersect($coExistenceBlockedIds, $kuserGroups))
		{
			return;
		}
		
		throw new KalturaAPIException (KalturaErrors::GROUPS_CANNOT_CO_EXIST, $kuserId, $kGroup->getId(), $kGroup->getCoExistenceBlockedIds());
	}
}
