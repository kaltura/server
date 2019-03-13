<?php
/**
 * @service group
 * @package plugins.group
 * @subpackage api.services
 */

class GroupService extends KalturaBaseUserService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		$kuser = kCurrentContext::getCurrentKsKuser();
		if(!$kuser && !kCurrentContext::$is_admin_session)
		{
			throw new KalturaAPIException(KalturaErrors::USER_ID_NOT_PROVIDED_OR_EMPTY);
		}
	}

	/**
	 * Adds a new group (user of type group).
	 *
	 * @action add
	 * @param KalturaGroup $group a new group
	 * @return KalturaGroup The new group
	 *
	 * @throws KalturaErrors::DUPLICATE_USER_BY_ID
	 * @throws KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL
	 * @throws KalturaErrors::INVALID_FIELD_VALUE
	 * @throws KalturaErrors::UNKNOWN_PARTNER_ID
	 * @throws KalturaErrors::PASSWORD_STRUCTURE_INVALID
	 * @throws KalturaErrors::DUPLICATE_USER_BY_LOGIN_ID
	 */
	public function addAction(KalturaGroup $group)
	{
		$group->type = KuserType::GROUP;
		if (!preg_match(kuser::PUSER_ID_REGEXP, $group->id))
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_FIELD_VALUE, 'id');
		}

		$this->validateUserNames($group);
		$lockKey = "user_add_" . $this->getPartnerId() . $group->id;
		$ret =  kLock::runLocked($lockKey, array($this, 'adduserImpl'), array($group));
		return $ret;
	}

	/**
	 * Retrieves a group object for a specified group ID.
	 * @action get
	 * @param string $groupId The unique identifier in the partner's system
	 * @return KalturaGroup The specified user object
	 *
	 * @throws KalturaGroupErrors::INVALID_GROUP_ID
	 */
	public function getAction($groupId)
	{
		$dbGroup = $this->getGroup($groupId);

		if (!kCurrentContext::$is_admin_session )//TODO - add validation function to allow access to the group
			throw new KalturaAPIException(KalturaErrors::CANNOT_RETRIEVE_ANOTHER_USER_USING_NON_ADMIN_SESSION, $groupId);


		if (!$dbGroup)
			throw new KalturaAPIException(KalturaGroupErrors::INVALID_GROUP_ID, $groupId);

		$group = new KalturaGroup();
		$group->fromObject($dbGroup, $this->getResponseProfile());

		return $group;
	}

	/**
	 * Delete group by ID
	 *
	 * @action delete
	 * @param string $groupId The unique identifier in the partner's system
	 * @return KalturaGroup The deleted  object
	 * @throws KalturaErrors::INVALID_USER_ID
	 */
	public function deleteAction($groupId)
	{
		$dbGroup = self::getGroup($groupId);
		$dbGroup->setStatus(KalturaUserStatus::DELETED);
		$dbGroup->save();
		$group = new KalturaGroup();
		$group->fromObject($dbGroup, $this->getResponseProfile());
		return $group;
	}

	/**
	 * Update group by ID
	 *
	 * @action update
	 * @param string $groupId The unique identifier in the partner's system
	 * @param KalturaGroup the updated object
	 * @return KalturaGroup The updated  object
	 * @throws KalturaErrors::INVALID_USER_ID
	 */
	public function updateAction($groupId, KalturaGroup $group)
	{
		$dbGroup = self::getGroup($groupId);
		$dbGroup = $group->toUpdatableObject($dbGroup);
		$dbGroup->save();
		$group = new KalturaGroup();
		$group->fromObject($dbGroup, $this->getResponseProfile());
		return $group;
	}

	/**
	 * Lists group  objects that are associated with an account.
	 * Blocked users are listed unless you use a filter to exclude them.
	 * Deleted users are not listed unless you use a filter to include them.
	 *
	 * @action list
	 * @param KalturaGroupFilter $filter
	 * @param KalturaFilterPager $pager A limit for the number of records to display on a page
	 * @return KalturaGroupListResponse The list of user objects
	 */
	public function listAction(KalturaGroupFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaGroupFilter();

		if(!$pager)
			$pager = new KalturaFilterPager();

		return $filter->getListResponse($pager, $this->getResponseProfile());

	}

	protected function getGroup($groupId)
	{
		$dbGroup = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $groupId);
		if(!$dbGroup || $dbGroup->getType() != KuserType::GROUP)
		{
			throw new KalturaAPIException(KalturaGroupErrors::INVALID_GROUP_ID);
		}
		return $dbGroup;
	}

}