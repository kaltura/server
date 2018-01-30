<?php
/**
 * @package plugins.konference
 * @subpackage api.filters
 */
class KalturaConferenceServerNodeFilter extends KalturaConferenceServerNodeBaseFilter
{
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if(!$type)
			$type = KonferencePlugin::getConferenceCoreValue(ConferenceServerNodeType::CONFERENCE_SERVER);
	
		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}
