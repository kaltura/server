<?php
/**
 * @package plugins.drm
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaDrmProfileBaseFilter extends KalturaFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"partnerIdEqual" => "_eq_partner_id",
		"partnerIdIn" => "_in_partner_id",
		"nameLike" => "_like_name",
		"providerEqual" => "_eq_provider",
		"providerIn" => "_in_provider",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"licenseServerUrlEqual" => "_eq_license_server_url",
		"licenseServerUrlIn" => "_in_license_server_url",
	);

	static private $order_by_map = array
	(
		"+id" => "+id",
		"-id" => "-id",
		"+name" => "+name",
		"-name" => "-name",
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
	 * @var int
	 */
	public $partnerIdEqual;

	/**
	 * @var string
	 */
	public $partnerIdIn;

	/**
	 * @var string
	 */
	public $nameLike;

	/**
	 * @var KalturaDrmProviderType
	 */
	public $providerEqual;

	/**
	 * @dynamicType KalturaDrmProviderType
	 * @var string
	 */
	public $providerIn;

	/**
	 * @var KalturaDrmProfileStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var string
	 */
	public $licenseServerUrlEqual;

	/**
	 * @var string
	 */
	public $licenseServerUrlIn;
}
