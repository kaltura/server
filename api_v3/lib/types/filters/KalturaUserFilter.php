<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaUserFilter extends KalturaUserBaseFilter
{
	
	static private $map_between_objects = array
	(
		"idOrScreenNameStartsWith" => "_likex_puser_id_or_screen_name",
		'firstNameOrLastNameStartsWith' => "_likex_first_name_or_last_name",
		"idEqual" => "_eq_puser_id",
		"idIn" => "_in_puser_id",
		"roleIdsEqual"	=> "_eq_role_ids",
		"roleIdsIn"	=>	"_in_role_ids",
		"permissionNamesMultiLikeAnd" => "_mlikeand_permission_names",
		"permissionNamesMultiLikeOr" => "_mlikeor_permission_names",
	);

	static private $order_by_map = array
	(
		"+id" => "+puser_id",
		"-id" => "-puser_id",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), self::$order_by_map);
	}
	
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		if (!$object_to_fill)
			$object_to_fill = new kuserFilter();
		
		$object_to_fill =  parent::toObject($object_to_fill, $props_to_skip);
		
		if (!is_null($this->loginEnabledEqual)) {
			if ($this->loginEnabledEqual === true)
				$object_to_fill->set('_gte_login_data_id', 0);
				
			if ($this->loginEnabledEqual === false)
				$object_to_fill->set('_ltornull_login_data_id', 0);
		}
		
		return $object_to_fill;		
	}
	
	public function fromObject($source_object, IResponseProfile $responseProfile = null)
	{
		parent::fromObject($source_object, $responseProfile);
		
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
	public $idOrScreenNameStartsWith;

	/**
	 * @var string
	 */
	public $idEqual;

	/**
	 * @var string
	 */
	public $idIn;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $loginEnabledEqual;
	
	/**
	 * @var string
	 */
	public $roleIdEqual;
	
	/**
	 * @var string
	 */
	public $roleIdsEqual;
	
	/**
	 * @var string
	 */
	public $roleIdsIn;
	
	/**
	 * @var string
	 */
	public $firstNameOrLastNameStartsWith;
	
	/**
	 * Permission names filter expression
	 * @var string
	 */
	public $permissionNamesMultiLikeOr;
	
	/**
	 * Permission names filter expression
	 * @var string
	 */
	public $permissionNamesMultiLikeAnd;
	
}
