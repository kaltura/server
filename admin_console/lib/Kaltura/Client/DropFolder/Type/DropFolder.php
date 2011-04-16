<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_DropFolder_Type_DropFolder extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaDropFolder';
	}
	
	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $id = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $partnerId = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $description = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_DropFolder_Enum_DropFolderType
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_DropFolder_Enum_DropFolderStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $ingestionProfileId = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $dc = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $path = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $fileSizeCheckInterval = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_DropFolder_Enum_DropFolderFileDeletePolicy
	 */
	public $fileDeletePolicy = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $autoFileDeleteDays = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_DropFolder_Enum_DropFolderFileHandlerType
	 */
	public $fileHandlerType = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $fileNamePatterns = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_DropFolder_Type_DropFolderFileHandlerConfig
	 */
	public $fileHandlerConfig;

	/**
	 * 
	 *
	 * @var string
	 */
	public $tags = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $createdAt = null;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $updatedAt = null;


}

