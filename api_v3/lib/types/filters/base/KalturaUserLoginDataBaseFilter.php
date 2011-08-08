<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
class KalturaUserLoginDataBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"loginEmailEqual" => "_eq_login_email",
	);

	private $order_by_map = array
	(
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
	 * 
	 * 
	 * @var string
	 */
	public $loginEmailEqual;
}
