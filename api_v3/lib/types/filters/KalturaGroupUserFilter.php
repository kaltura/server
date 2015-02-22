<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaGroupUserFilter extends KalturaGroupUserBaseFilter
{

	static private $map_between_objects = array	();

	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new KuserKgroupFilter();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($coreFilter = null, $props_to_skip = array()) 
	{
		$this->validateUserIdOrGroupIdFiltered();
			
		return parent::toObject($coreFilter, $props_to_skip);
	}
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}


	protected function validateUserIdOrGroupIdFiltered()
	{
		if(!$this->userIdEqual && !$this->userIdIn && !$this->groupIdEqual && !$this->groupIdIn)
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, $this->getFormattedPropertyNameWithClassName('userIdEqual') . '/' . $this->getFormattedPropertyNameWithClassName('userIdIn') . '/' . $this->getFormattedPropertyNameWithClassName('groupIdEqual') . '/' . $this->getFormattedPropertyNameWithClassName('groupIdIn'));
	}

	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		if($this->groupIdEqual)
		{
			$partnerId = $this->getPartnerId();

			$c = new Criteria();
			$c->add(kuserPeer::PARTNER_ID, $partnerId);
			$c->add(kuserPeer::PUSER_ID, $this->groupIdEqual);
			$c->add(kuserPeer::TYPE, KuserType::GROUP);
			if (kCurrentContext::$ks_partner_id == Partner::BATCH_PARTNER_ID) //batch should be able to get categoryUser of deleted users.
				kuserPeer::setUseCriteriaFilter(false);

			// in case of more than one deleted kusers - get the last one
			$c->addDescendingOrderByColumn(kuserPeer::UPDATED_AT);

			$kuser = kuserPeer::doSelectOne($c);
			kuserPeer::setUseCriteriaFilter(true);

			if (!$kuser)
			{
				$response = new KalturaGroupUserListResponse();
				$response->objects = new KalturaGroupUserArray();
				$response->totalCount = 0;

				return $response;
			}

			$this->groupIdEqual = $kuser->getId();
		}

		if($this->userIdEqual)
		{
			$partnerId = $this->getPartnerId();

			$c = new Criteria();
			$c->add(kuserPeer::PARTNER_ID, $partnerId);
			$c->add(kuserPeer::PUSER_ID, $this->userIdEqual);
			$c->add(kuserPeer::TYPE, KuserType::USER);
			$kuser = kuserPeer::doSelectOne($c);

			if (!$kuser)
			{
				$response = new KalturaGroupUserListResponse();
				$response->objects = new KalturaGroupUserArray();
				$response->totalCount = 0;

				return $response;
			}

			$this->userIdEqual = $kuser->getId();
		}

		if($this->userIdIn)
		{
			$usersIds = explode(',', $this->userIdIn);
			$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;

			$c = new Criteria();
			$c->add(kuserPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
			$c->add(kuserPeer::PUSER_ID, $usersIds, Criteria::IN);
			$c->add(kuserPeer::TYPE, KuserType::USER);
			$kusers = kuserPeer::doSelect($c);

			if (!$kusers)
			{
				$response = new KalturaGroupUserListResponse();
				$response->objects = new KalturaGroupUserArray();
				$response->totalCount = 0;

				return $response;
			}

			$usersIds = array();
			foreach($kusers as $kuser)
			{
				/* @var $kuser kuser */
				$usersIds[] = $kuser->getId();
			}

			$this->userIdIn = implode(',', $usersIds);
		}

		if($filter->groupIdIn)
		{
			$groupIdIn = explode(',', $this->groupIdIn);
			$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;

			$c = new Criteria();
			$c->add(kuserPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
			$c->add(kuserPeer::PUSER_ID, $groupIdIn, Criteria::IN);
			$c->add(kuserPeer::TYPE, KuserType::GROUP);
			$kusers = kuserPeer::doSelect($c);

			if (!$kusers)
			{
				$response = new KalturaGroupUserListResponse();
				$response->objects = new KalturaGroupUserArray();
				$response->totalCount = 0;

				return $response;
			}

			$groupIdIn = array();
			foreach($kusers as $kuser)
			{
				/* @var $kuser kuser */
				$groupIdIn[] = $kuser->getId();
			}

			$this->groupIdIn = implode(',', $groupIdIn);
		}

		$kuserKgroupFilter = $this->toObject();
		
		$c = KalturaCriteria::create(KuserKgroupPeer::OM_CLASS);
		$kuserKgroupFilter->attachToCriteria($c);
		$pager->attachToCriteria($c);
		$c->applyFilters();
		
		$list = KuserKgroupPeer::doSelect($c);

		$newList = KalturaGroupUserArray::fromDbArray($list, $responseProfile);
		
		$response = new KalturaGroupUserListResponse();
		$response->objects = $newList;
		$resultCount = count($newList);
		if ($resultCount && $resultCount < $pager->pageSize)
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		else
		{
			KalturaFilterPager::detachFromCriteria($c);
			$totalCount = KuserKgroupPeer::doCount($c);
		}
		$response->totalCount = $totalCount;
		return $response;
	}
}
