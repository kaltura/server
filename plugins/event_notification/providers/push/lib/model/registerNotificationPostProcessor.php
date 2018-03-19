<?php
/**
 * @package plugins.pushNotification
 * @subpackage model
 */
class registerNotificationPostProcessor
{
	const QUEUE_NAME_PREFIX = "qns_";
	const QUEUE_NAME_POSTFIX = "_qne";
	const QUEUE_KEY_PREFIX = "qks_";
	const QUEUE_KEY_POSTFIX = "_qke";
	const CONTENT_PARAMS_PREFIX = "s_cp_";
	const CONTENT_PARAMS_POSTFIX = "_e_cp";
	const QUEUE_PREFIX = "pn_";
	
	/*
	 * @var array 
	 * search by key if exists in the request params and replace the toekn value with the request parsms value 
	 */
	public $tokensToReplace = array();
	
	/*
	 * @var array
	 * Hash to sign queue key with
	 */
	public $hash;
	
	public function addToken($key, $token)
	{
		$this->tokensToReplace[$key] = $token;
	}
	
	public function setHash($hash)
	{
		$this->hash = $hash;
	}
	
	private function encode($data)
	{
		$secret = kConf::get("push_server_secret");
		$iv = kConf::get("push_server_secret_iv");
	
		$cipherData = KCryptoWrapper::encrypt_aes($data, $secret, $iv); 
	
		return bin2hex($cipherData);
	}
	
	public function processResponse(&$response)
	{
		try
		{
			$requestParams = infraRequestUtils::getRequestParams();
			$ksObject = $this->getKsObject($requestParams);
			if(!$ksObject)
				throw new Exception('Failed to get KS object');
			
			$this->setHash($ksObject->getHash());
			$this->updateResponseQueueName($response);
			$this->updateResponseQueueKey($response, $requestParams);
			$this->updateResponseUrl($response, $requestParams, $ksObject);
		}
		catch(Exception $e)
		{
			return false;
		}
		
		return true;
	}
	
	public function updateResponseQueueKey(&$response, $requestParams)
	{
		$params = $this->buildParamsKeyValueArrayFromRawParams($requestParams);
		
		foreach ($this->tokensToReplace as $key => $value)
		{
			if(isset($params[$key]))
			{
				$response = str_replace($value, $params[$key], $response);
			}
		}
		
		$matchString = '/' . self::QUEUE_KEY_PREFIX . '(' . self::QUEUE_PREFIX . '.*?)' .
					 self::CONTENT_PARAMS_PREFIX . '(.*?)' . self::CONTENT_PARAMS_POSTFIX . self::QUEUE_KEY_POSTFIX . '/';
		if(preg_match_all($matchString, $response, $matches))
		{
			$queueKeysToReplace = $matches[0];
			$queuKeysPrefixes = $matches[1];
			$md5Strings = $matches[2];
			
			foreach ($queueKeysToReplace as $key => $queueKeyToReplace)
			{
				$queuKeyPrefix = $queuKeysPrefixes[$key];
				$md5String = md5($md5Strings[$key]);
				
				$encodedQueueKey = $this->encode($queuKeyPrefix.$md5String . ":" . $this->hash);
				$response = str_replace($queueKeyToReplace, $encodedQueueKey, $response);
			}
		}
	}
	
	public function updateResponseQueueName(&$response)
	{
		$matchString = '/' . self::QUEUE_NAME_PREFIX . '(.*?)' . self::QUEUE_NAME_POSTFIX .'/';
		if(preg_match_all($matchString, $response, $matches))
		{
			$queueNameToReplaceMatches = $matches[0];
			$queueNames = $matches[1];
			
			foreach ($queueNameToReplaceMatches as $key => $value)
			{
				$encodedQueueName = $this->encode($queueNames[$key] . ":" . $this->hash);
				$response = str_replace($value, $encodedQueueName, $response);
			}
		}
	}
	
	public function updateResponseUrl(&$response, $requestParams, kSessionBase $ksObject)
	{
		$urlData = json_encode(array_merge($this->getBasicUrlData(), $this->getKsUrlData($ksObject)));
		$urlData = urlencode(base64_encode($ksObject->getPartnerId() . ":" . $this->encode($urlData)));		
		$response = str_replace("{urlData}", $urlData, $response);
	}
	
	public function buildParamsKeyValueArrayFromRawParams($requestParams)
	{
		$paramsKeyValue = array();
		
		foreach ($requestParams as $key => $value)
		{
			preg_match('/(\\d?:?pushNotificationParams:userParams:item\\d:).*key/', $key, $matches);
			if(count($matches))
			{
				$resKey = $matches[1];
				$resKey = $resKey . "value:value";
				$resValue = $requestParams[$resKey];
				$paramsKeyValue[$value] = $resValue;
			}
		}

		return $paramsKeyValue;
	}
	
	public function getKsObject($requestParams)
	{
		$ks = $this->getRequestParamsKs($requestParams);
		if(!$ks)
			return null;
		
		$ksObj = new kSessionBase();
		$parseResult = $ksObj->parseKS($ks);
		$ksStatus = $ksObj->tryToValidateKS();
		if($parseResult && $ksStatus == kSessionBase::OK)
			return $ksObj;
		
		return null;
	}
	
	private function getRequestParamsKs($requestParams)
	{
		if(isset($requestParams['originalKs']))
			return $requestParams['originalKs'];
		
		if(isset($requestParams['ks']))
			return $requestParams['ks'];
		
		if(isset($requestParams['service']) && $requestParams['service'] === "multirequest" && isset($requestParams['1:ks']))
			return $requestParams['1:ks'];
		
		return null;
	}
	
	private function getBasicUrlData()
	{
		return array(
				"key"	=> kConf::get("push_server_secret"),
				"ip"		=> infraRequestUtils::getRemoteAddress(),
				"hash"		=> $this->hash,
        );
	}
	
	private function getKsUrlData(kSessionBase $ksObject = null)
	{
		if(!$ksObject)
			return array();
		
		return array(
				"ksPartnerId"	=> $ksObject->partner_id, 
				"ksUserId" 		=> $ksObject->user,
				"ksPrivileges"	=> $ksObject->getPrivileges(), 
				"ksExpiry" 		=> $ksObject->valid_until
		);
	}
	
	private function buildCachableResponse($partnerId, $queueName, $queueKey)
	{
		$result = new KalturaPushNotificationData();
		$result->queueName = self::QUEUE_NAME_PREFIX . $queueName . self::QUEUE_NAME_POSTFIX;
		$result->queueKey = self::QUEUE_KEY_PREFIX . $queueKey . self::QUEUE_KEY_POSTFIX;
		$result->url = infraRequestUtils::getProtocol() . "://" . kConf::get("push_server_host") . "/?p=" . $partnerId . "&x={urlData}";
		
		return $result;
	}
	
	private function buildUnCachableResponse($partnerId, $queueName, $queueKey)
	{		
		$urlData = json_encode(array_merge($this->getBasicUrlData(), $this->getKsUrlData(kCurrentContext::$ks_object)));
		$urlData = urlencode(base64_encode("$partnerId:" . self::encode($urlData)));
		
		$result = new KalturaPushNotificationData();
		$result->queueName = $this->encode($queueName . ":" . $this->hash);
		$result->queueKey = $this->encode($queueKey . ":" . $this->hash);
		$result->url = infraRequestUtils::getProtocol() . "://" . kConf::get("push_server_host") . "/?p=" . $partnerId . "&x=$urlData";
		
		return $result;
	}
	
	public function buildResponse($partnerId, $queueName, $queueKey)
	{
		$this->setHash(kCurrentContext::$ks_object->getHash());
		
		if(kApiCache::getEnableResponsePostProcessor())
			return $this->buildCachableResponse($partnerId, $queueName, $queueKey);
		else 
			return $this->buildUnCachableResponse($partnerId, $queueName, $queueKey);
	}
}
