<?php
/**
 * @package plugins.sso
 * @relatedService SsoService
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaSsoBaseFilter extends KalturaRelatedFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"applicationTypeEqual" => "_eq_application_type",
		"partnerIdEqual" => "_eq_partner_id",
		"domainEqual" => "_eq_domain",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"redirectUrlEqual" => "_eq_redirect_url",
	);

	static private $order_by_map = array
	(
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
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
	public $applicationTypeEqual;

	/**
	 * @var int
	 */
	public $partnerIdEqual;

	/**
	 * @var string
	 */
	public $domainEqual;

	/**
	 * @var KalturaSsoStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var time
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $createdAtLessThanOrEqual;

	/**
	 * @var string
	 */
	public $redirectUrlEqual;
}
