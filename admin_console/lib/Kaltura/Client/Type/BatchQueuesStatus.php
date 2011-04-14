<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_BatchQueuesStatus extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaBatchQueuesStatus';
	}
	
	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_BatchJobType
	 */
	public $jobType = null;

	/**
	 * The worker configured id
	 * 
	 *
	 * @var int
	 */
	public $workerId = null;

	/**
	 * The friendly name of the type
	 * 
	 *
	 * @var string
	 */
	public $typeName = null;

	/**
	 * The size of the queue
	 * 
	 *
	 * @var int
	 */
	public $size = null;

	/**
	 * The avarage wait time
	 * 
	 *
	 * @var int
	 */
	public $waitTime = null;


}

