<?php
/**
 * @package Admin
 * @subpackage Client
 */
abstract class Kaltura_Client_Type_AdminUserBaseFilter extends Kaltura_Client_Type_UserFilter
{
	public function getKalturaObjectType()
	{
		return 'KalturaAdminUserBaseFilter';
	}
	

}

