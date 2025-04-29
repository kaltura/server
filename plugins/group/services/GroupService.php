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
		$groupTypes = array(KuserType::GROUP, KuserType::APPLICATIVE_GROUP);
		if(!$dbGroup || !in_array($dbGroup->getType(), $groupTypes))
		{
			throw new KalturaAPIException(KalturaGroupErrors::INVALID_GROUP_ID);
		}
		return $dbGroup;
	}

	/**
	 * @action searchGroup
	 * @actionAlias elasticsearch_esearch.searchGroup
	 * @param KalturaESearchGroupParams $searchParams
	 * @param KalturaPager $pager
	 * @return KalturaESearchGroupResponse
	 */
	public function searchGroupAction(KalturaESearchGroupParams $searchParams, KalturaPager $pager = null)
	{
		$userSearch = new kUserSearch();
		list($coreResults, $objectCount) = self::initAndSearch($userSearch, $searchParams, $pager);
		$response = new KalturaESearchGroupResponse();
		$response->objects = KalturaESearchGroupResultArray::fromDbArray($coreResults, $this->getResponseProfile());
		$response->totalCount = $objectCount;
		return $response;
	}

	/**
	 * @param kBaseSearch $coreSearchObject
	 * @param $searchParams
	 * @param $pager
	 * @return array
	 */
	protected function initAndSearch($coreSearchObject, $searchParams, $pager)
	{
		list($coreSearchOperator, $objectStatusesArr, $objectId, $kPager, $coreOrder) =
			self::initSearchActionParams($searchParams, $pager);
		$elasticResults = $coreSearchObject->doSearch($coreSearchOperator, $kPager, $objectStatusesArr, $objectId, $coreOrder);

		list($coreResults, $objectCount) = kESearchCoreAdapter::transformElasticToCoreObject($elasticResults, $coreSearchObject);
		return array($coreResults, $objectCount);
	}

	protected static function initSearchActionParams($searchParams, KalturaPager $pager = null)
	{

		/**
		 * @var ESearchParams $coreParams
		 */
		$coreParams = $searchParams->toObject();

		$groupTypeItem = new ESearchUserItem();
		$groupTypeItem->setSearchTerm(KuserType::GROUP);
		$groupTypeItem->setItemType(ESearchItemType::EXACT_MATCH);
		$groupTypeItem->setFieldName(ESearchUserFieldName::TYPE);

		$applicativeGroupTypeItem = new ESearchUserItem();
		$applicativeGroupTypeItem->setSearchTerm(KuserType::APPLICATIVE_GROUP);
		$applicativeGroupTypeItem->setItemType(ESearchItemType::EXACT_MATCH);
		$applicativeGroupTypeItem->setFieldName(ESearchUserFieldName::TYPE);

		$orOperator = new ESearchOperator();
		$orOperator->setOperator(ESearchOperatorType::OR_OP);
		$orOperator->setSearchItems(array($groupTypeItem, $applicativeGroupTypeItem));

		$baseOperator = new ESearchOperator();
		$baseOperator->setOperator(ESearchOperatorType::AND_OP);
		$baseOperator->setSearchItems(array($coreParams->getSearchOperator(), $orOperator));

		$objectStatusesArr = array();
		$objectStatuses = $coreParams->getObjectStatuses();
		if (!empty($objectStatuses))
		{
			$objectStatusesArr = explode(',', $objectStatuses);
		}

		$kPager = null;
		if ($pager)
		{
			$kPager = $pager->toObject();
		}

		return array($baseOperator, $objectStatusesArr, $coreParams->getObjectId(), $kPager, $coreParams->getOrderBy());
	}

	/**
	 * clone the group (groupId), and set group id with the neeGroupName.
	 *
	 * @action clone
	 * @param string $originalGroupId The unique identifier in the partner's system
	 * @param string $newGroupId The unique identifier in the partner's system
	 * @param string $newGroupName The name of the new cloned group
	 * @return KalturaGroup The cloned group
	 *
	 * @throws KalturaErrors::INVALID_FIELD_VALUE
	 * @throws KalturaGroupErrors::INVALID_GROUP_ID
	 */
	public function cloneAction($originalGroupId, $newGroupId, $newGroupName = null)
	{
		$dbGroup = $this->getGroup($originalGroupId);

		if (!$dbGroup)
		{
			throw new KalturaAPIException(KalturaGroupErrors::INVALID_GROUP_ID, $originalGroupId);
		}

		$dbNewGroup = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $newGroupId);
		if ($dbNewGroup)
		{
			throw new KalturaAPIException(KalturaGroupErrors::DUPLICATE_GROUP_BY_ID, $newGroupId);
		}

		$group = new KalturaGroup();
		if ($newGroupName == null)
		{
			$newGroupName = $newGroupId;
		}
		$newDbGroup = $group->clonedObject($dbGroup, $newGroupId, $newGroupName);
		$newDbGroup->save();

		$groupUsers =  KuserKgroupPeer::retrieveKuserKgroupByKgroupId($dbGroup->getId());
		$kusers = $this->getKusersFromKuserKgroup($groupUsers);
		$GroupUser = new GroupUserService();
		$isAsync = $GroupUser->addGroupUsersToClonedGroup($kusers, $newDbGroup, $dbGroup->getId());
		if($isAsync)
		{
			$newDbGroup->setProcessStatus(GroupProcessStatus::PROCESSING);
			$newDbGroup->save();
		}

		$group->fromObject($newDbGroup, $this->getResponseProfile());

		return $group;
	}

	protected function getKusersFromKuserKgroup($groupUsers)
	{
		$kusers = array();
		foreach ($groupUsers as $groupUser)
		{
			$kuserId = $groupUser->getKuserId();
			$kusers[] = kuserPeer::retrieveByPK($kuserId);
		}
		return $kusers;
	}
}
