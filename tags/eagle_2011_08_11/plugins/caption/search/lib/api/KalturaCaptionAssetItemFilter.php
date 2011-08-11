<?php
/**
 * @package plugins.captionSearch
 * @subpackage api.filters
 */
class KalturaCaptionAssetItemFilter extends KalturaCaptionAssetFilter
{
	private $map_between_objects = array
	(
		"idEqual" => "_eq_caption_asset_id",
		"idIn" => "_in_caption_asset_id",
		"startTimeGreaterThanOrEqual" => "_gte_start_time",
		"startTimeLessThanOrEqual" => "_lte_start_time",
		"endTimeGreaterThanOrEqual" => "_gte_end_time",
		"endTimeLessThanOrEqual" => "_lte_end_time",
		"tagsLike" => "_like_tags",
		"tagsMultiLikeOr" => "_mlikeor_tags",
		"tagsMultiLikeAnd" => "_mlikeand_tags",
		"contentLike" => "_like_content",
		"contentMultiLikeOr" => "_mlikeor_content",
		"contentMultiLikeAnd" => "_mlikeand_content",
		"partnerDescriptionLike" => "_like_partner_description",
		"partnerDescriptionMultiLikeOr" => "_mlikeor_partner_description",
		"partnerDescriptionMultiLikeAnd" => "_mlikeand_partner_description",
		"languageEqual" => "_eq_language",
		"languageIn" => "_in_language",
		"labelEqual" => "_eq_label",
		"labelIn" => "_in_label",
	);

	private $order_by_map = array
	(
		"+startTime" => "+start_time",
		"-startTime" => "-start_time",
		"+endTime" => "+end_time",
		"-endTime" => "-end_time",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), $this->order_by_map);
	}

	/**
	 * @var string
	 */
	public $tagsLike;

	/**
	 * @var string
	 */
	public $tagsMultiLikeOr;

	/**
	 * @var string
	 */
	public $tagsMultiLikeAnd;
	
	/**
	 * @var string
	 */
	public $contentLike;

	/**
	 * @var string
	 */
	public $contentMultiLikeOr;

	/**
	 * @var string
	 */
	public $contentMultiLikeAnd;

	/**
	 * @var string
	 */
	public $partnerDescriptionLike;

	/**
	 * @var string
	 */
	public $partnerDescriptionMultiLikeOr;

	/**
	 * @var string
	 */
	public $partnerDescriptionMultiLikeAnd;

	/**
	 * @var KalturaLanguage
	 */
	public $languageEqual;

	/**
	 * @var string
	 */
	public $languageIn;

	/**
	 * @var string
	 */
	public $labelEqual;

	/**
	 * @var string
	 */
	public $labelIn;
	
	/**
	 * @var int
	 */
	public $startTimeGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $startTimeLessThanOrEqual;

	/**
	 * @var int
	 */
	public $endTimeGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $endTimeLessThanOrEqual;
}