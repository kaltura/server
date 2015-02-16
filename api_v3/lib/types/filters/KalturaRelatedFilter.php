<?php
/**
 * @package api
 * @subpackage filters
 */
abstract class KalturaRelatedFilter extends KalturaFilter
{
	/**
	 * @param KalturaFilterPager $pager
	 * @param KalturaResponseProfileBase $responseProfile
	 * @return KalturaListResponse
	 */
	abstract public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null);
}
