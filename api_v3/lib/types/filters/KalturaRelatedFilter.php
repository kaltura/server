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

	/**
	 * @param KalturaFilterPager $pager
	 * @param KalturaDetachedResponseProfile|null $responseProfile
	 * @return KalturaListResponse
	 * @throws Exception
	 */
	public function validateAndGetListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{

		if (MapFilterToList::validateAccess($this))
			return $this->getListResponse($pager,$responseProfile);
		return new KalturaListResponse();

	}

}
