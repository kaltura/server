<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPartnerStatistics extends KalturaObject
{
	/**
	 * Package total allowed bandwidth and storage
	 * 
	 * @var int
	 * @readonly
	 */
	public $packageBandwidthAndStorage;
	
	/**
	 * Partner total hosting in GB on the disk
	 * 
	 * @var float
	 * @readonly
	 */
	public $hosting;
	
	/**
	 * Partner total bandwidth in GB
	 * 
	 * @var float
	 * @readonly
	 */
	public $bandwidth;

	/**
	 * total usage in GB - including bandwidth and storage
	 * 
	 * @var int
	 * @readonly
	 */
	public $usage;
	
	/**
	 * Percent of usage out of partner's package. if usage is 5GB and package is 10GB, this value will be 50
	 * 
	 * @var float
	 * @readonly
	 */
	public $usagePercent;

	/**
	 * date when partner reached the limit of his package (timestamp)
	 * 
	 * @var int
	 * @readonly
	 */
	public $reachedLimitDate;
}