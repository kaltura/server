<?php
/**
 * @package plugins.captionSearch
 * @subpackage api.filters
 */
class KalturaCaptionAssetItemFilter extends KalturaCaptionAssetFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_caption_asset_id",
		"idIn" => "_in_caption_asset_id",
		"startTimeGreaterThanOrEqual" => "_gte_start_time",
		"startTimeLessThanOrEqual" => "_lte_start_time",
		"endTimeGreaterThanOrEqual" => "_gte_end_time",
		"endTimeLessThanOrEqual" => "_lte_end_time",
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

	static private $order_by_map = array
	(
		"+startTime" => "+start_time",
		"-startTime" => "-start_time",
		"+endTime" => "+end_time",
		"-endTime" => "-end_time",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), self::$order_by_map);
	}
	
	protected function validateEntryIdFiltered()
	{
		// do nothing, just overwrite parent validations
	}

	/* (non-PHPdoc)
	 * @see KalturaAssetFilter::getTypeListResponse()
	 */
	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, array $types = null)
	{
		$captionAssetItemFilter = new CaptionAssetItemFilter();
		$this->toObject($captionAssetItemFilter);

		$c = KalturaCriteria::create(CaptionAssetItemPeer::OM_CLASS);
		if($pager)
			$pager->attachToCriteria($c);

		$captionAssetItemFilter->attachToCriteria($c);
		$list = CaptionAssetItemPeer::doSelect($c);

		$response = new KalturaCaptionAssetItemListResponse();
		$response->objects = KalturaCaptionAssetItemArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $c->getRecordsCount();
		return $response;
	}
	
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