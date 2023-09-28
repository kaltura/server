<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaUserAppRoleFilter extends KalturaUserAppRoleBaseFilter
{
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
		
		$c = new Criteria();
		$userAppRoleFilter = $this->toObject();
		$c->addAnd(KuserToUserRolePeer::APP_GUID, null, Criteria::ISNOTNULL);
		$c->addAnd(kuserPeer::PARTNER_ID, kCurrentContext::getCurrentPartnerId(), Criteria::EQUAL);
		$c->addAnd(kuserPeer::STATUS, KuserStatus::DELETED, Criteria::NOT_EQUAL);
		
		$userAppRoleFilter->attachToCriteria($c);
		$pager->attachToCriteria($c);
		
		// disable default criteria (which only retrieve results that 'app_guid === null' for backward compatibility)
		KuserToUserRolePeer::setUseCriteriaFilter(false);
		
		// join with 'kuser' table to return active or blocked users (not deleted) which will fail on alpha/lib/model/KuserToUserRole.php:43
		$list = KuserToUserRolePeer::doSelectJoinkuser($c);
		$resultCount = count($list);
		if ($resultCount && ($resultCount < $pager->pageSize))
		{
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		}
		else
		{
			KalturaFilterPager::detachFromCriteria($c);
			$totalCount = KuserToUserRolePeer::doCountJoinkuser($c);
		}
		
		KuserToUserRolePeer::setUseCriteriaFilter(true);
		
		$response->objects = KalturaUserAppRoleArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		
		return $response;
	}
	
	/**
	 * @throws KalturaAPIException
	 * @throws PropelException
	 * @throws kCoreException
	 */
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		$this->fixCsvFilterProperties();
		
		$partnerId = kCurrentContext::getCurrentPartnerId();
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
			$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $this->userIdEqual);
			$this->userIdEqual = $kuser ? $kuser->getId() : -1; // set -1 = no result will be returned when the user is missing
		}
		
		if ($isAdminSession && !empty($this->userIdIn))
		{
			$this->userIdIn = myKuserUtils::preparePusersToKusersFilter($this->userIdIn);
		}
		
		if (!empty($this->userRoleIdEqual))
		{
			$userRole = UserRolePeer::retrieveByPK($this->userRoleIdEqual);
			$this->userRoleIdEqual = $userRole ? $userRole->getId() : -1;
		}
		
		if (!empty($this->userRoleIdIn))
		{
			$this->userRoleIdIn = UserRolePeer::retrieveExistingUserRolesCsvByCsv($this->userRoleIdIn);
		}
		
		if (!empty($this->appGuidEqual))
		{
			$appGuidExist =  MicroServiceAppRegistry::getExistingAppGuid($partnerId, $this->appGuidEqual);
			$this->appGuidEqual = $appGuidExist ? $appGuidExist : 'null'; // set to 'null' will not return results (if set to '' 'attachToCriteria' will remove it from mysql query)
		}
		
		if(!empty($this->appGuidIn))
		{
			$this->appGuidIn = MicroServiceAppRegistry::getExistingAppGuidsFromCsv($partnerId, $this->appGuidIn);
		}
		
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
}
