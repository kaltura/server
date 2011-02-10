<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBatchJobResponse extends KalturaObject 
{
	/**
	 * The main batch job
	 * 
	 * @var KalturaBatchJob
	 */
	public $batchJob;
	
	
	/**
	 * All batch jobs that reference the main job as root
	 * 
	 * @var KalturaBatchJobArray
	 */
	public $childBatchJobs;
}