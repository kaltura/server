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
		
		if (in_array(kCurrentContext::getCurrentSessionType(), array(kSessionBase::SESSION_TYPE_NONE,kSessionBase::SESSION_TYPE_WIDGET)))
		{
			$response->totalCount = 0;
			return $response;
		}
		
		$c = new Criteria();
		$userAppRoleFilter = $this->toObject();
		$c->addAnd(KuserToUserRolePeer::APP_GUID, null, Criteria::ISNOTNULL);
		
		$userAppRoleFilter->attachToCriteria($c);
		$pager->attachToCriteria($c);
		
		KuserToUserRolePeer::setUseCriteriaFilter(false);
		$list = KuserToUserRolePeer::doSelect($c);
		
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
		// TODO: think I need to verify the mongo Ids are correct and KS permissions here
		
		if (!empty($this->userIdEqual))
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $this->userIdEqual);
			if ($kuser)
			{
				$this->userIdEqual = $kuser->getId();
			}
			else
			{
				$this->userIdEqual = -1; // no result will be returned when the user is missing
			}
		}
		
		if (!empty($this->userIdIn))
		{
			$this->userIdIn = myKuserUtils::preparePusersToKusersFilter($this->userIdIn);
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
}
