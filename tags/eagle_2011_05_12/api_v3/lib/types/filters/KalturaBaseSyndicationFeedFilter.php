<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaBaseSyndicationFeedFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
	);

	private $order_by_map = array
	(
		"+playlistId" => "+playlist_id",
		"-playlistId" => "-playlist_id",
		"+name" => "+name",
		"-name" => "-name",
		"+type" => "+type",
		"-type" => "-type",
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), $this->order_by_map);
	}
}
