<?php
/**
 * @package plugins.conference
 * @subpackage api.filters
 */
class KalturaConferenceServerNodeFilter extends KalturaConferenceServerNodeBaseFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if(!$type)
			$type = ConferencePlugin::getCoreValue('serverNodeType',ConferenceServerNodeType::CONFERENCE_SERVER);
	
		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}
