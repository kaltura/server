<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
class KalturaConversionProfileBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"nameEqual" => "_eq_name",
		"systemNameEqual" => "_eq_system_name",
		"systemNameIn" => "_in_system_name",
		"tagsMultiLikeOr" => "_mlikeor_tags",
		"tagsMultiLikeAnd" => "_mlikeand_tags",
		"defaultEntryIdEqual" => "_eq_default_entry_id",
		"defaultEntryIdIn" => "_in_default_entry_id",
	);

	private $order_by_map = array
	(
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
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
	 * @var KalturaConversionProfileStatus
	 */
	public $statusEqual;

	/**
	 * 
	 * 
	 * @dynamicType KalturaConversionProfileStatus
	 * @var string
	 */
	public $statusIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $nameEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $systemNameEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $systemNameIn;

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
	 * @var string
	 */
	public $defaultEntryIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $defaultEntryIdIn;
}
