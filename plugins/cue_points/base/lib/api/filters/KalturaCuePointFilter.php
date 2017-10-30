<?php
/**
 * @package plugins.cuePoint
 * @subpackage api.filters
 */
class KalturaCuePointFilter extends KalturaCuePointBaseFilter
{
	/**
	 * @var string
	 */
	public $freeText;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $userIdEqualCurrent;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $userIdCurrent;
	
	static private $map_between_objects = array
	(
		"cuePointTypeEqual" => "_eq_type",
		"cuePointTypeIn" => "_in_type",
		"freeText" => "_free_text",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	protected function validateEntryIdFiltered()
	{
		if(!$this->idEqual && !$this->idIn && !$this->entryIdEqual && !$this->entryIdIn)
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL,
					$this->getFormattedPropertyNameWithClassName('idEqual') . '/' . $this->getFormattedPropertyNameWithClassName('idIn') . '/' .
					$this->getFormattedPropertyNameWithClassName('entryIdEqual') . '/' . $this->getFormattedPropertyNameWithClassName('entryIdIn'));
	}

	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new CuePointFilter();
	}
	
	protected function translateUserIds()
	{		
		if($this->userIdCurrent == KalturaNullableBoolean::TRUE_VALUE)
		{
			if(kCurrentContext::$ks_kuser_id)
			{
				$this->userIdEqual = kCurrentContext::$ks_kuser_id;
			}
			else
			{
				$this->isPublicEqual = KalturaNullableBoolean::TRUE_VALUE;
			}
			$this->userIdCurrent = null;
		}
		
		if(isset($this->userIdEqual)){
			$dbKuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $this->userIdEqual);
			if (! $dbKuser) {
				throw new KalturaAPIException ( KalturaErrors::INVALID_USER_ID );
			}
			$this->userIdEqual = $dbKuser->getId();
		}
		
		if(isset($this->userIdIn)){
			$userIds = explode(",", $this->userIdIn);
			foreach ($userIds as $userId){
				$dbKuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $userId);
				if (! $dbKuser) {
				    throw new KalturaAPIException ( KalturaErrors::INVALID_USER_ID );
			}
				$kuserIds = $dbKuser->getId().",";
			}
			
			$this->userIdIn = $kuserIds;
		}
	}
	
	protected function getCriteria()
	{
	    return KalturaCriteria::create(CuePointPeer::OM_CLASS);
	}
	
	protected function doGetListResponse(KalturaFilterPager $pager, $type = null)
	{
		$this->validateEntryIdFiltered();
		if (!is_null($this->userIdEqualCurrent) && $this->userIdEqualCurrent)
		{
			$this->userIdEqual = kCurrentContext::getCurrentKsKuserId();
		}
		else
		{
			$this->translateUserIds();
		}
		
		$c = $this->getCriteria();

		if($type)
		{
			$this->cuePointTypeEqual = $type;
			$this->cuePointTypeIn = null;
		}

		$entryIds = null;
		if ($this->entryIdEqual) {
			$entryIds = array($this->entryIdEqual);
		} else if ($this->entryIdIn) {
			$entryIds = explode(',', $this->entryIdIn);
		}
		
		if (! is_null ( $entryIds )) {
			$entryIds = entryPeer::filterEntriesByPartnerOrKalturaNetwork ( $entryIds, kCurrentContext::getCurrentPartnerId());
			if (! $entryIds) {
				return array(array(), 0);
			}
			
			$this->entryIdEqual = null;
			$this->entryIdIn = implode ( ',', $entryIds );
		}

		$cuePointFilter = $this->toObject();
		$cuePointFilter->attachToCriteria($c);

		$pager->attachToCriteria($c);
			
		$list = CuePointPeer::doSelect($c);
		
		return array($list, count($list));
	}
	
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		//Was added to avoid braking backward compatibility for old player chapters module
		if(isset($this->tagsLike) && $this->tagsLike==KalturaAnnotationFilter::CHAPTERS_PUBLIC_TAG)
			KalturaCriterion::disableTag(KalturaCriterion::TAG_WIDGET_SESSION);

		list($list, $totalCount) = $this->doGetListResponse($pager, $type);
		$response = new KalturaCuePointListResponse();
		$response->objects = KalturaCuePointArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
	
		return $response;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		return $this->getTypeListResponse($pager, $responseProfile);
	}

	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::validateForResponseProfile()
	 */
	public function validateForResponseProfile()
	{
		if(		!kCurrentContext::$is_admin_session
			&&	!$this->idEqual
			&&	!$this->idIn
			&&	!$this->systemNameEqual
			&&	!$this->systemNameIn)
		{
			if(PermissionPeer::isValidForPartner(PermissionName::FEATURE_ENABLE_RESPONSE_PROFILE_USER_CACHE, kCurrentContext::getCurrentPartnerId()))
			{
				KalturaResponseProfileCacher::useUserCache();
				return;
			}
			
			throw new KalturaAPIException(KalturaCuePointErrors::USER_KS_CANNOT_LIST_RELATED_CUE_POINTS, get_class($this));
		}
	}
}
