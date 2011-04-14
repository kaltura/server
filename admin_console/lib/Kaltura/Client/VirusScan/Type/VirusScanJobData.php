<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_VirusScan_Type_VirusScanJobData extends Kaltura_Client_Type_JobData
{
	public function getKalturaObjectType()
	{
		return 'KalturaVirusScanJobData';
	}
	
	/**
	 * 
	 *
	 * @var string
	 */
	public $srcFilePath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $flavorAssetId = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_VirusScan_Enum_VirusScanJobResult
	 */
	public $scanResult = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_VirusScan_Enum_VirusFoundAction
	 */
	public $virusFoundAction = null;


}

