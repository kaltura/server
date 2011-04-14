<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_ApiParameterPermissionItem extends Kaltura_Client_Type_PermissionItem
{
	public function getKalturaObjectType()
	{
		return 'KalturaApiParameterPermissionItem';
	}
	
	/**
	 * 
	 *
	 * @var string
	 */
	public $object = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $parameter = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_ApiParameterPermissionItemAction
	 */
	public $action = null;


}

