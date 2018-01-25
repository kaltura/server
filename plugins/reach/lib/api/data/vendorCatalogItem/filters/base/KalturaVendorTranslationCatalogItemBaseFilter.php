<?php
/**
 * @package plugins.reach
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaVendorTranslationCatalogItemBaseFilter extends KalturaVendorCaptionsCatalogItemFilter
{
	static private $map_between_objects = array
	(
		"targetLanguageEqual" => "_eq_target_language",
		"targetLanguageIn" => "_in_target_language",
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), self::$order_by_map);
	}

	/**
	 * @var KalturaCatalogItemLanguage
	 */
	public $targetLanguageEqual;

	/**
	 * @var string
	 */
	public $targetLanguageIn;
}
