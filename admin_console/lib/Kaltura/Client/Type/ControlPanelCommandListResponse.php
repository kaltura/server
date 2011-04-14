<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_ControlPanelCommandListResponse extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaControlPanelCommandListResponse';
	}
	
	/**
	 * 
	 *
	 * @var array of KalturaControlPanelCommand
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

