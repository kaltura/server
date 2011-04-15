<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_AdminConsole_Type_UiConfAdmin extends Kaltura_Client_Type_UiConf
{
	public function getKalturaObjectType()
	{
		return 'KalturaUiConfAdmin';
	}
	
	/**
	 * 
	 *
	 * @var bool
	 */
	public $isPublic = null;


}

