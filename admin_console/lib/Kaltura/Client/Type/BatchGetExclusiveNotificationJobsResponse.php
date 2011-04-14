<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_BatchGetExclusiveNotificationJobsResponse extends Kaltura_Client_ObjectBase
{
	public function getKalturaObjectType()
	{
		return 'KalturaBatchGetExclusiveNotificationJobsResponse';
	}
	
	/**
	 * 
	 *
	 * @var array of KalturaNotification
	 * @readonly
	 */
	public $notifications;

	/**
	 * 
	 *
	 * @var array of KalturaPartner
	 * @readonly
	 */
	public $partners;


}

