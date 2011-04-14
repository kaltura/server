<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_StorageProfile_Type_StorageProfileListResponse extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaStorageProfileListResponse';
	}
	
	/**
	 * 
	 *
	 * @var array of KalturaStorageProfile
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

