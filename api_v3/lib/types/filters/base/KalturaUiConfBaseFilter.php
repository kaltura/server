<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaUiConfBaseFilter extends KalturaFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"nameLike" => "_like_name",
		"partnerIdEqual" => "_eq_partner_id",
		"partnerIdIn" => "_in_partner_id",
		"objTypeEqual" => "_eq_obj_type",
		"objTypeIn" => "_in_obj_type",
		"tagsMultiLikeOr" => "_mlikeor_tags",
		"tagsMultiLikeAnd" => "_mlikeand_tags",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"creationModeEqual" => "_eq_creation_mode",
		"creationModeIn" => "_in_creation_mode",
		"versionEqual" => "_eq_version",
		"versionMultiLikeOr" => "_mlikeor_version",
		"versionMultiLikeAnd" => "_mlikeand_version",
		"partnerTagsMultiLikeOr" => "_mlikeor_partner_tags",
		"partnerTagsMultiLikeAnd" => "_mlikeand_partner_tags",
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
	public $nameLike;

	/**
	 * @var int
	 */
	public $partnerIdEqual;

	/**
	 * @var string
	 */
	public $partnerIdIn;

	/**
	 * @var KalturaUiConfObjType
	 */
	public $objTypeEqual;

	/**
	 * @var string
	 */
	public $objTypeIn;

	/**
	 * @var string
	 */
	public $tagsMultiLikeOr;

	/**
	 * @var string
	 */
	public $tagsMultiLikeAnd;

	/**
	 * @var time
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $createdAtLessThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtLessThanOrEqual;

	/**
	 * @var KalturaUiConfCreationMode
	 */
	public $creationModeEqual;

	/**
	 * @var string
	 */
	public $creationModeIn;

	/**
	 * @var string
	 */
	public $versionEqual;

	/**
	 * @var string
	 */
	public $versionMultiLikeOr;

	/**
	 * @var string
	 */
	public $versionMultiLikeAnd;

	/**
	 * @var string
	 */
	public $partnerTagsMultiLikeOr;

	/**
	 * @var string
	 */
	public $partnerTagsMultiLikeAnd;
}
