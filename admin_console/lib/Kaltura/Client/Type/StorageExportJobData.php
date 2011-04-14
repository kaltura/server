<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_StorageExportJobData extends Kaltura_Client_Type_StorageJobData
{
	public function getKalturaObjectType()
	{
		return 'KalturaStorageExportJobData';
	}
	
	/**
	 * 
	 *
	 * @var string
	 */
	public $destFileSyncStoredPath = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $force = null;


}

