<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_PullJobData extends Kaltura_Client_Type_JobData
{
	public function getKalturaObjectType()
	{
		return 'KalturaPullJobData';
	}
	
	/**
	 * 
	 *
	 * @var string
	 */
	public $srcFileUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $destFileLocalPath = null;


}

