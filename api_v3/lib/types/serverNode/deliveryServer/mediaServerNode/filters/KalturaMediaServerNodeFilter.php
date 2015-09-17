<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaMediaServerNodeFilter extends KalturaMediaServerNodeBaseFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if(!$type)
			$type = WowzaPlugin::getWowzaMediaServerTypeCoreValue(WowzaMediaServerNodeType::WOWZA_MEDIA_SERVER);
	
		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}
