<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_BatchJobResponse extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaBatchJobResponse';
	}
	
	/**
	 * The main batch job
	 * 
	 *
	 * @var Kaltura_Client_Type_BatchJob
	 */
	public $batchJob;

	/**
	 * All batch jobs that reference the main job as root
	 * 
	 *
	 * @var array of KalturaBatchJob
	 */
	public $childBatchJobs;


}

