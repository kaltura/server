<?php
/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
require_once ( "model/genericObjectWrapper.class.php" );
require_once ( "kalturaSystemAction.class.php" );

/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
class resendNotificationAction extends kalturaSystemAction
{
	/**
	 * Will investigate a single entry
	 */
	public function execute()
	{
		$this->forceSystemAuthentication();

		$notification_id = @$_REQUEST["notification_id"];
		$entry_id = @$_REQUEST["entry_id"];
		
		$not = notificationPeer::retrieveByPK($notification_id);
		if ($not)
		{
			$not->setStatus(BatchJob::BATCHJOB_STATUS_PENDING);
			$not->setNumberOfAttempts(0);
			$not->save();
		}
		
		$this->redirect ( "/system/investigate?entry_id=$entry_id" );
	}
}
?>