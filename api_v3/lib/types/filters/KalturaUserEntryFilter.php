<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaUserEntryFilter extends KalturaUserEntryBaseFilter
{

	/**
	 * @var KalturaNullableBoolean
	 */
	public $userIdEqualCurrent;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $isAnonymous;
	
	/**
	 * @var string
	 */
	public $privacyContextEqual;
	
	/**
	 * @var string
	 */
	public $privacyContextIn;

	/**
	 * @var int
	 */
	public $partnerId;

	static private $map_between_objects = array
	(
		"privacyContextEqual" => "_eq_privacy_context",
		"privacyContextIn" => "_in_privacy_context",
		"partnerId" => "_eq_partner_id"
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/**
	 * @return baseObjectFilter
	 */
	protected function getCoreFilter()
	{
		return new UserEntryFilter();
	}
	
	protected function validateFilter()
	{
		if(!$this->userIdEqual && !$this->userIdIn && !$this->entryIdEqual && !$this->entryIdIn)
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL,
				$this->getFormattedPropertyNameWithClassName('userIdEqual') . '/' . $this->getFormattedPropertyNameWithClassName('userIdIn') . '/' .
				$this->getFormattedPropertyNameWithClassName('entryIdEqual') . '/' . $this->getFormattedPropertyNameWithClassName('entryIdIn'));
	}

	protected function getEntriesByEntitledKuser()
	{
		$currentKsKuserId = kCurrentContext::getCurrentKsKuserId();

		$allEntryIds = $this->entryIdEqual ? array($this->entryIdEqual) : explode(',', $this->entryIdIn);
		$entries = entryPeer::retrieveByPKs($allEntryIds);
		$entitledEntriesIds = array();

		/* @var $entry entry */
		foreach ($entries as $entry)
		{
			if(	$entry->isEntitledKuserEdit($currentKsKuserId)		||
				$entry->isEntitledKuserPublish($currentKsKuserId)	||
				$entry->isEntitledKuserView($currentKsKuserId) )
			{
				$entitledEntriesIds[] = $entry->getEntryId();
			}
		}
		return array($entitledEntriesIds, array_diff($allEntryIds, $entitledEntriesIds));
	}

	protected function buildCriteriaByEntitledEntries(Criteria $c, $entitledEntriesIds, $notEntitledEntriesIds)
	{
		$notEntitledEntriesCriterion = $c->getNewCriterion(UserEntryPeer::ENTRY_ID, $notEntitledEntriesIds, Criteria::IN);
		$notEntitledEntriesCriterion->addAnd($c->getNewCriterion(UserEntryPeer::KUSER_ID, kCurrentContext::getCurrentKsKuserId(), Criteria::EQUAL));

		$criteria = $c->getNewCriterion(UserEntryPeer::ENTRY_ID, $entitledEntriesIds, Criteria::IN);
		$criteria->addOr($notEntitledEntriesCriterion);

		$c->addAnd($criteria);
		return $c;
	}

	/**
	 * @param KalturaFilterPager $pager
	 * @param KalturaDetachedResponseProfile $responseProfile
	 * @return KalturaListResponse
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$response = new KalturaUserEntryListResponse();
		if ( in_array(kCurrentContext::getCurrentSessionType(), array(kSessionBase::SESSION_TYPE_NONE,kSessionBase::SESSION_TYPE_WIDGET)) )
		{
			$response->totalCount = 0;
			return $response;
		}

		$c = new Criteria();
		$userEntryFilter = $this->toObject();

		// For User KS and if no user ID was given in the filter, retrieve all the user entries for which the user is entitled to.
		$disableDefaultCriteria = false;
		if (kCurrentContext::$ks && !kCurrentContext::$is_admin_session && !$this->userIdEqual && !$this->userIdIn)
		{
			$disableDefaultCriteria = true;
			list($entitledEntriesIds, $notEntitledEntriesIds) = $this->getEntriesByEntitledKuser();

			$c = $this->buildCriteriaByEntitledEntries($c, $entitledEntriesIds, $notEntitledEntriesIds);
			$c->addAnd ( UserEntryPeer::STATUS, UserEntryStatus::DELETED, Criteria::NOT_EQUAL);

			UserEntryPeer::setUseCriteriaFilter(false);

			$userEntryFilter->unsetByName('_eq_entry_id');
			$userEntryFilter->unsetByName('_in_entry_id');
		}
		elseif (in_array($this->statusEqual, array(KalturaUserEntryStatus::RECYCLED, KalturaUserEntryStatus::DELETED)))
		{
			$disableDefaultCriteria = true;
			UserEntryPeer::setUseCriteriaFilter(false);
		}

		$userEntryFilter->attachToCriteria($c);
		$pager->attachToCriteria($c);
		$list = UserEntryPeer::doSelect($c);

		$resultCount = count($list);
		if ($resultCount && ($resultCount < $pager->pageSize))
		{
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		}
		else
		{
			KalturaFilterPager::detachFromCriteria($c);
			$totalCount = UserEntryPeer::doCount($c);
		}

		if($disableDefaultCriteria)
		{
			UserEntryPeer::setUseCriteriaFilter(true);
		}

		$response->totalCount = $totalCount;
		$response->objects = KalturaUserEntryArray::fromDbArray($list, $responseProfile);
		return $response;
	}
	

	public function toObject ($object_to_fill = null, $props_to_skip = array())
	{
		if (kCurrentContext::$ks_partner_id != Partner::BATCH_PARTNER_ID)
		{
			if (!is_null($this->privacyContextEqual) || !is_null($this->privacyContextIn))
			{
				throw new KalturaAPIException(KalturaErrors::USER_ENTRY_FILTER_FORBIDDEN_FIELDS_USED);
			}
			$this->partnerId = kCurrentContext::getCurrentPartnerId();
		}
		
		if (!is_null($this->userIdEqualCurrent) && $this->userIdEqualCurrent)
		{
			$this->userIdEqual = kCurrentContext::getCurrentKsKuserId();
		}
		else
		{
			$this->fixFilterUserId();
		}
		$this->validateFilter();
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
	
	public function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var $srcObj UserEntryFilter */
		parent::doFromObject($srcObj, $responseProfile);
		if (kCurrentContext::$ks_partner_id == Partner::BATCH_PARTNER_ID) //batch should be able to get userEntry objects of deleted users.
				kuserPeer::setUseCriteriaFilter(false);
		
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


	/**
	 * The user_id is infact a puser_id and the kuser_id should be retrieved
	 */
	protected function fixFilterUserId()
	{
		if (kCurrentContext::$ks_partner_id == Partner::BATCH_PARTNER_ID)
		{
			kCurrentContext::$partner_id = $this->partnerId;
		}

		if ($this->userIdEqual !== null)
		{
			if (kCurrentContext::$ks_partner_id == Partner::BATCH_PARTNER_ID) //batch should be able to get userEntry objects of deleted users.
				kuserPeer::setUseCriteriaFilter(false);

			$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $this->userIdEqual);
			kuserPeer::setUseCriteriaFilter(true);
			if ($kuser)
				$this->userIdEqual = $kuser->getId();
			else
				$this->userIdEqual = -1; // no result will be returned when the user is missing
		}

		if(!empty($this->userIdIn))
		{
			$this->userIdIn = myKuserUtils::preparePusersToKusersFilter( $this->userIdIn );
		}
		if(!empty($this->userIdNotIn))
		{
			$this->userIdNotIn = myKuserUtils::preparePusersToKusersFilter( $this->userIdNotIn );
		}

		if(!is_null($this->isAnonymous))
		{
			if(KalturaNullableBoolean::toBoolean($this->isAnonymous)===false)
				$this->userIdNotIn .= self::getListOfAnonymousUsers();

			elseif(KalturaNullableBoolean::toBoolean($this->isAnonymous)===true)
				$this->userIdIn .= self::getListOfAnonymousUsers();
		}
	}

	public static function getListOfAnonymousUsers()
	{
		$anonKuserIds = "";
		$anonKusers = kuserPeer::getKuserByPartnerAndUids(kCurrentContext::getCurrentPartnerId(), array(0,''));
		foreach ($anonKusers as $anonKuser) {
			$anonKuserIds .= ",".$anonKuser->getKuserId();
		}
		return $anonKuserIds;
	}
	
	public function getEmptyListResponse()
	{
		$res = new KalturaUserEntryListResponse();
		$res->objects = array();
		$res->totalCount = 0;
		return $res;
	}
}
