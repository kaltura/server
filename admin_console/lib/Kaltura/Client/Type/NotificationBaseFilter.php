<?php
/**
 * @package Admin
 * @subpackage Client
 */
abstract class Kaltura_Client_Type_NotificationBaseFilter extends Kaltura_Client_Type_BaseJobFilter
{
	public function getKalturaObjectType()
	{
		return 'KalturaNotificationBaseFilter';
	}
	

}

