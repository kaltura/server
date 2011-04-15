<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_AdminConsole_Type_UiConfAdminListResponse extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaUiConfAdminListResponse';
	}
	
	/**
	 * 
	 *
	 * @var array of KalturaUiConfAdmin
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

