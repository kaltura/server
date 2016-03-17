<?php

require_once(KALTURA_ROOT_PATH . '/alpha/apps/kaltura/lib/cache/kCacheManager.php');
require_once(KALTURA_ROOT_PATH . '/vendor/facebook-sdk-php-v5-customized/autoload.php');

/**
 * Saves key/value in the custom data of the provider given
 *  @package infra
 *  @subpackage general
 */
class kDistributionPersistentDataHandler implements \Facebook\PersistentData\PersistentDataInterface{

	private $accessURL;

	/**
	 * expecting http://hostname/api_v3/index.php?service=contentdistribution_distributionprofile&&id=@id&distributionProfile%3AobjectType=KalturaFacebookDistributionProfile&ks=bxgsxvsxs
	 * @param string $accessURL
	 * @throws Exception
	 */
    function __construct($accessURL){
		$this->accessURL = $accessURL;
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
		$postParams = array();
		$postParams['action']='get';
		// first we create the URL
		$requestUrl = $this->accessURL;
		$response = $this->runCurlCommand($requestUrl, $postParams);
		return $this->getAndValidateKeyValueFromXMLResponse($key, $response);
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value)
    {
		$postParams = array();
		$postParams['action']='update';
		$postParams['distributionProfile:'.$key] = $value;
		$requestUrl = $this->accessURL;

		$response = $this->runCurlCommand($requestUrl, $postParams);
		$this->getAndValidateKeyValueFromXMLResponse($key, $response);
    }


	private function runCurlCommand($urlString, $postParams)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $urlString);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE); // make sure we're doing this using post
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
		$response = curl_exec($ch);
		if (curl_errno($ch))
			throw new Exception("Error while trying to connect to:". $urlString."error=".curl_error($ch));
		return $response;
	}

	private function getAndValidateKeyValueFromXMLResponse($key, $response)
	{
		try {
			$xml = simplexml_load_string($response);
		} catch (Exception $e) {
			throw new Exception("Failed to parse response[content] as XML, result was:" . $response);
		}
		if (!$xml)
		{
			throw new Exception("Failed to parse response[content] as XML, result was:" . $response);
		}

		if($xml->xpath('result/'.$key)){
			return ''.$xml->result->$key;
		}
		throw new Exception("Could not find the key {$key} using the handler no such mapping as result/{$key}");

	}

}
