<?php
/**
 * @package plugins.group
 * @subpackage api.filters
 */
class KalturaGroupFilter extends KalturaUserFilter
{
	/**
	 * @var KalturaGroupType
	 */
	public $groupType;

	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$c = KalturaCriteria::create(kuserPeer::OM_CLASS);
		$groupFilter = $this->toObject();
		$groupFilter->attachToCriteria($c);
		$groupType = $this->groupType ?? KuserType::GROUP;
		$c->addAnd(kuserPeer::TYPE, $groupType);
		$c->addAnd(kuserPeer::PUSER_ID, NULL, KalturaCriteria::ISNOTNULL);
		$pager->attachToCriteria($c);
		$list = kuserPeer::doSelect($c);
		$totalCount = $c->getRecordsCount();
		$newList = KalturaGroupArray::fromDbArray($list, $responseProfile);
		$response = new KalturaGroupListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		return $response;
	}
}
