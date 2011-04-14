<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_RemoteConvertJobData extends Kaltura_Client_Type_ConvartableJobData
{
	public function getKalturaObjectType()
	{
		return 'KalturaRemoteConvertJobData';
	}
	
	/**
	 * 
	 *
	 * @var string
	 */
	public $srcFileUrl = null;

	/**
	 * Should be set by the API
	 * 
	 *
	 * @var string
	 */
	public $destFileUrl = null;


}

