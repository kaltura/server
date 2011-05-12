<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaUserFilter extends KalturaUserBaseFilter
{
	
	private $map_between_objects = array
	(
		"idEqual" => "_eq_puser_id",
		"idIn" => "_in_puser_id",
	);

	private $order_by_map = array
	(
		"+id" => "+puser_id",
		"-id" => "-puser_id",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), $this->order_by_map);
	}
	
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		$object_to_fill =  parent::toObject($object_to_fill, $props_to_skip);
		
		if (!is_null($this->loginEnabledEqual)) {
			if ($this->loginEnabledEqual === true)
				$object_to_fill->set('_gte_login_data_id', 0);
				
			if ($this->loginEnabledEqual === false)
				$object_to_fill->set('_ltornull_login_data_id', 0);
		}
		
		return $object_to_fill;		
	}
	
	public function fromObject ( $source_object )
	{
		parent::fromObject($source_object);
		
		$loginDataIdGreaterOrEqualValue =  $source_object->get('_gte_login_data_id');
		$loginDataIdLessThanOrNullValue =  $source_object->get('_ltornull_login_data_id');
		
		if ($loginDataIdGreaterOrEqualValue == 0) {
			$this->loginEnabledEqual = true;
		}
		else if ($loginDataIdLessThanOrNullValue == 0) {
			$this->loginEnabledEqual = false;
		}				
	}
	
	

	/**
	 * @var string
	 */
	public $idEqual;

	/**
	 * @var string
	 */
	public $idIn;
	
	/**
	 * @var bool
	 */
	public $loginEnabledEqual;
	
	
}
