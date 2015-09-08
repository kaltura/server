<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaEdgeServerNodeFilter extends KalturaEdgeServerNodeBaseFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if(!$type)
			$type = serverNodeType::EDGE;
	
		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}
