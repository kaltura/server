<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_MultiCenters_Type_FileSyncImportJobData extends Kaltura_Client_Type_JobData
{
	public function getKalturaObjectType()
	{
		return 'KalturaFileSyncImportJobData';
	}
	
	/**
	 * 
	 *
	 * @var string
	 */
	public $sourceUrl = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $filesyncId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tmpFilePath = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $destFilePath = null;


}

