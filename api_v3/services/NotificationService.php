<?php

/**
 * Notification Service
 *
 * @service notification
 * @package api
 * @subpackage services
 */
class NotificationService extends KalturaBaseService 
{
	/**
	 * Return the notifications for a specific entry id and type
	 * 
	 * @action getClientNotification
	 * @param string $entryId
	 * @param KalturaNotificationType $type
	 * @return KalturaClientNotification
	 */
	function getClientNotificationAction($entryId, $type)
	{
		//$notifications = notificationPeer::retrieveByEntryIdAndType($entryId, $type);
		$notifications = BatchJobPeer::retrieveByEntryIdAndType($entryId, BatchJob::BATCHJOB_TYPE_NOTIFICATION, $type);
		
		// FIXME: throw error if not found		
		if (count($notifications) == 0)
		{
            throw new KalturaAPIException(KalturaErrors::NOTIFICATION_FOR_ENTRY_NOT_FOUND, $entryId);
		}
		
	    $notification = $notifications[0];

	    $partnerId = $this->getPartnerId();
	    
	    $nofication_config_str = null;
		list($nofity, $nofication_config_str) = myPartnerUtils::shouldNotify($partnerId);
		
		if (!$nofity)
			return new KalturaClientNotification();
			
		$nofication_config = myNotificationsConfig::getInstance($nofication_config_str);
		$nofity_send_type = $nofication_config->shouldNotify($type);
	    
	    if ($nofity_send_type != myNotificationMgr::NOTIFICATION_MGR_SEND_SYNCH && $nofity_send_type != myNotificationMgr::NOTIFICATION_MGR_SEND_BOTH)
	    	return new KalturaClientNotification();
	    
		$partner = PartnerPeer::retrieveByPK($partnerId);
		list($url, $signatureKey) = myNotificationMgr::getPartnerNotificationInfo ($partner );
		
		list($params, $rawSignature) = myNotificationMgr::prepareNotificationData($url, $signatureKey, $notification, null);
		$serializedParams = http_build_query( $params , "" , "&" );
		
		$result = new KalturaClientNotification();
		$result->url = $url;
		$result->data = $serializedParams;
		
		return $result;
	}
}