<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaAnnotationFilter extends KalturaAnnotationBaseFilter
{
	
	/**
	 * @param AnnotationFilter $annotationFilter
	 * @param array $propsToSkip
	 * @return AnnotationFilter
	 */
	public function toObject($annotationFilter = null, $propsToSkip = array())
	{
		if(!$annotationFilter)
			$annotationFilter = new AnnotationFilter();
			
		if(isset($this->userIdEqual))
			$this->userIdEqual = PuserKuserPeer::getKuserIdFromPuserId(kCurrentContext::$ks_partner_id, $this->userIdEqual);
		
		if(isset($this->userIdIn))
			$this->userIdIn = PuserKuserPeer::getKuserIdFromPuserIds(kCurrentContext::$ks_partner_id, explode(',', $this->userIdIn));
		
		return parent::toObject($annotationFilter, $propsToSkip);
	}
}