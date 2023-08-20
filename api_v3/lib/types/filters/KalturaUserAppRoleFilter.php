<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaUserAppRoleFilter extends KalturaUserAppRoleBaseFilter
{
	/**
	 * Temporary until mongoWrapper
	 * EP Impersonated KS can list by app_guid only
	 *
	 * @var bool
	 */
	public static $EP_FILTER_RESULTS_ON_APP_GUID_ONLY_LIST = false;
	
	/**
	 * The User Id to search for
	 *
	 * @var string
	 */
	public $userIdEqual;
	
	/**
	 * Users Ids csv list
	 *
	 * @var string
	 */
	public $userIdIn;
	
	
	static private $map_between_objects = array
	(
		"userIdEqual" => "_eq_user_id",
		"userIdIn" => "_in_user_id",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	protected function getCoreFilter()
	{
		return new AppRoleFilter();
	}
	
	/**
	 * @param KalturaFilterPager $pager
	 * @param KalturaDetachedResponseProfile|null $responseProfile
	 * @return KalturaListResponse
	 * @throws PropelException
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$response = new KalturaUserAppRoleListResponse();
		
		if (in_array(kCurrentContext::getCurrentSessionType(), array(kSessionBase::SESSION_TYPE_NONE,kSessionBase::SESSION_TYPE_WIDGET)))
		{
			$response->objects = array();
			$response->totalCount = 0;
			return $response;
		}
		
		$c = new Criteria();
		$userAppRoleFilter = $this->toObject();
		$c->addAnd(KuserToUserRolePeer::APP_GUID, null, Criteria::ISNOTNULL);
		
		$userAppRoleFilter->attachToCriteria($c);
		$pager->attachToCriteria($c);
		
		// disable default criteria (which only retrieve results that 'app_guid === null' for backward compatibility)
		KuserToUserRolePeer::setUseCriteriaFilter(false);
		
		if (self::$EP_FILTER_RESULTS_ON_APP_GUID_ONLY_LIST)
		{
			$c->addGroupByColumn(KuserToUserRolePeer::APP_GUID);
		}
		
		$list = KuserToUserRolePeer::doSelect($c);
		
		// temporary until we have mongoWrapper to validate against app_registry that 'appGuid' belongs to partner
		if (self::$EP_FILTER_RESULTS_ON_APP_GUID_ONLY_LIST && count($list) && ($this->appGuidEqual || $this->appGuidIn))
		{
			$appGuidsBelongToKsPartner = $this->verifyAppGuidBelongToKsPartner($list);
			
			if (!count($appGuidsBelongToKsPartner))
			{
				$response->objects = array();
				$response->totalCount = 0;
				return $response;
			}
			
			$c = new Criteria();
			$c->addAnd(KuserToUserRolePeer::APP_GUID, null, Criteria::ISNOTNULL);
			
			// get results for the remaining appGuids which do belong to ks partner
			$this->appGuidIn = implode(',', $appGuidsBelongToKsPartner);
			$userAppRoleFilter = $this->toObject();
			$userAppRoleFilter->attachToCriteria($c);;
			$pager->attachToCriteria($c);
			
			$list = KuserToUserRolePeer::doSelect($c);
		}
		
		$resultCount = count($list);
		if ($resultCount && ($resultCount < $pager->pageSize))
		{
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		}
		else
		{
			KalturaFilterPager::detachFromCriteria($c);
			$totalCount = KuserToUserRolePeer::doCount($c);
		}
		
		KuserToUserRolePeer::setUseCriteriaFilter(true);
		
		$response->totalCount = $totalCount;
		$response->objects = KalturaUserAppRoleArray::fromDbArray($list, $responseProfile);
		return $response;
	}
	
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		$this->fixCsvFilterProperties();
		
		// TODO: KS permissions here
		$isAdminSession = kCurrentContext::getCurrentSessionType() === kSessionBase::SESSION_TYPE_ADMIN;
		
		// user ks can only retrieve results for himself
		if (!$isAdminSession)
		{
			$kuser = kCurrentContext::getCurrentKsKuser();
			if (!$kuser)
			{
				throw new KalturaAPIException(KalturaErrors::USER_ID_NOT_FOUND, kCurrentContext::$ks_uid);
			}
			
			$this->userIdEqual = $kuser->getId();
			$this->userIdIn = null;
		}
		
		if ($isAdminSession && !empty($this->userIdEqual))
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $this->userIdEqual);
			$this->userIdEqual = $kuser ? $kuser->getId() : -1; // set -1 = no result will be returned when the user is missing
		}
		
		if ($isAdminSession && !empty($this->userIdIn))
		{
			$this->userIdIn = myKuserUtils::preparePusersToKusersFilter($this->userIdIn);
		}
		
		if ($isAdminSession && !empty($this->userRoleIdEqual))
		{
			$userRole = UserRolePeer::retrieveByPK($this->userRoleIdEqual);
			$this->userRoleIdEqual = $userRole ? $userRole->getId() : -1;
		}
		
		if ($isAdminSession && !empty($this->userRoleIdIn))
		{
			// todo: wrap in a function inside UserRolePeer
			$userRoleIdsList = explode(kString::CSV_SEPARATOR, $this->userRoleIdIn);
			$userRoleIds = UserRolePeer::retrieveByPKs($userRoleIdsList);
			$this->userRoleIdIn = $userRoleIds ? implode(kString::CSV_SEPARATOR, $userRoleIds) : -1;
		}
		
		// todo add filter for appGuid once I have mongo wrapper to call app_registry and verify app_guid.partner_id == kCurrentContext::getCurrentPartnerId()
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
	
	public function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var $srcObj AppRoleFilter */
		parent::doFromObject($srcObj, $responseProfile);
		
		if ($srcObj->get('_eq_user_id'))
		{
			$this->userIdEqual = $this->prepareKusersToPusersFilter($srcObj->get('_eq_user_id'));
		}

		if ($srcObj->get('_in_user_id'))
		{
			$this->userIdIn = $this->prepareKusersToPusersFilter($srcObj->get('_in_user_id'));
		}
	}
	
	protected function fixCsvFilterProperties()
	{
		$this->userIdIn = !empty($this->userIdIn) ? kString::csvFixWhitespace($this->userIdIn) : null;
		$this->appGuidIn = !empty($this->appGuidIn) ? kString::csvFixWhitespace($this->appGuidIn) : null;
		$this->userRoleIdIn = !empty($this->userRoleIdIn) ? kString::csvFixWhitespace($this->userRoleIdIn) : null;
	}
	
	private function verifyAppGuidBelongToKsPartner($kuserToUserRoles)
	{
		$appGuidsBelongToKsPartner = array();
		
		if ($this->appGuidIn)
		{
			$appGuidsBelongToKsPartner = explode(',', $this->appGuidIn);
		}
		
		if ($this->appGuidEqual)
		{
			$appGuidsBelongToKsPartner = $this->appGuidEqual;
		}
		
		foreach ($kuserToUserRoles as $kuserToUserRole)
		{
			/* @var KuserToUserRole $kuserToUserRole */
			$kuser = kuserPeer::retrieveByPK($kuserToUserRole->getKuserId());
			if (!$kuser)
			{
				KalturaLog::debug('App Guid ID [' . $kuserToUserRole->getAppGuid() . '] and Kuser ID [' . $kuserToUserRole->getKuserId() . '] does not belong to KS Partner Id [' . kCurrentContext::getCurrentPartnerId() . '] - removing from filter');
				$key = array_search($kuserToUserRole->getAppGuid(), $appGuidsBelongToKsPartner);
				
				if ($key !== false)
				{
					unset($appGuidsBelongToKsPartner[$key]);
				}
			}
		}
		
		return $appGuidsBelongToKsPartner;
	}
}
