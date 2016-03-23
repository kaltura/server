<?php
/**
 * @package plugins.annotation
 * @subpackage api.filters
 */
class KalturaAnnotationFilter extends KalturaAnnotationBaseFilter
{
	const CHAPTERS_PUBLIC_TAG = 'chaptering';

	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::validateForResponseProfile()
	 */
	public function validateForResponseProfile()
	{
		if(		!kCurrentContext::$is_admin_session
			&&	!$this->isPublicEqual)
		{
			parent::validateForResponseProfile();
		}
	}

	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		//Was added to avoid braking backward compatibility for old player chapters module
		if(isset($this->tagsLike) && $this->tagsLike==self::CHAPTERS_PUBLIC_TAG)
			KalturaCriterion::disableTag(KalturaCriterion::TAG_WIDGET_SESSION);

		return parent::getTypeListResponse($pager, $responseProfile, AnnotationPlugin::getCuePointTypeCoreValue(AnnotationCuePointType::ANNOTATION));
	}
}
