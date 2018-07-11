<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaAppTokenFilter extends KalturaAppTokenBaseFilter
{
	static private $map_between_objects = array
	(
		"sessionUserIdEqual" => "_eq_kuser_id",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new appTokenFilter();
	}
}
