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
    private function encode($data)
    {
        // use a 128 Rijndael encyrption algorithm with Cipher-block chaining (CBC) as mode of AES encryption
        $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        $secret = kConf::get("push_server_secret");
        $iv = kConf::get("push_server_secret_iv");
        
        // pad the rest of the block to suit Node crypto functions padding scheme (PKCS5)
        $blocksize = 16; 
        $pad = $blocksize - (strlen($data) % $blocksize);
        $data = $data . str_repeat(chr($pad), $pad);
        
        mcrypt_generic_init($cipher, $secret, $iv);
        $cipherData = mcrypt_generic($cipher, $data);
        mcrypt_generic_deinit($cipher);
        
        return bin2hex($cipherData);
    }  
    
	/**
	 * Register to a queue from which event messages will be provided according to given template. Queue will be created if not already exists
	 * 
	 * @action register
	 * @actionAlias eventNotification_eventNotificationTemplate.register
	 * @param string $notificationTemplateSystemName Existing push notification template system name
	 * @param KalturaEventNotificationParameterArray $userParamsArray User params
	 * @return KalturaPushNotificationData
	 */
	function registerAction($notificationTemplateSystemName, $userParamsArray)
	{
	    // find the template, according to its system name, on both current partner and partner 0
	    $partnerId = $this->getPartnerId();
	    $partnersIds = array ( 
	        PartnerPeer::GLOBAL_PARTNER,
	        $partnerId,
	     );
	    $dbEventNotificationTemplate = EventNotificationTemplatePeer::retrieveBySystemName($notificationTemplateSystemName, null, $partnersIds);
	    if (!$dbEventNotificationTemplate)
	        throw new KalturaAPIException(KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_SYSTEM_NAME_NOT_FOUND, $notificationTemplateSystemName);
	    
	    // verify template is push typed
	    if (!$dbEventNotificationTemplate instanceof PushNotificationTemplate)
	        throw new KalturaAPIException(KalturaEventNotificationErrors::EVENT_NOTIFICATION_WRONG_TYPE, $notificationTemplateSystemName, get_class($dbEventNotificationTemplate) );
	    
	    // Check all template needed params were actually given 
	    $missingParams = array();
	    $templateParams = $dbEventNotificationTemplate->getContentParameters();

	    // create array of all keys
	    $userParamsArrayKeys = array();
	    foreach ($userParamsArray as $userParam) {
	        array_push($userParamsArrayKeys,$userParam->toObject()->getKey());
	    }
	    
	    foreach ($templateParams as $templateParam)
	    {
	        if (!in_array($templateParam->getKey(), $userParamsArrayKeys))
	            array_push($missingParams, $templateParam->getKey());
	    }
	    
	    if ( $missingParams != null )
	        throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER,  implode(",", $missingParams ));

	    //check that keys actually have values
	    foreach ($userParamsArray as $userParam) {
	        $userParamObj = $userParam->toObject();
	        if (!$userParamObj->getValue()) {
	            KalturaLog::debug(print_r($userParamObj,true));
	            throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, "Value of ". $userParamObj->getKey() );
	        }
	    }	    
	    
	    $queueKey = $dbEventNotificationTemplate->getQueueKey($userParamsArray->toObjectsArray(), $partnerId, null);
	    $hash = kCurrentContext::$ks_object->getHash();
	    
	    // create queue if not exists 
	    if (!$dbEventNotificationTemplate->exists($queueKey))
	       $dbEventNotificationTemplate->create($queueKey);
	    
	    $result = new KalturaPushNotificationData();
	    $result->key = $this->encode($queueKey . ":" .$hash);

	    // build the url to return
	    $protocol = infraRequestUtils::getProtocol();
	    $host = kConf::get("push_server_host");
	    $secret = kConf::get("push_server_secret");
	    $ip = kCurrentContext::$user_ip;
	    $token = base64_encode($partnerId . ":" . $this->encode($secret . ":" . $ip . ":" . $hash . ":" . uniqid() ) );
	    $result->url = $protocol . "://" . $host ."/?p=" . $partnerId ."&x=" . urlencode($token);
	    
	    return $result;
	}
}
