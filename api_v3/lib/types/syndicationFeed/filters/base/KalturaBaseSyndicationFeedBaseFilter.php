<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaBaseSyndicationFeedBaseFilter extends KalturaFilter
{
	/**
	 * This filter should be in use for retrieving only a specific syndication (identified by its syndication id).
	 * @var string
	 */
	public $idEqual;

	/**
	 * This filter should be in use for retrieving specific syndication (string should include comma separated list of
	 * syndication strings).
	 * @var string
	 */
	public $idIn;

	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id"
	);

	static private $order_by_map = array
	(
		"+playlistId" => "+playlist_id",
		"-playlistId" => "-playlist_id",
		"+name" => "+name",
		"-name" => "-name",
		"+type" => "+type",
		"-type" => "-type",
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), self::$order_by_map);
	}
}
