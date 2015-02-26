<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaFlavorAssetFilter extends KalturaFlavorAssetBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaAssetFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		$types = assetPeer::retrieveAllFlavorsTypes();
		return $this->getTypeListResponse($pager, $responseProfile, $types);
	}
}
