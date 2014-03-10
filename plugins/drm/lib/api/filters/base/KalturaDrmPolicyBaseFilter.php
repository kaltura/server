<?php
/**
 * @package plugins.drm
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaDrmPolicyBaseFilter extends KalturaFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"partnerIdEqual" => "_eq_partner_id",
		"partnerIdIn" => "_in_partner_id",
		"nameLike" => "_like_name",
		"systemNameLike" => "_like_system_name",
		"providerEqual" => "_eq_provider",
		"providerIn" => "_in_provider",
		"profileIdEqual" => "_eq_profile_id",
		"profileIdIn" => "_in_profile_id",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"scenarioEqual" => "_eq_scenario",
		"scenarioIn" => "_in_scenario",
	);

	static private $order_by_map = array
	(
		"+id" => "+id",
		"-id" => "-id",
		"+name" => "+name",
		"-name" => "-name",
		"+systemName" => "+system_name",
		"-systemName" => "-system_name",
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
	 * @var string
	 */
	public $systemNameLike;

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

	/**
	 * @var KalturaDrmLicenseScenario
	 */
	public $scenarioEqual;

	/**
	 * @dynamicType KalturaDrmLicenseScenario
	 * @var string
	 */
	public $scenarioIn;
}
