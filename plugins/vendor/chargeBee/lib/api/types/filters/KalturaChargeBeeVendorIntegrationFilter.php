<?php
/**
 * @package plugins.chargeBee
 * @subpackage api.filters
 */
class KalturaChargeBeeVendorIntegrationFilter extends KalturaChargeBeeVendorIntegrationBaseFilter
{

	public function getCoreFilter()
	{
		return new VendorFilter();
	}

	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		if (!$this->typeIn && !$this->typeEqual)
		{
			$this->typeIn = KalturaVendorTypeEnum::CHARGE_BEE_FREE_TRIAL . ',' . KalturaVendorTypeEnum::CHARGE_BEE_PAYGO;
		}

		$c = new Criteria();
		$filter = $this->toObject();
		$filter->attachToCriteria($c);
		$pager->attachToCriteria($c);

		$list = VendorIntegrationPeer::doSelect($c);

		$resultCount = count($list);
		if ($resultCount && $resultCount < $pager->pageSize)
		{
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		}
		else
		{
			KalturaFilterPager::detachFromCriteria($c);
			$totalCount = VendorIntegrationPeer::doCount($c);
		}

		$response = new KalturaChargeBeeVendorIntegrationResponse();
		$response->objects = KalturaChargeBeeVendorIntegrationArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;
	}
}
