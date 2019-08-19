<?php
/**
 * @package plugins.sso
 * @subpackage api.filters
 */
class KalturaSsoFilter extends KalturaRelatedFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"partnerIdEqual" => "_eq_partner_id",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
	);

	static private $order_by_map = array
	(
		"+id" => "+id",
		"-id" => "-id",
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), self::$order_by_map);
	}

	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new SsoFilter();
	}


	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$c = new Criteria();
		$ssoFilter = $this->toObject();
		$ssoFilter->attachToCriteria($c);
		$c->addAnd(VendorIntegrationPeer::VENDOR_TYPE,VendorTypeEnum::SSO);
		$partnerIdFromKS = kCurrentContext::getCurrentPartnerId();
		if ($this->partnerIdEqual != $partnerIdFromKS)
		{
			$c->addAnd(VendorIntegrationPeer::PARTNER_ID, $partnerIdFromKS);
		}
		if ($this->statusEqual || $this->statusIn)
		{
			VendorIntegrationPeer::allowDeletedInCriteriaFilter();
		}
		$pager->attachToCriteria($c);
		$list = VendorIntegrationPeer::doSelect($c);
		$totalCount = VendorIntegrationPeer::doCount($c);
		$newList = KalturaSsoArray::fromDbArray($list, $responseProfile);
		$response = new KalturaSsoListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		return $response;
	}

	/**
	 * @var int
	 */
	public $idEqual;

	/**
	 * @var string
	 */
	public $idIn;

	/**
	 * @var int
	 */
	public $partnerIdEqual;

	/**
	 * @var KalturaSsoStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var time
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $createdAtLessThanOrEqual;

}