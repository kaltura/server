<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_ApiActionPermissionItem extends Kaltura_Client_Type_PermissionItem
{
	public function getKalturaObjectType()
	{
		return 'KalturaApiActionPermissionItem';
	}
	
	/**
	 * 
	 *
	 * @var string
	 */
	public $service = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $action = null;


}

