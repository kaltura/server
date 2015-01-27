<?php

/**
 * Add & Manage GroupUser
 *
 * @service groupUser
 */
class GroupUserService extends KalturaBaseService
{

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
	 */
	function addAction(KalturaGroupUser $groupUser)
	{
		/* @var $dbGroupUser KuserKgroup*/

		$partnerId = kCurrentContext::getCurrentPartnerId();

		//verify kuser exists
		$kuser = kuserPeer::getKuserByPartnerAndUid( $partnerId, $groupUser->userId, false , KuserType::USER);
		if (! $kuser)
			throw new KalturaAPIException ( KalturaErrors::USER_NOT_FOUND, $groupUser->userId );

		//verify group exists
		$kgroup = kuserPeer::getKuserByPartnerAndUid( $partnerId, $groupUser->groupId, false , KuserType::GROUP);
		if (! $kgroup)
			throw new KalturaAPIException ( KalturaErrors::GROUP_NOT_FOUND, $groupUser->userId );

		//verify kuser does not belongs to kgroup
		$kuserKgroup = KuserKgroupPeer::retrieveByKuserIdAndKgroupId($kuser->getId(), $kgroup->getId());
		if($kuserKgroup)
			throw new KalturaAPIException (KalturaErrors::GROUP_USER_ALREADY_EXISTS);

		//verify user does not belongs to more than max allowed groups
		$kuserKgroups = KuserKgroupPeer::retrieveByKuserId($kuser->getId());
		if ( count($kuserKgroups) > KuserKgroup::MAX_NUMBER_OF_GROUPS_PER_USER){
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
	 * delete by userId and groupId
	 *
	 * @action delete
	 * @param string $userId
	 * @param string $groupId
	 */
	function deleteAction($userId, $groupId)
	{
		$partnerId = $this->getPartnerId();

		//verify kuser exists
		$kuser = kuserPeer::getKuserByPartnerAndUid( $partnerId, $userId, false, KuserType::USER);
		if (!$kuser)
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $userId);


		//verify group exists
		$kgroup = kuserPeer::getKuserByPartnerAndUid(  $partnerId, $groupId ,false, KuserType::GROUP);
		if (! $kgroup){
			//if the delete worker was triggered due to group deletion
			if (kCurrentContext::$master_partner_id != Partner::BATCH_PARTNER_ID)
				throw new KalturaAPIException(KalturaErrors::GROUP_NOT_FOUND, $groupId);

			kuserPeer::setUseCriteriaFilter(false);
			$kgroup = kuserPeer::getKuserByPartnerAndUid($partnerId, $groupId);
			kuserPeer::setUseCriteriaFilter(true);

			if (!$kgroup)
				throw new KalturaAPIException ( KalturaErrors::GROUP_NOT_FOUND, $groupId );
		}


		$dbKuserKgroup = KuserKgroupPeer::retrieveByKuserIdAndKgroupId($kuser->getId(), $kgroup->getId());
		if (!$dbKuserKgroup)
			throw new KalturaAPIException(KalturaErrors::GROUP_USER_DOES_NOT_EXISTS, $userId, $groupId);

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
	 * @throws KalturaErrors::MUST_FILTER_USERS_OR_GROUPS
	 */
	function listAction(KalturaGroupUserFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaGroupUserFilter();

		if(!($filter->userIdEqual || $filter->userIdIn || $filter->groupIdEqual || $filter->groupIdIn))
			throw new KalturaAPIException(KalturaErrors::MUST_FILTER_USERS_OR_GROUPS);

		if (!$pager)
			$pager = new KalturaFilterPager();

		
		$kuserKgroupFilter = new KuserKgroupFilter();
		$filter->toObject($kuserKgroupFilter);
		
		$c = KalturaCriteria::create(KuserKgroupPeer::OM_CLASS);
		$kuserKgroupFilter->attachToCriteria($c);
		$pager->attachToCriteria($c);
//		$c->applyFilters();
		
		$list = KuserKgroupPeer::doSelect($c);

		$newList = KalturaGroupUserArray::fromDbArray($list);
		
		$response = new KalturaGroupUserListResponse();
		$response->objects = $newList;
		$response->totalCount = KuserKgroupPeer::doCount($c);
		
		return $response;
	}

}