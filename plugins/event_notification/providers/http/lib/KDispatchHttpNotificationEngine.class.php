<?php
/**
 * @package plugins.httpNotification
 * @subpackage Scheduler
 */
class KDispatchHttpNotificationEngine extends KDispatchEventNotificationEngine
{
	/**
	 * Folder to save uploaded files.
	 * 
	 * @var string
	 */
	protected $tempFolderPath;
	
	/* (non-PHPdoc)
	 * @see KDispatchEventNotificationEngine::__construct()
	 */
	public function __construct()
	{
		$this->tempFolderPath = sys_get_temp_dir();
		
		if(isset(KBatchBase::$taskConfig->params->tempFolderPath) && KBatchBase::$taskConfig->params->tempFolderPath)
			$this->tempFolderPath = KBatchBase::$taskConfig->params->tempFolderPath;
	}
	
	/* (non-PHPdoc)
	 * @see KDispatchEventNotificationEngine::dispatch()
	 */
	public function dispatch(KalturaEventNotificationTemplate $eventNotificationTemplate, KalturaEventNotificationDispatchJobData &$data)
	{
		$this->sendHttpRequest($eventNotificationTemplate, $data);
	}

	/**
	 * @param KalturaHttpNotificationTemplate $httpNotificationTemplate
	 * @param KalturaHttpNotificationDispatchJobData $data
	 * @return boolean
	 */
	public function sendHttpRequest(KalturaHttpNotificationTemplate $httpNotificationTemplate, KalturaHttpNotificationDispatchJobData &$data)
	{
		/**
		 * TODO
		 * 
		 * add headers:
		 * job id
		 * scheduler id, worker id, session
		 */
		
		$contentParameters = array();
		$postParameters = array();
		if(is_array($data->contentParameters) && count($data->contentParameters))
		{
			foreach($data->contentParameters as $contentParameter)
			{
				/* @var $contentParameter KalturaKeyValue */
				$postParameters[$contentParameter->key] = $contentParameter->value;
				$contentParameters['{' . $contentParameter->key . '}'] = $contentParameter->value;
			}		
		}
		
		$headers = array();
		$curlData = $data->data;
		$secret = $data->signSecret;
		if(!is_null($secret)) { 
			$dataSig = sha1($secret . $curlData);
			$headers[] = "X-KALTURA-SIGNATURE: $dataSig";
		}
		
		if(is_array($data->customHeaders) && count($data->customHeaders))
		{
			foreach($data->customHeaders as $customHeader)
			{
				/* @var $customHeader KalturaKeyValue */
				$key = $customHeader->key;
				$value = $customHeader->value;
				if(is_array($contentParameters) && count($contentParameters))
				{
					$key = str_replace(array_keys($contentParameters), $contentParameters, $key);
					$value = str_replace(array_keys($contentParameters), $contentParameters, $value);
				}
				$headers[] = "$key: $value";
			}
		}
		
		
		$curlWrapper = new KCurlWrapper();

		if(count($headers))
			$curlWrapper->setOpt(CURLOPT_HTTPHEADER, $headers);

		$url = $data->url;
		switch($data->method)
		{
			case KalturaHttpNotificationMethod::POST:
				$curlWrapper->setOpt(CURLOPT_POST, true);
				if ($curlData)
					$curlWrapper->setOpt(CURLOPT_POSTFIELDS, $curlData);
				break;

			case KalturaHttpNotificationMethod::PUT:
				$curlWrapper->setOpt(CURLOPT_PUT, true);

				if ($curlData)
				{
					$filename = tempnam($this->tempFolderPath, 'httpPut_');
					file_put_contents($filename, $curlData);
					$curlWrapper->setOpt(CURLOPT_INFILE, $filename);
				}
				break;

			case KalturaHttpNotificationMethod::DELETE:
				$curlWrapper->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
				if ($curlData)
					$curlWrapper->setOpt( CURLOPT_POSTFIELDS, $curlData);
				break;
				
			case KalturaHttpNotificationMethod::GET:
			default:
				if($curlData)
					$url .= '?' . $curlData;
		}

		if($data->timeout)
			$curlWrapper->setOpt( CURLOPT_TIMEOUT, $data->timeout);

		if($data->connectTimeout)
			$curlWrapper->setOpt( CURLOPT_CONNECTTIMEOUT, $data->connectTimeout);

		if($data->authenticationMethod)
			$curlWrapper->setOpt( CURLOPT_HTTPAUTH, $data->authenticationMethod);

		if($data->sslVersion)
			$curlWrapper->setOpt( CURLOPT_SSLVERSION, $data->sslVersion);

		if($data->sslCertificateType)
			$curlWrapper->setOpt( CURLOPT_SSLCERTTYPE, $data->sslCertificateType);

		if($data->sslCertificatePassword)
			$curlWrapper->setOpt( CURLOPT_SSLCERTPASSWD, $data->sslCertificatePassword);

		if($data->sslEngine)
			$curlWrapper->setOpt( CURLOPT_SSLENGINE, $data->sslEngine);

		if($data->sslEngineDefault)
			$curlWrapper->setOpt( CURLOPT_SSLENGINE_DEFAULT, $data->sslEngineDefault);

		if($data->sslCertificate)
		{
			if($data->sslCertificateType == KalturaHttpNotificationCertificateType::PEM)
				$curlWrapper->setOpt( CURLOPT_SSLCERT, $data->sslCertificate);
			else
			{
				$curlWrapper->setOpt( CURLOPT_CAINFO, $data->sslCertificate);
				$curlWrapper->setOpt( CURLOPT_SSL_VERIFYPEER, true);
			}
		}
		
		if($data->username || $data->password)
			$curlWrapper->setOpt( CURLOPT_USERPWD, $data->username . ':' . $data->password);

		if($data->sslKey)
			$curlWrapper->setOpt( CURLOPT_SSLKEY, $data->sslKey);

		if($data->sslKeyType)
			$curlWrapper->setOpt( CURLOPT_SSLKEYTYPE, $data->sslKeyType);

		if($data->sslKeyPassword)
			$curlWrapper->setOpt( CURLOPT_SSLKEYPASSWD, $data->sslKeyPassword);

		$results = $curlWrapper->doExec($url);
		$httpCode = $curlWrapper->getInfo(CURLINFO_HTTP_CODE);
		$errCode = $curlWrapper->getErrorNumber();
		$errMessage = $curlWrapper->getError();

		$curlWrapper->close();

		KalturaLog::info("HTTP Request httpCode [" . $httpCode . "]\nResults [$results]");
		if(!$results || $httpCode != 200)
		{
			throw new kTemporaryException("Sending HTTP request failed [$errCode] httpCode [$httpCode]: $errMessage", $httpCode);
		}
		
		return true;
	}
}
