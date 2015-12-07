<?php
/**
 * @package plugins.thumbCuePoint
 * @subpackage api.filters
 */
class KalturaThumbCuePointFilter extends KalturaThumbCuePointBaseFilter
{
	static private $map_between_objects = array
	(
		"descriptionLike" => "_like_text",
		"descriptionMultiLikeOr" => "_mlikeor_text",
		"descriptionMultiLikeAnd" => "_mlikeand_text",
		"titleLike" => "_like_name",
		"titleMultiLikeOr" => "_mlikeor_name",
		"titleMultiLikeAnd" => "_mlikeand_name",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
