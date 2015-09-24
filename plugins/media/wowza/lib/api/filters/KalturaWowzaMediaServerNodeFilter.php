<?php
/**
 * @package plugins.wowza
 * @subpackage api.filters
 */
class KalturaWowzaMediaServerNodeFilter extends KalturaWowzaMediaServerNodeBaseFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if(!$type)
			$type = WowzaPlugin::getWowzaMediaServerTypeCoreValue(WowzaMediaServerNodeType::WOWZA_MEDIA_SERVER);
	
		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}
