<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_WorkerQueueFilter extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaWorkerQueueFilter';
	}
	
	/**
	 * 
	 *
	 * @var int
	 */
	public $schedulerId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $workerId = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_BatchJobType
	 */
	public $jobType = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Type_BatchJobFilter
	 */
	public $filter;


}

