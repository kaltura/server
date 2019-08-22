<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaSsoFilter extends KalturaSsoBaseFilter
{
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
		$pager->attachToCriteria($c);
		$list = VendorIntegrationPeer::doSelect($c);
		$totalCount = VendorIntegrationPeer::doCount($c);
		$newList = KalturaSsoArray::fromDbArray($list, $responseProfile);
		$response = new KalturaSsoListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		return $response;
	}
}
