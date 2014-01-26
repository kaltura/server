<?php
/**
 * @package plugins.drm
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaDrmDeviceBaseFilter extends KalturaFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"partnerIdEqual" => "_eq_partner_id",
		"partnerIdIn" => "_in_partner_id",
		"userIdLike" => "_like_user_id",
		"deviceIdLike" => "_like_device_id",
		"versionLike" => "_like_version",
		"providerEqual" => "_eq_provider",
		"providerIn" => "_in_provider",
		"profileIdEqual" => "_eq_profile_id",
		"profileIdIn" => "_in_profile_id",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
	);

	static private $order_by_map = array
	(
		"+id" => "+id",
		"-id" => "-id",
		"+userId" => "+user_id",
		"-userId" => "-user_id",
		"+deviceId" => "+device_id",
		"-deviceId" => "-device_id",
		"+version" => "+version",
		"-version" => "-version",
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
	public $userIdLike;

	/**
	 * @var string
	 */
	public $deviceIdLike;

	/**
	 * @var string
	 */
	public $versionLike;

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
	 * @var int
	 */
	public $profileIdEqual;

	/**
	 * @var string
	 */
	public $profileIdIn;

	/**
	 * @var KalturaDrmPolicyStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;
}
