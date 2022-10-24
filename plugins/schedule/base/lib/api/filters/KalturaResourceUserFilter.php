<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaResourceUserFilter extends KalturaResourceUserBaseFilter
{
	/**
	 * @inheritDoc
	 */
	protected function getCoreFilter()
	{
		return new ResourceUserFilter();
	}

	/**
	 * @inheritDoc
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$response = new KalturaResourceUserListResponse();
		if($this->userIdIn)
		{
			$usersIds = explode(',', $this->userIdIn);
			$partnerId = kCurrentContext::getCurrentPartnerId();

			$c = new Criteria();
			$c->add(kuserPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
			$c->add(kuserPeer::PUSER_ID, $usersIds, Criteria::IN);
			$kusers = kuserPeer::doSelect($c);

			$usersIds = array();
			foreach($kusers as $kuser)
			{
				/* @var $kuser kuser */
				$usersIds[] = $kuser->getId();
			}

			$this->userIdIn = implode(',', $usersIds);
		}
		if($this->userIdEqual)
		{
			$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;

			$c = new Criteria();
			$c->add(kuserPeer::PARTNER_ID, $partnerId);
			$c->add(kuserPeer::PUSER_ID, $this->userIdEqual);

			// in case of more than one deleted kusers - get the last one
			$c->addDescendingOrderByColumn(kuserPeer::UPDATED_AT);

			$kuser = kuserPeer::doSelectOne($c);

			if (!$kuser)
			{
				$response = new KalturaCategoryUserListResponse();
				$response->objects = new KalturaCategoryUserArray();
				$response->totalCount = 0;

				return $response;
			}

			$this->userIdEqual = $kuser->getId();
		}

		$c = new Criteria();
		$resourceUserFilter = $this->toObject();
		$resourceUserFilter->attachToCriteria($c);
		$pager->attachToCriteria($c);
		$list = ResourceUserPeer::doSelect($c);

		KalturaFilterPager::detachFromCriteria($c);
		$totalCount = ResourceUserPeer::doCount($c);

		$newList = KalturaResourceUserArray::fromDbArray($list, $responseProfile);

		$response->objects = $newList;
		$response->totalCount = $totalCount;


		return $response;
	}

	public function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var $srcObj ResourceUserFilter */
		parent::doFromObject($srcObj, $responseProfile);

		if ($srcObj->get('_eq_user_id'))
		{
			$this->userIdEqual = $this->prepareKusersToPusersFilter($srcObj->get('_eq_user_id'));
		}
		if ($srcObj->get('_in_user_id'))
		{
			$this->userIdIn = $this->prepareKusersToPusersFilter($srcObj->get('_in_user_id'));
		}
		if ($srcObj->get('_notin_user_id'))
		{
			$this->userIdNotIn = $this->prepareKusersToPusersFilter($srcObj->get('_notin_user_id'));
		}

	}
}
