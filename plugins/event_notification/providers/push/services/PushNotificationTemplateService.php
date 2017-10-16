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
	/**
	 * Register to a queue from which event messages will be provided according to given template. Queue will be created if not already exists
	 *
	 * @action register
	 * @actionAlias eventNotification_eventNotificationTemplate.register
	 * @param string $notificationTemplateSystemName Existing push notification template system name
	 * @param KalturaPushNotificationParams $pushNotificationParams
	 * @return KalturaPushNotificationData
	 */
	function registerAction($notificationTemplateSystemName, KalturaPushNotificationParams $pushNotificationParams)
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
		
		$postProcessor = new registerNotificationPostProcessor();
		if(kApiCache::getEnableResponsePostProcessor() && !kApiCache::getResponsePostProcessor())
			kApiCache::setResponsePostProcessor($postProcessor);

		$missingParams = array();		
		$userQueueNameParams = array();
		$userQueueKeyParams = array();
		$userParamsArrayKeys = array();
		$userParamsArray = $pushNotificationParams->toObject()->getUserParams();
		
		// get template configured params
		$queueNameParams = $dbEventNotificationTemplate->getQueueNameParameters(true);
		$queueKeyParams = $dbEventNotificationTemplate->getQueueKeyParameters(true);
		
		foreach ($userParamsArray as $userParam)
		{
			$userParamKey = $userParam->getKey();
			array_push($userParamsArrayKeys, $userParamKey);
			
			if(isset($queueNameParams[$userParamKey]))
			{
				$userQueueNameParams[] = $userParam;
			}
			
			if(isset($queueKeyParams[$userParamKey]))
			{
				$valueToken = $queueKeyParams[$userParamKey]->getQueueKeyToken();
				if($valueToken && kApiCache::getEnableResponsePostProcessor())
				{
					$userParam->setValue(new kStringValue($valueToken));
					$postProcessor->addToken($userParamKey, $valueToken);
				}
				$userQueueKeyParams[] = $userParam;
			}
		}
		
		foreach ($queueKeyParams as $keyParam => $keyValue)
		{
			if (!in_array($keyParam, $userParamsArrayKeys))
				array_push($missingParams, $keyParam);
		}
		
		if (!empty($missingParams))
			throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, implode(",", $missingParams));
		
		$queueName = $dbEventNotificationTemplate->getQueueName($userQueueNameParams, $partnerId, null);
		$queueKey = $dbEventNotificationTemplate->getQueueKey($userQueueKeyParams, $partnerId, null, kApiCache::getEnableResponsePostProcessor());
		
		return $postProcessor->buildResponse($partnerId, $queueName, $queueKey);
	}
	
	/**
	 * Clear queue messages 
	 *
	 * @action sendCommand
	 * @actionAlias eventNotification_eventNotificationTemplate.sendCommand
	 * @param string $notificationTemplateSystemName Existing push notification template system name
	 * @param KalturaPushNotificationParams $pushNotificationParams
	 * @param KalturaPushNotificationCommandType $command Command to be sent to push server
	 */
	function sendCommandAction($notificationTemplateSystemName, KalturaPushNotificationParams $pushNotificationParams, $command)
	{
		kApiCache::disableCache();
		// find the template, according to its system name, on both current partner and partner 0
		$partnerId = $this->getPartnerId();
		$partnersIds = array(PartnerPeer::GLOBAL_PARTNER, $partnerId);
		
		$dbEventNotificationTemplate = EventNotificationTemplatePeer::retrieveBySystemName($notificationTemplateSystemName, null, $partnersIds);
		if (!$dbEventNotificationTemplate)
			throw new KalturaAPIException(KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_SYSTEM_NAME_NOT_FOUND, $notificationTemplateSystemName);
		
		// verify template is push typed
		if (!$dbEventNotificationTemplate instanceof PushNotificationTemplate)
			throw new KalturaAPIException(KalturaEventNotificationErrors::EVENT_NOTIFICATION_WRONG_TYPE, $notificationTemplateSystemName, get_class($dbEventNotificationTemplate));
		
		$missingParams = array();
		$queueNameParams = array();
		$userParamsArrayKeys = array();
		$userParamsArray = $pushNotificationParams->toObject()->getUserParams();
		$templateQueueNameParams = $dbEventNotificationTemplate->getQueueNameParameters(true);
		
		foreach ($userParamsArray as $userParam)
		{
			$userParamKey = $userParam->getKey();
			array_push($userParamsArrayKeys, $userParam->getKey());
			
			if(isset($templateQueueNameParams[$userParamKey]))
				$queueNameParams[$userParamKey] = $userParam;
		}
		
		foreach ($templateQueueNameParams as $templateParamKey => $templateParamValue)
		{
			if (!in_array($templateParamKey, $userParamsArrayKeys))
				array_push($missingParams, $templateParamKey);
		}
		
		if (!empty($missingParams))
			throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, implode(",", $missingParams));
		
		$queueName = $dbEventNotificationTemplate->getQueueName($queueNameParams, $partnerId, null);
		
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
	}
}