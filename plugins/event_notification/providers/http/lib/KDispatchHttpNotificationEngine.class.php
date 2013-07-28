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
	public function dispatch(KalturaEventNotificationTemplate $eventNotificationTemplate, KalturaEventNotificationDispatchJobData $data)
	{
		$this->sendHttpRequest($eventNotificationTemplate, $data);
	}

	/**
	 * @param KalturaHttpNotificationTemplate $httpNotificationTemplate
	 * @param KalturaHttpNotificationDispatchJobData $data
	 * @return boolean
	 */
	public function sendHttpRequest(KalturaHttpNotificationTemplate $httpNotificationTemplate, KalturaHttpNotificationDispatchJobData $data)
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
		
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		
		if(count($headers))
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			
		$url = $data->url;
		switch($data->method)
		{
			case KalturaHttpNotificationMethod::POST:
				curl_setopt($ch, CURLOPT_POST, true);
				
				if($data->data)
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data->data);
				break;
				
			case KalturaHttpNotificationMethod::PUT:
				curl_setopt($ch, CURLOPT_PUT, true);
				
				if($data->data)
				{
					$filename = tempnam($this->tempFolderPath, 'httpPut_');
					file_put_contents($filename, $data->data) ;
					curl_setopt($ch, CURLOPT_INFILE, $filename);
				}
				break;
				
			case KalturaHttpNotificationMethod::DELETE:
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				
				if($data->data)
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data->data);
				break;
				
			case KalturaHttpNotificationMethod::GET:
			default:
				if($data->data)
					$url .= '?' . $data->data;
		}

		curl_setopt($ch, CURLOPT_URL, $url);
		
		if($data->timeout)
			curl_setopt($ch, CURLOPT_TIMEOUT, $data->timeout);
		
		if($data->connectTimeout)
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $data->connectTimeout);
		
		if($data->authenticationMethod)
			curl_setopt($ch, CURLOPT_HTTPAUTH, $data->authenticationMethod);
		
		if($data->sslVersion)
			curl_setopt($ch, CURLOPT_SSLVERSION, $data->sslVersion);
		
		if($data->sslCertificateType)
			curl_setopt($ch, CURLOPT_SSLCERTTYPE, $data->sslCertificateType);
		
		if($data->sslCertificatePassword)
			curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $data->sslCertificatePassword);
		
		if($data->sslEngine)
			curl_setopt($ch, CURLOPT_SSLENGINE, $data->sslEngine);
		
		if($data->sslEngineDefault)
			curl_setopt($ch, CURLOPT_SSLENGINE_DEFAULT, $data->sslEngineDefault);
		
		if($data->sslCertificate)
		{
			if($data->sslCertificateType == KalturaHttpNotificationCertificateType::PEM)
			{
				curl_setopt($ch, CURLOPT_SSLCERT, $data->sslCertificate);
			}
			else
			{
				curl_setopt($ch, CURLOPT_CAINFO, $data->sslCertificate);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			}
		}
		
		if($data->username || $data->password)
			curl_setopt($ch, CURLOPT_USERPWD, $data->username . ':' . $data->password);
		
		if($data->sslKey)
			curl_setopt($ch, CURLOPT_SSLKEY, $data->sslKey);
		
		if($data->sslKeyType)
			curl_setopt($ch, CURLOPT_SSLKEYTYPE, $data->sslKeyType);
		
		if($data->sslKeyPassword)
			curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $data->sslKeyPassword);
		
		$results = curl_exec($ch);
		$info = curl_getinfo($ch);
		$httpCode = $info['http_code'];
		$errCode = curl_errno($ch);
		$errMessage = curl_error($ch);
		curl_close($ch);
		
		KalturaLog::info("HTTP Request info [" . print_r($info, true) . "]\nResults [$results]");
		if(!$results || $httpCode != 200)
		{
			throw new kTemporaryException("Sending HTTP request failed [$errCode]: $errMessage", $httpCode);
		}
		
		return true;
	}
}
