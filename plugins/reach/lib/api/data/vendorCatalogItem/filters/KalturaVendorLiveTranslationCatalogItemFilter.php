<?php
/**
 * @package plugins.reach
 * @subpackage api.filters
 */
class KalturaVendorLiveTranslationCatalogItemFilter extends KalturaVendorLiveTranslationCatalogItemBaseFilter
{
	/**
	 * @var KalturaCatalogItemLanguage
	 */
	public $targetLanguageEqual;

	/**
	 * @var string
	 */
	public $targetLanguageIn;

	static private $map_between_objects = array
	(
		"targetLanguageEqual" => "_eq_target_language",
		"targetLanguageIn" => "_in_target_language",
	);

	public function getTypeListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null, $type = null)
	{
		if(!$type)
		{
			$type = KalturaVendorServiceFeature::LIVE_TRANSLATION;
		}
			
		return parent::getTypeListResponse($pager, $responseProfile, $type);
	}
}
