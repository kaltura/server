<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_AdminConsole_Type_InvestigateEntryData extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaInvestigateEntryData';
	}
	
	/**
	 * 
	 *
	 * @var Kaltura_Client_Type_BaseEntry
	 * @readonly
	 */
	public $entry;

	/**
	 * 
	 *
	 * @var Kaltura_Client_FileSync_Type_FileSyncListResponse
	 * @readonly
	 */
	public $fileSyncs;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Type_BatchJobListResponse
	 * @readonly
	 */
	public $jobs;

	/**
	 * 
	 *
	 * @var array of KalturaInvestigateFlavorAssetData
	 * @readonly
	 */
	public $flavorAssets;

	/**
	 * 
	 *
	 * @var array of KalturaInvestigateThumbAssetData
	 * @readonly
	 */
	public $thumbAssets;

	/**
	 * 
	 *
	 * @var array of KalturaTrackEntry
	 * @readonly
	 */
	public $tracks;


}

