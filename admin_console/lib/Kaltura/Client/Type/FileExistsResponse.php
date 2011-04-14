<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_FileExistsResponse extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaFileExistsResponse';
	}
	
	/**
	 * Indicates if the file exists
	 * 
	 *
	 * @var bool
	 */
	public $exists = null;

	/**
	 * Indicates if the file size is right
	 * 
	 *
	 * @var bool
	 */
	public $sizeOk = null;


}

