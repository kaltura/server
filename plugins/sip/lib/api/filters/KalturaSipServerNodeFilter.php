<?php
/**
 * @package plugins.sip
 * @subpackage api.filters
 */
class KalturaSipServerNodeFilter extends KalturaSipServerNodeBaseFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if(!$type)
			$type = SipPlugin::getCoreValue('serverNodeType',SipServerNodeType::SIP_SERVER);
	
		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}
