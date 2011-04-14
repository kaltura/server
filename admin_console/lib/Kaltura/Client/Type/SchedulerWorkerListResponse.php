<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_SchedulerWorkerListResponse extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaSchedulerWorkerListResponse';
	}
	
	/**
	 * 
	 *
	 * @var array of KalturaSchedulerWorker
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

