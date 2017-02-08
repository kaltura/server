<?php
/**
 * Push Notification Template service that allows registration for a push queue 
 *
 * @service pushNotificationTemplate
 * @package plugins.pushNotification
 * @subpackage api.services
 */
class PushNotificationTemplateService extends KalturaBaseService
{	
	private function buildResult($partnerId, $queueName, $queueKey, $postProccesor)
	{
		$hash = kCurrentContext::$ks_object->getHash();
		$protocol = infraRequestUtils::getProtocol();
		$host = kConf::get("push_server_host");
		$secret = kConf::get("push_server_secret");
		$token = base64_encode($partnerId . ":" . $postProccesor->encode($secret . ":" . $hash . ":" . uniqid()));
		$postProccesor->setHash($hash);
		
		$postProccesor->setHash($hash);
		$result = new KalturaPushNotificationData();
		$result->queueName = $postProccesor->encode($queueName . ":" . $hash);
		$result->queueKey = $queueKey;
		$result->url = $protocol . "://" . $host . "/?p=" . $partnerId . "&x=" . urlencode($token);
		
		return $result;
	}

	/**
	 * Register to a queue from which event messages will be provided according to given template. Queue will be created if not already exists
	 *
	 * @action register
	 * @actionAlias eventNotification_eventNotificationTemplate.register
	 * @param string $notificationTemplateSystemName Existing push notification template system name
	 * @param KalturaPushNotificationParams $pushNotificationParams
	 * @return KalturaPushNotificationData
	 */
	function registerAction($notificationTemplateSystemName, $pushNotificationParams)
	{		
		// find the template, according to its system name, on both current partner and partner 0
		$partnerId = $this->getPartnerId();
		$partnersIds = array(PartnerPeer::GLOBAL_PARTNER, $partnerId);

		$dbEventNotificationTemplate = EventNotificationTemplatePeer::retrieveBySystemName($notificationTemplateSystemName, null, $partnersIds);
		if (!$dbEventNotificationTemplate)
			throw new KalturaAPIException(KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_SYSTEM_NAME_NOT_FOUND, $notificationTemplateSystemName);

		// verify template is push typed
		if (!$dbEventNotificationTemplate instanceof PushNotificationTemplate)
			throw new KalturaAPIException(KalturaEventNotificationErrors::EVENT_NOTIFICATION_WRONG_TYPE, $notificationTemplateSystemName, get_class($dbEventNotificationTemplate));
		
		$postProccesor = new registerNotificationPostProcessor();
		kApiCache::setResponsePostProccesor($postProccesor);

		/* @var $kPushNotificationParams kPushNotificationParams */
		$missingParams = array();
		$userContnetParams = array();
		$userQueueKeyParams = array();
		$userParamsArrayKeys = array();
		$userParamsArray = $pushNotificationParams->toObject()->getUserParams();
		
		// get template configured params
		$contnetParams = $dbEventNotificationTemplate->getContentParametersKeyValueArray();
		$queueKeyParams = $dbEventNotificationTemplate->getQueueKeyParametersKeyValueArray();
		$templateParams = array_merge($contnetParams, $queueKeyParams);
		
		foreach ($userParamsArray as $userParam)
		{
			$userParamKey = $userParam->getKey();
			array_push($userParamsArrayKeys, $userParamKey);
			
			if(isset($contnetParams[$userParamKey]))
			{
				$userContnetParams[] = $userParam;
				continue;
			}
			
			if(isset($queueKeyParams[$userParamKey]))
			{
				$valueToken = $queueKeyParams[$userParamKey]->getQueueKeyToken();
				if($valueToken)
				{
					$userParam->setValue(new kStringValue($valueToken));
					$postProccesor->addToken($userParamKey, $valueToken);
				}
				$userQueueKeyParams[] = $userParam;
			}
		}
		
		foreach ($templateParams as $templateParamKey => $templateParamValue)
		{
			if (!in_array($templateParamKey, $userParamsArrayKeys))
				array_push($missingParams, $templateParamKey);
		}
		
		if ($missingParams != null)
			throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, implode(",", $missingParams));
		
		$queueName = $dbEventNotificationTemplate->getQueueName($userContnetParams, $partnerId, null);
		$queueKey = $dbEventNotificationTemplate->getQueueKey(array_merge($userContnetParams, $userQueueKeyParams), $partnerId, null, true);
		
		return $this->buildResult($partnerId, $queueName, $queueKey, $postProccesor);
	}
	
	/**
	 * Clear queue messages 
	 *
	 * @action sendCommand
	 * @actionAlias eventNotification_eventNotificationTemplate.sendComman
	 * @param string $queueName QueueNAme to clear messages for
	 * @param KalturaPushNotificationCommandType $command Command to be sent to push server
	 * @return bool Allwyas true
	 */
	function sendCommandAction($queueName, KalturaPushNotificationCommandType $command)
	{
		$time = time();
		$msg = json_encode(array(
				"data" 		=> null,
				"queueKey" 	=> null,
				"queueName"	=> $queueName,
				"msgId"		=> md5("$queueName $time"),
				"msgTime"	=> $time,
				"command"	=> $command
		));
		
		// get instance of activated queue proivder and send message
		$queueProvider = QueueProvider::getInstance();
		$queueProvider->send('', $msg);
		
		return true;
	}
}