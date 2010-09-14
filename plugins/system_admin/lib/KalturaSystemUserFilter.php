<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaSystemUserFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
	);

	private $order_by_map = array
	(
		"+id" => "+id",
		"-id" => "-id",
		"+status" => "+status",
		"-status" => "-status",
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
