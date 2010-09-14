<?php
class KalturaBatchQueuesStatus extends KalturaObject 
{
	/**
	 * The job type (KalturaBatchJobType or extended)
	 * 
	 * @var int
	 */
	public $jobType;
	
	/**
	 * The worker configured id
	 * 
	 * @var int
	 */
	public $workerId;
	
	/**
	 * The friendly name of the type
	 * 
	 * @var string
	 */
	public $typeName;
	
	/**
	 * The size of the queue
	 * 
	 * @var int
	 */
	public $size;
	
	/**
	 * The avarage wait time
	 * 
	 * @var int
	 */
	public $waitTime;
}