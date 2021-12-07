<?php


class KalturaTeamsVendorIntegrationUserFilter extends KalturaVendorIntegrationUserFilter
{

	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$userFilter = $this->toObject();
		$c = KalturaCriteria::create(kuserPeer::OM_CLASS);
		$userFilter->attachToCriteria($c);
		if (!is_null($this->roleIdEqual))
		{
			$roleCriteria = new Criteria();
			$roleCriteria->add ( KuserToUserRolePeer::USER_ROLE_ID , $this->roleIdEqual );
			$roleCriteria->addSelectColumn(KuserToUserRolePeer::KUSER_ID);
			$rs = KuserToUserRolePeer::doSelectStmt($roleCriteria);
			$kuserIds = $rs->fetchAll(PDO::FETCH_COLUMN);
			$c->add(kuserPeer::ID, $kuserIds, KalturaCriteria::IN);
		}
		$c->addAnd(kuserPeer::PUSER_ID, NULL, KalturaCriteria::ISNOTNULL);
		$pager->attachToCriteria($c);
		$list = kuserPeer::doSelect($c);
		$totalCount = $c->getRecordsCount();

		$newList = KalturaTeamsVendorIntegrationUserArray::fromDbArray($list, $responseProfile);
		$response = new KalturaTeamsVendorIntegrationUserListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		return $response;
	}

}