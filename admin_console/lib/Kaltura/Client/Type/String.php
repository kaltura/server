<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_String extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaString';
	}
	
	/**
	 * 
	 *
	 * @var string
	 */
	public $value = null;


}

