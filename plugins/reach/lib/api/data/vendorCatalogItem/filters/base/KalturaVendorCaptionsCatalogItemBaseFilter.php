<?php
/**
 * @package plugins.reach
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaVendorCaptionsCatalogItemBaseFilter extends KalturaVendorCatalogItemFilter
{
	static private $map_between_objects = array
	(
		"sourceLanguageEqual" => "_eq_source_language",
		"sourceLanguageIn" => "_in_source_language",
		"outputFormatEqual" => "_eq_output_format",
		"outputFormatIn" => "_in_output_format",
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
	public $sourceLanguageEqual;

	/**
	 * @var string
	 */
	public $sourceLanguageIn;

	/**
	 * @var KalturaVendorCatalogItemOutputFormat
	 */
	public $outputFormatEqual;

	/**
	 * @var string
	 */
	public $outputFormatIn;
}
