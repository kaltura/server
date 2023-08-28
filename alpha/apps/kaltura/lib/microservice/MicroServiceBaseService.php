<?php
/**
 * Base Micro Service
 */
class MicroServiceBaseService
{
	const MICRO_SERVICE_PREFIX_PLACEHOLDER = "[micro-url-prefix]";
	protected $serviceUrl = '';
	protected $requestHeaders = array();

	/**
	 * @param string $microServicePrefix - the service url prefix (app-registry.service-url/micro-service-url)
	 * @param string $microServiceUrl - the specific micro-service url (app-registry)
	 */
	public function __construct($microServicePrefix, $microServiceUrl)
	{
		$this->initService($microServicePrefix, $microServiceUrl);
	}

	private function generateSession($partnerId)
	{
		$secrets = kSessionBase::getSecretsFromCache($partnerId);
		if (!$secrets)
		{
			return null;
		}

		list($adminSecret, $userSecret, $ksVersion) = $secrets;
		$privileges = "*,disableentitlement";
		return kSessionBase::generateSession($ksVersion, $adminSecret, 'admin', kSessionBase::SESSION_TYPE_ADMIN, $partnerId, 3600, $privileges);
	}

	/**
	 * init the micro service
	 *
	 * @param string $microServicePrefix - the service url prefix
	 * @param string $microServiceUrl - the service action
	 */
	private function initService($microServicePrefix, $microServiceUrl)
	{
		// service url
		$serviceUrl = kConf::get("microservice_url");
		$serviceUrl = str_replace(self::MICRO_SERVICE_PREFIX_PLACEHOLDER, $microServicePrefix, $serviceUrl);
		$this->serviceUrl = trim($serviceUrl, "\/") . '/' . trim($microServiceUrl, "\/");

		if(strpos($this->serviceUrl, 'https://') !== false)
		{
			$header = "X-FORWARDED-PROTO: https"; // standard, in AWS deployment
			array_push($this->requestHeaders,  $header);
		}

		// content type
		$header = "Content-Type: application/json";
		array_push($this->requestHeaders,  $header);
	}

	/**
	 * perform service request
	 *
	 * @param string $action - the specific service action
	 * @param array $params - service params
	 * @return object - the response
	 * @throws
	 */
	protected function serve($partnerId, $action, $params)
	{
		// url - service url + action
		$requestUrl = $this->serviceUrl .'/' . trim($action, '\/');

		// params => json
		$requestParams = json_encode($params);

		// auth header
		// generate admin ks
		$ks = $this->generateSession($partnerId);

		$requestHeaders = $this->requestHeaders;
		$header = "Authorization: KS $ks";
		array_push($requestHeaders,  $header);

		// curl
		$ch = curl_init($requestUrl);

		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $requestParams);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$startTime = microtime(true);
		$response = curl_exec($ch);
		$timeTook = microtime(true) - $startTime;

		$requestInfo = array(
			'requestUrl' => $requestUrl,
			'requestHeaders' => $requestHeaders,
			'requestBody' => $requestParams
		);
		
		KalturaLog::debug('Microservice request data: ' . print_r($requestInfo, 1));
		KalturaLog::debug('Microservice request took - ' . $timeTook. ' seconds');

		$curlError = curl_error($ch);
		curl_close($ch);

		// curl errors - throws exception
		if (!empty($curlError))
		{
			//TODO: handle error
		}

		// parse response
		$result = json_decode($response);
		if (empty($result))
		{
			KalturaLog::err("MicroService: error contacting service " . $requestUrl . ": " .$response);
		}

		$statusCode = isset($result->statusCode) ? $result->statusCode : 200;
		$error = isset($result->error) ? $result->error : '';
		$errorMessage = isset($result->message) ? $result->message : '';
		if (is_array($errorMessage))
		{
			$errorMessage = $errorMessage[0] ? $errorMessage[0] : '';
		}

		// service errors - throws exception
		if ($statusCode !== 200)
		{
			$error =  $statusCode . ':' . $error . ':' . $errorMessage;
			KalturaLog::err("MicroService: error contacting service " . $requestUrl . ": " .$error);
		}

		//return json
		return $result;
	}
}