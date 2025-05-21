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
		$groupTypes = isset($this->groupType) ? array($this->groupType) : array(GroupType::GROUP, GroupType::APPLICATIVE_GROUP);

		$c = KalturaCriteria::create(kuserPeer::OM_CLASS);
		$groupFilter = $this->toObject();
		$groupFilter->attachToCriteria($c);
		$c->addAnd(kuserPeer::TYPE, $groupTypes, Criteria::IN);
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
