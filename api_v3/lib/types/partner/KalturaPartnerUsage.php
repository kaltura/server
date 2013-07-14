<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPartnerUsage extends KalturaObject
{
	/**
	 * Partner total hosting in GB on the disk
	 * 
	 * @var float
	 * @readonly
	 */
	public $hostingGB;
	
	/**
	 * percent of usage out of partner's package. if usageGB is 5 and package is 10GB, this value will be 50
	 * 
	 * @var float
	 * @readonly
	 */
	public $Percent;

	/**
	 * package total BW - actually this is usage, which represents BW+storage
	 * 
	 * @var int
	 * @readonly
	 */
	public $packageBW;

	/**
	 * total usage in GB - including bandwidth and storage
	 * 
	 * @var float
	 * @readonly
	 */
	public $usageGB;

	/**
	 * date when partner reached the limit of his package (timestamp)
	 * 
	 * @var int
	 * @readonly
	 */
	public $reachedLimitDate;
	
	/**
	 * a semi-colon separated list of comma-separated key-values to represent a usage graph.
	 * keys could be 1-12 for a year view (1,1.2;2,1.1;3,0.9;...;12,1.4;)
	 * keys could be 1-[28,29,30,31] depending on the requested month, for a daily view in a given month (1,0.4;2,0.2;...;31,0.1;)
	 * 
	 * @var string
	 * @readonly
	 */
	public $usageGraph;
	
	static private $map_between_objects = array
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