<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaUserEntryBaseFilter extends KalturaFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"idNotIn" => "_notin_id",
		"entryIdEqual" => "_eq_entry_id",
		"entryIdIn" => "_in_entry_id",
		"entryIdNotIn" => "_notin_entry_id",
		"userIdEqual" => "_eq_user_id",
		"userIdIn" => "_in_user_id",
		"userIdNotIn" => "_notin_user_id",
		"statusEqual" => "_eq_status",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"typeEqual" => "_eq_type",
	);

	static private $order_by_map = array
	(
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

	/**
	 * @var int
	 */
	public $idEqual;

	/**
	 * @var string
	 */
	public $idIn;

	/**
	 * @var string
	 */
	public $idNotIn;

	/**
	 * @var string
	 */
	public $entryIdEqual;

	/**
	 * @var string
	 */
	public $entryIdIn;

	/**
	 * @var string
	 */
	public $entryIdNotIn;

	/**
	 * @var string
	 */
	public $userIdEqual;

	/**
	 * @var string
	 */
	public $userIdIn;

	/**
	 * @var string
	 */
	public $userIdNotIn;

	/**
	 * @var KalturaUserEntryStatus
	 */
	public $statusEqual;

	/**
	 * @var time
	 */
	public $createdAtLessThanOrEqual;

	/**
	 * @var time
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtLessThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtGreaterThanOrEqual;

	/**
	 * @var KalturaUserEntryType
	 */
	public $typeEqual;

//TODO: check if this should be static and shared with KalturaBaseEntryFilter
	private function preparePusersToKusersFilter( $puserIdsCsv )
	{
		$kuserIdsArr = array();
		$puserIdsArr = explode(',',$puserIdsCsv);
		$kuserArr = kuserPeer::getKuserByPartnerAndUids(kCurrentContext::getCurrentPartnerId(), $puserIdsArr);

		foreach($kuserArr as $kuser)
		{
			$kuserIdsArr[] = $kuser->getId();
		}

		if(!empty($kuserIdsArr))
		{
			return implode(',',$kuserIdsArr);
		}

		return -1; // no result will be returned if no puser exists
	}
	
	/**
	 * The user_id is infact a puser_id and the kuser_id should be retrieved
	 */
	protected function fixFilterUserId()
	{
		if ($this->userIdEqual !== null)
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $this->userIdEqual);
			if ($kuser)
				$this->userIdEqual = $kuser->getId();
			else
				$this->userIdEqual = -1; // no result will be returned when the user is missing
		}

		if(!empty($this->userIdIn))
		{
			$this->userIdIn = $this->preparePusersToKusersFilter( $this->userIdIn );
		}

	}
}
