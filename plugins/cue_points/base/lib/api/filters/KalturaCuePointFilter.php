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
	
	protected function doGetListResponse(KalturaFilterPager $pager, $type = null)
	{
		$this->validateEntryIdFiltered();
		$this->translateUserIds();
		
		$c = KalturaCriteria::create(CuePointPeer::OM_CLASS);
		if($type)
		{
			$c->add(CuePointPeer::TYPE, $type);
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
		
		return array($list, $c->getRecordsCount());
	}
	
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null, $type = null)
	{
		list($list, $totalCount) = $this->doGetListResponse($pager, $type);
		$response = new KalturaCuePointListResponse();
		$response->objects = KalturaCuePointArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
	
		return $response;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		return $this->getTypeListResponse($pager, $responseProfile);
	}
}
