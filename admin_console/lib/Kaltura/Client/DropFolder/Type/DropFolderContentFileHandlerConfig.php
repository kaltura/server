<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_DropFolder_Type_DropFolderContentFileHandlerConfig extends Kaltura_Client_DropFolder_Type_DropFolderFileHandlerConfig
{
	public function getKalturaObjectType()
	{
		return 'KalturaDropFolderContentFileHandlerConfig';
	}
	
	/**
	 * 
	 *
	 * @var Kaltura_Client_DropFolder_Enum_DropFolderContentFileHandlerMatchPolicy
	 */
	public $contentMatchPolicy = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $slugRegex = null;


}

