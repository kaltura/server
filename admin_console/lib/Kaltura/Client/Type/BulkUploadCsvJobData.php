<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_BulkUploadCsvJobData extends Kaltura_Client_Type_BulkUploadJobData
{
	public function getKalturaObjectType()
	{
		return 'KalturaBulkUploadCsvJobData';
	}
	
	/**
	 * The version of the csv file
	 * 
	 *
	 * @var Kaltura_Client_BulkUpload_Enum_BulkUploadCsvVersion
	 */
	public $csvVersion = null;


}

