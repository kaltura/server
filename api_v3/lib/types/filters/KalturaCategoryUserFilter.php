<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaCategoryUserFilter extends KalturaCategoryUserBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($coreFilter = null, $props_to_skip = array()) 
	{
		if(is_null($coreFilter))
			$coreFilter = new categoryKuserFilter();
			
		return parent::toObject($coreFilter, $props_to_skip);
	}
}
