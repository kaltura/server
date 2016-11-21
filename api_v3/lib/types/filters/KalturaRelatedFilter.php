<?php
/**
 * @package api
 * @subpackage filters
 */
abstract class KalturaRelatedFilter extends KalturaFilter
{
	/**
	 * @param KalturaFilterPager $pager
	 * @param KalturaDetachedResponseProfile $responseProfile
	 * @return KalturaListResponse
	 */
	abstract public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null);
	
	public function validateForResponseProfile()
	{
		
	}
}
