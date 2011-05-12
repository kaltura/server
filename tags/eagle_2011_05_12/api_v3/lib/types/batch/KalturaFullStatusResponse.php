<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaFullStatusResponse extends KalturaObject 
{
	/**
	 * The status of all queues on the server
	 * 
	 * @var KalturaBatchQueuesStatusArray
	 */
	public $queuesStatus;
	
	
	/**
	 * Array of all schedulers
	 * 
	 * @var KalturaSchedulerArray
	 */
	public $schedulers;
}