<?php
/**
 * @package plugins.pushNotification
 * @subpackage model
 */
class registerNotificationPostProcessor
{
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
	
	public function processResponse(&$response)
	{
		$requestParams = infraRequestUtils::getRequestParams();
		$this->updateResponseQueueKey($response, $requestParams);
		$this->updateResponseUrl($response, $requestParams);
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
		
		if(preg_match('/(pn_.*?)md5s_(.*?)_md5e/', $response, $matches))
		{
			$queueKeyToReplace = $matches[0];
			$queuKeyPrefix = $matches[1];
			$md5String = md5($matches[2]);
				
			$encodedQueueKey = $this->encode($queuKeyPrefix.$md5String . ":" . $this->hash);
			$response = str_replace($queueKeyToReplace, $encodedQueueKey, $response);
		}
	}
	
	public function updateResponseUrl(&$response, $requestParams)
	{		
		$ksObject = $this->getKsObject($requestParams);
		
		$urlData = json_encode(array_merge($this->getBasicUrlData(), $this->getKsUrlData($ksObject)));
		
		$urlData = urlencode(base64_encode("$ksPartnerId:" . $this->encode($urlData)));
		
		$response = str_replace("{urlData}", $urlData, $response);
	}
	
	public function buildParamsKeyValueArrayFromRawParams()
	{
		$paramsKeyValue = array();
		
		foreach ($params as $key => $value)
		{
			preg_match('/(pushNotificationParams:userParams:item\\d:).*key/', $key, $matches);
			if(count($matches))
			{
				$resKey = $matches[1];
				$resKey = $resKey . "value:value";
				$resValue = $params[$resKey];
				$paramsKeyValue[$value] = $resValue;
			}
		}

		return $paramsKeyValue;
	}
	
	public function getKsObject($requestParams)
	{
		if(isset($requestParams['ks']))
		{
			$ksObj = new kSessionBase();
			$parseResult = $ksObj->parseKS($requestParams['ks']);
			$ksStatus = $ksObj->tryToValidateKS();
			if($parseResult && $ksStatus == kSessionBase::OK)
				return $ksObj;
		}
		
		return null;
	}
	
	public function addToken($key, $token)
	{
		$this->tokensToReplace[$key] = $token;
	}
	
	public function setHash($hash)
	{
		$this->hash = $hash;
	}
	
	private function getBasicUrlData()
	{
		return array(
				"secret"	=> kConf::get("push_server_secret"),
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
				"ksPrivileges"	=> $ksObject->getParsedPrivileges(), 
				"ksExpiry" 		=> $ksObject->valid_until
		);
	}
	
	private function buildCachableResponse($partnerId, $queueName, $queueKey)
	{
		$result = new KalturaPushNotificationData();
		$result->queueName = $this->encode($queueName . ":" . $hash);
		$result->queueKey = $queueKey;
		$result->url = infraRequestUtils::getProtocol() . "://" . kConf::get("push_server_host") . "/?p=" . $partnerId . "&x={urlData}";;
		
		return $result;
	}
	
	private function buildUnCachableResponse($partnerId, $queueName, $queueKey)
	{		
		$urlData = json_encode(array_merge($this->getBasicUrlData(), $this->getKsUrlData(kCurrentContext::$ks_object)));
		$urlData = urlencode(base64_encode("$ksPartnerId:" . self::encode($urlData)));
		
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
			return $this->buildCachableResponse();
		else 
			return $this->buildUnCachableResponse();
	}
}