<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_UiConfTypeInfo extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaUiConfTypeInfo';
	}
	
	/**
	 * UiConf Type
	 * 
	 *
	 * @var Kaltura_Client_Enum_UiConfObjType
	 */
	public $type = null;

	/**
	 * Available versions
	 * 
	 *
	 * @var array of KalturaString
	 */
	public $versions;

	/**
	 * The direcotry this type is saved at
	 * 
	 *
	 * @var string
	 */
	public $directory = null;

	/**
	 * Filename for this UiConf type
	 * 
	 *
	 * @var string
	 */
	public $filename = null;


}

