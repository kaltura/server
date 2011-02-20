<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
class KalturaUiConfBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"nameLike" => "_like_name",
		"partnerIdEqual" => "_eq_partner_id",
		"partnerIdIn" => "_in_partner_id",
		"objTypeEqual" => "_eq_obj_type",
		"tagsMultiLikeOr" => "_mlikeor_tags",
		"tagsMultiLikeAnd" => "_mlikeand_tags",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"creationModeEqual" => "_eq_creation_mode",
		"creationModeIn" => "_in_creation_mode",
	);

	private $order_by_map = array
	(
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
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
	 * @var int
	 */
	public $idEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $idIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $nameLike;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $partnerIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $partnerIdIn;

	/**
	 * 
	 * 
	 * @var KalturaUiConfObjType
	 */
	public $objTypeEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $tagsMultiLikeOr;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $tagsMultiLikeAnd;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $createdAtLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $updatedAtLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var KalturaUiConfCreationMode
	 */
	public $creationModeEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $creationModeIn;
}
