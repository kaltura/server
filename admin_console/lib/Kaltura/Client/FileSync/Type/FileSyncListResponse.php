<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_FileSync_Type_FileSyncListResponse extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaFileSyncListResponse';
	}
	
	/**
	 * 
	 *
	 * @var array of KalturaFileSync
	 * @readonly
	 */
	public $objects;

	/**
	 * 
	 *
	 * @var int
	 * @readonly
	 */
	public $totalCount = null;


}

