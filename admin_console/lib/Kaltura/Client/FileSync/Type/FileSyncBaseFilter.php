<?php
/**
 * @package Admin
 * @subpackage Client
 */
abstract class Kaltura_Client_FileSync_Type_FileSyncBaseFilter extends Kaltura_Client_Type_Filter
{
	public function getKalturaObjectType()
	{
		return 'KalturaFileSyncBaseFilter';
	}
	
	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_FileSyncObjectType
	 */
	public $fileObjectTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileObjectTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $objectIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $objectIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $versionEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $versionIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $objectSubTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $objectSubTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $dcEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $dcIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $originalEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $updatedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $readyAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $readyAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $syncTimeGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $syncTimeLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_FileSync_Enum_FileSyncStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_FileSync_Enum_FileSyncType
	 */
	public $fileTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileTypeIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $linkedIdEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $linkCountGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $linkCountLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $fileSizeGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $fileSizeLessThanOrEqual = null;


}

