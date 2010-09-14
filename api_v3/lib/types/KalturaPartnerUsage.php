<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPartnerUsage extends KalturaObject
{
	/**
	 * @var float
	 * @readonly
	 */
	public $hostingGB;
	
	/**
	 * @var float
	 * @readonly
	 */
	public $Percent;

	/**
	 * @var int
	 * @readonly
	 */
	public $packageBW;

	/**
	 * @var int
	 * @readonly
	 */
	public $usageGB;

	/**
	 * @var int
	 * @readonly
	 */
	public $reachedLimitDate;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $usageGraph;
	
	private $map_between_objects = array
	(
		"hostingGB" , "Percent" , "packageBW" => "package_bw" , "reachedLimitDate" => "reached_limit_date" ,
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function fromPartnerUsage( $source_array )
	{
	}
}