<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_FreeJobResponse extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaFreeJobResponse';
	}
	
	/**
	 * 
	 *
	 * @var Kaltura_Client_Type_BatchJob
	 * @readonly
	 */
	public $job;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_BatchJobType
	 * @readonly
	 */
	public $jobType = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $queueSize = null;


}

