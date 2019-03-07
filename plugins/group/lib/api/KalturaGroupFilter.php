<?php
/**
 * @package plugins.group
 * @subpackage api.filters
 */
class KalturaGroupFilter extends KalturaUserFilter
{
	static private $order_by_map = array
	(
		"+membersCount" => "+members_count",
		"-membersCount" => "-members_count",
	);

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), self::$order_by_map);
	}

	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$c = KalturaCriteria::create(kuserPeer::OM_CLASS);
		$groupFilter = $this->toObject();
		$groupFilter->attachToCriteria($c);
		$c->addAnd(kuserPeer::TYPE,KuserType::GROUP);
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