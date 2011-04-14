<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_UserFilter extends Kaltura_Client_Type_UserBaseFilter
{
	public function getKalturaObjectType()
	{
		return 'KalturaUserFilter';
	}
	
	/**
	 * 
	 *
	 * @var string
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $idIn = null;

	/**
	 * 
	 *
	 * @var bool
	 */
	public $loginEnabledEqual = null;


}

