<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_SchedulerStatus extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaSchedulerStatus';
	}
	
	/**
	 * The id of the Category
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * The configured id of the scheduler
	 * 
	 *
	 * @var int
	 */
	public $schedulerConfiguredId = null;

	/**
	 * The configured id of the job worker
	 * 
	 *
	 * @var int
	 */
	public $workerConfiguredId = null;

	/**
	 * The type of the job worker.
	 * 
	 *
	 * @var Kaltura_Client_Enum_BatchJobType
	 */
	public $workerType = null;

	/**
	 * The status type
	 * 
	 *
	 * @var Kaltura_Client_Enum_SchedulerStatusType
	 */
	public $type = null;

	/**
	 * The status value
	 * 
	 *
	 * @var int
	 */
	public $value = null;

	/**
	 * The id of the scheduler
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $schedulerId = null;

	/**
	 * The id of the worker
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $workerId = null;


}

