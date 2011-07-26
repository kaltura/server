<?php
/**
 * @package plugins.codeCuePoint
 * @subpackage api.filters
 */
class KalturaCodeCuePointFilter extends KalturaCodeCuePointBaseFilter
{
	private $map_between_objects = array
	(
		"codeLike" => "_like_name",
		"codeMultiLikeOr" => "_mlikeor_name",
		"codeMultiLikeAnd" => "_mlikeand_name",
		"codeEqual" => "_eq_name",
		"codeIn" => "_in_name",
		"descriptionLike" => "_like_text",
		"descriptionMultiLikeOr" => "_mlikeor_text",
		"descriptionMultiLikeAnd" => "_mlikeand_text",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}
}
