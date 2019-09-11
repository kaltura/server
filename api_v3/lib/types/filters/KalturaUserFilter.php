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

	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new kuserFilter();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaFilter::toObject()
	 */
	public function toObject ( $object_to_fill = null, $props_to_skip = array() )
	{
		$object_to_fill =  parent::toObject($object_to_fill, $props_to_skip);
		
		if (!is_null($this->loginEnabledEqual)) {
			if ($this->loginEnabledEqual === true)
				$object_to_fill->set('_gt_login_data_id', 0);
				
			if ($this->loginEnabledEqual === false)
				$object_to_fill->set('_ltornull_login_data_id', 0);
		}
		
		return $object_to_fill;		
	}
	
	public function doFromObject($source_object, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($source_object, $responseProfile);
		
		$loginDataIdGreaterOrEqualValue =  $source_object->get('_gt_login_data_id');
		$loginDataIdLessThanOrNullValue =  $source_object->get('_ltornull_login_data_id');
		
		if ($loginDataIdGreaterOrEqualValue === 0) {
			$this->loginEnabledEqual = true;
		}
		else if ($loginDataIdLessThanOrNullValue === 0) {
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

	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$userFilter = $this->toObject();

		$c = KalturaCriteria::create(kuserPeer::OM_CLASS);
		$userFilter->attachToCriteria($c);
		
		if (!is_null($this->roleIdEqual))
		{
			$roleCriteria = new Criteria();
			$roleCriteria->add ( KuserToUserRolePeer::USER_ROLE_ID , $this->roleIdEqual );
			$roleCriteria->addSelectColumn(KuserToUserRolePeer::KUSER_ID);
			$rs = KuserToUserRolePeer::doSelectStmt($roleCriteria);
			$kuserIds = $rs->fetchAll(PDO::FETCH_COLUMN);
						
			$c->add(kuserPeer::ID, $kuserIds, KalturaCriteria::IN);
		}

		$c->addAnd(kuserPeer::PUSER_ID, NULL, KalturaCriteria::ISNOTNULL);
		
		$pager->attachToCriteria($c);
		$list = kuserPeer::doSelect($c);
		
		$totalCount = $c->getRecordsCount();

		$newList = KalturaUserArray::fromDbArray($list, $responseProfile);
		$response = new KalturaUserListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		
		return $response;
	}
}
