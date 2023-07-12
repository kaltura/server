<?php

/**
 * @package plugins.sessionCuePoint
 * @subpackage api.filters
 */
class KalturaSessionCuePointFilter extends KalturaSessionCuePointBaseFilter
{
	/* (non-PHPdoc)
 	 * @see KalturaFilter::getCoreFilter()
 	 */
	protected function getCoreFilter()
	{
		return new SessionCuePointFilter();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::validateForResponseProfile()
	 */
	public function validateForResponseProfile()
	{
		if (!kCurrentContext::$is_admin_session
			&& !$this->isPublicEqual)
		{
			parent::validateForResponseProfile();
		}
	}
	
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		return parent::getTypeListResponse($pager, $responseProfile, SessionCuePointPlugin::getCuePointTypeCoreValue(SessionCuePointType::SESSION));
	}
}
