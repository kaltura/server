<?php

/**
 * Add & Manage GroupUser
 *
 * @service groupUser
 */
class GroupUserService extends KalturaBaseService
{
	/**
	 * Add new GroupUser
	 * 
	 * @action add
	 * @param KalturaGroupUser $groupUser
	 * @return KalturaGroupUser
	 */
	function addAction(KalturaGroupUser $groupUser)
	{
		$groupUser->partnerId = $this->getPartnerId();
		$dbGroupUser = $groupUser->toInsertableObject();
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
	 * @return KalturaGroupUser
	 */
	function deleteAction($userId, $groupId)
	{
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;

		//verify kuser exists
		$kuser = kuserPeer::getKuserByPartnerAndUid( $userId , $partnerId, false , KuserType::USER);
		if (! $kuser)
			throw new KalturaAPIException ( KalturaErrors::USER_NOT_FOUND, $userId );

		//verify group exists
		$kgroup = kuserPeer::getKuserByPartnerAndUid( $groupId , $partnerId , false , KuserType::GROUP);
		if (! $kgroup)
			throw new KalturaAPIException ( KalturaErrors::GROUP_NOT_FOUND, $groupId );

		$dbKuserKgroup = KuserKgroupPeer::getByKuserIdAndKgroupId($kuser->getId(), $kgroup->getId());
		if (!$dbKuserKgroup)
			throw new KalturaAPIException(KalturaErrors::GROUP_USER_DOES_NOT_EXISTS, $userId, $groupId);

		$dbKuserKgroup->setStatus(KuserKgroupStatus::DELETED);
		$dbKuserKgroup->save();
		$groupUser = new KalturaGroupUser();
		$groupUser->fromObject($dbKuserKgroup);
		
		return $groupUser;
	}

	/**
	 * List all GroupUsers
	 * 
	 * @action list
	 * @param KalturaGroupUserFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaGroupUserListResponce
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

		
		$kuserKgroupFilter = new kuserKgroupFilter();
		$filter->toObject($kuserKgroupFilter);
		
		$c = KalturaCriteria::create(KuserKgroupPeer::OM_CLASS);
		$kuserKgroupFilter->attachToCriteria($c);
		$pager->attachToCriteria($c);
		$c->applyFilters();
		
		$list = KuserKgroupPeer::doSelect($c);
		$totalCount = $c->getRecordsCount();
		
		$newList = KalturaGroupUserArray::fromDbArray($list);
		
		$response = new KalturaGroupUserListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		
		return $response;
	}

}