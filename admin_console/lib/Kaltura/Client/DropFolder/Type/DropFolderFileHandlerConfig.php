<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_DropFolder_Type_DropFolderFileHandlerConfig extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaDropFolderFileHandlerConfig';
	}
	
	/**
	 * 
	 *
	 * @var Kaltura_Client_DropFolder_Enum_DropFolderFileHandlerType
	 */
	public $handlerType = null;


}

