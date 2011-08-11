<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFreeJobResponse extends KalturaObject
{
	/**
	 * @var KalturaBatchJob
	 * @readonly 
	 */
	public $job;

	/**
	 * @var KalturaBatchJobType
	 * @readonly 
	 */
    public $jobType;
    
	/**
	 * @var int
	 * @readonly 
	 */
    public $queueSize;
}

?>