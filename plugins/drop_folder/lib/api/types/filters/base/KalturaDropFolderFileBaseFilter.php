<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.filters.base
 * @abstract
 */
class KalturaDropFolderFileBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"partnerIdEqual" => "_eq_partner_id",
		"partnerIdIn" => "_in_partner_id",
		"dropFolderIdEqual" => "_eq_drop_folder_id",
		"dropFolderIdIn" => "_in_drop_folder_id",
		"fileNameEqual" => "_eq_file_name",
		"fileNameIn" => "_in_file_name",
		"fileNameLike" => "_like_file_name",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"parsedSlugEqual" => "_eq_parsed_slug",
		"parsedSlugIn" => "_in_parsed_slug",
		"parsedSlugLike" => "_like_parsed_slug",
		"parsedFlavorEqual" => "_eq_parsed_flavor",
		"parsedFlavorIn" => "_in_parsed_flavor",
		"parsedFlavorLike" => "_like_parsed_flavor",
		"errorCodeEqual" => "_eq_error_code",
		"errorCodeIn" => "_in_error_code",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
	);

	private $order_by_map = array
	(
		"+id" => "+id",
		"-id" => "-id",
		"+fileName" => "+file_name",
		"-fileName" => "-file_name",
		"+fileSize" => "+file_size",
		"-fileSize" => "-file_size",
		"+fileSizeLastSetAt" => "+file_size_last_set_at",
		"-fileSizeLastSetAt" => "-file_size_last_set_at",
		"+parsedSlug" => "+parsed_slug",
		"-parsedSlug" => "-parsed_slug",
		"+parsedFlavor" => "+parsed_flavor",
		"-parsedFlavor" => "-parsed_flavor",
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
	 * @var int
	 */
	public $dropFolderIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $dropFolderIdIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $fileNameEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $fileNameIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $fileNameLike;

	/**
	 * 
	 * 
	 * @var KalturaDropFolderFileStatus
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
	 * @var string
	 */
	public $parsedSlugEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $parsedSlugIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $parsedSlugLike;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $parsedFlavorEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $parsedFlavorIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $parsedFlavorLike;

	/**
	 * 
	 * 
	 * @var KalturaDropFolderFileErrorCode
	 */
	public $errorCodeEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $errorCodeIn;

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
