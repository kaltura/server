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
	
	public function encode($data)
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
		$params = infraRequestUtils::getRequestParams();
		$params = $this->buildKeyValueArrayuFromRawParams($params);
		
		foreach ($this->tokensToReplace as $key => $value)
		{
			if(isset($params[$key]))
			{
				$response = str_replace($value, $params[$key], $response);
			}	
		}
		
		if(preg_match('/<queueKey>(.*?)<\/queueKey>/', $response, $matches))
		{
			$queueKey = $matches[1];
			$encodedQueueKey = $this->encode(md5($queueKey) . ":" . $this->hash);
			$response = str_replace($queueKey, $encodedQueueKey, $response);
		}
	}
	
	public function buildKeyValueArrayuFromRawParams($params)
	{
		$result = array();
		
		foreach ($params as $key => $value)
		{
			preg_match('/(pushNotificationParams:userParams:item\\d:).*key/', $key, $matches);
			if(count($matches))
			{
				$resKey = $matches[1];
				$resKey = $resKey . "value:value";
				$resValue = $params[$resKey];
				$result[$value] = $resValue;
			}
		}

		return $result;
	}
	
	public function addToken($key, $token)
	{
		$this->tokensToReplace[$key] = $token;
	}
	
	public function setHash($hash)
	{
		$this->hash = $hash;
	}
}