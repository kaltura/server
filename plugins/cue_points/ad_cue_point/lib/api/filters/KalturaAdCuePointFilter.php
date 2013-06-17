<?php
/**
 * @package plugins.adCuePoint
 * @subpackage api.filters
 */
class KalturaAdCuePointFilter extends KalturaAdCuePointBaseFilter
{
	static private $map_between_objects = array
	(
		"protocolTypeEqual" => "_eq_sub_type",
		"protocolTypeIn" => "_in_sub_type",
		"titleLike" => "_like_name",
		"titleMultiLikeOr" => "_mlikeor_name",
		"titleMultiLikeAnd" => "_mlikeand_name",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
