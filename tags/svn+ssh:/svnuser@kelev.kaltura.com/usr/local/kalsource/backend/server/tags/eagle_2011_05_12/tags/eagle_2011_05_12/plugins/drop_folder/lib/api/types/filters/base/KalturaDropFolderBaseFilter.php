<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.filters.base
 * @abstract
 */
class KalturaDropFolderBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"partnerIdEqual" => "_eq_partner_id",
		"partnerIdIn" => "_in_partner_id",
		"nameLike" => "_like_name",
		"typeEqual" => "_eq_type",
		"typeIn" => "_in_type",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"ingestionProfileIdEqual" => "_eq_ingestion_profile_id",
		"ingestionProfileIdIn" => "_in_ingestion_profile_id",
		"dcEqual" => "_eq_dc",
		"dcIn" => "_in_dc",
		"pathLike" => "_like_path",
		"fileHandlerTypeEqual" => "_eq_file_handler_type",
		"fileHandlerTypeIn" => "_in_file_handler_type",
		"fileNamePatternsLike" => "_like_file_name_patterns",
		"fileNamePatternsMultiLikeOr" => "_mlikeor_file_name_patterns",
		"fileNamePatternsMultiLikeAnd" => "_mlikeand_file_name_patterns",
		"tagsLike" => "_like_tags",
		"tagsMultiLikeOr" => "_mlikeor_tags",
		"tagsMultiLikeAnd" => "_mlikeand_tags",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
	);

	private $order_by_map = array
	(
		"+id" => "+id",
		"-id" => "-id",
		"+name" => "+name",
		"-name" => "-name",
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
	 * @var string
	 */
	public $nameLike;

	/**
	 * 
	 * 
	 * @var KalturaDropFolderType
	 */
	public $typeEqual;

	/**
	 * 
	 * 
	 * @dynamicType KalturaDropFolderType
	 * @var string
	 */
	public $typeIn;

	/**
	 * 
	 * 
	 * @var KalturaDropFolderStatus
	 */
	public $statusEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $statusIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $ingestionProfileIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $ingestionProfileIdIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $dcEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $dcIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $pathLike;

	/**
	 * 
	 * 
	 * @var KalturaDropFolderFileHandlerType
	 */
	public $fileHandlerTypeEqual;

	/**
	 * 
	 * 
	 * @dynamicType KalturaDropFolderFileHandlerType
	 * @var string
	 */
	public $fileHandlerTypeIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $fileNamePatternsLike;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $fileNamePatternsMultiLikeOr;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $fileNamePatternsMultiLikeAnd;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $tagsLike;

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
}
