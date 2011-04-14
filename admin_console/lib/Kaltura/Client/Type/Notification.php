<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Type_Notification extends Kaltura_Client_Type_BaseJob
{
	public function getKalturaObjectType()
	{
		return 'KalturaNotification';
	}
	
	/**
	 * 
	 *
	 * @var string
	 */
	public $puserId = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_NotificationType
	 */
	public $type = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $objectId = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_NotificationStatus
	 */
	public $status = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $notificationData = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $numberOfAttempts = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $notificationResult = null;

	/**
	 * 
	 *
	 * @var Kaltura_Client_Enum_NotificationObjectType
	 */
	public $objType = null;


}

