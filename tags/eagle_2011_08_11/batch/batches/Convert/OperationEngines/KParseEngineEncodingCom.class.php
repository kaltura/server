<?php

/**
 * Encoding.com API: http://www.encoding.com/wdocs/ApiDoc
 * 
 * @package Scheduler
 * @subpackage Conversion
 */
class KParseEngineEncodingCom
{
	const ENCODING_COM = "encoding_com";
	
	private $conversionLog;

	/*
	 * @var KSchedularTaskConfig
	 */
	protected $engine_config;
	
	/**
	 * @param KSchedularTaskConfig $engine_config
	 */
	public function __construct( KSchedularTaskConfig $engine_config )
	{
		$this->engine_config = $engine_config;
	}
	
	public function getName()
	{
		return self::ENCODING_COM;
	}
	
	public function getType()
	{
		return KalturaConversionEngineType::ENCODING_COM;
	}
	
	public function getLogData()
	{
		return $this->conversionLog;
	}
	
	public function logConvert($data)
	{
		$this->conversionLog .= "$data\n\n";
	}

	protected function getUserId()
	{
		return $this->engine_config->params->EncodingComUserId;
	}

	protected function getUserKey()
	{
		return $this->engine_config->params->EncodingComUserKey;
	}

	protected function getUrl()
	{
		return $this->engine_config->params->EncodingComUrl;
	}
	
	/**
	 * @param KalturaConvertJobData $data
	 * @param string $errMessage
	 * @return number
	 */
	public function parse ( KalturaConvertJobData &$data, &$errMessage )
	{
		$sendData = new KEncodingComData();
		
		$sendData->setUserId($this->getUserId());
		$sendData->setUserKey($this->getUserKey());
		
		$sendData->setAction(KEncodingComData::ACTION_GET_STATUS);
		$sendData->setMediaId($data->remoteMediaId);

		$err = null;
		$requestXml = $sendData->getXml();
		$responseXml = $this->sendRequest($requestXml, $err);
		
		if(!$responseXml)
		{
			$errMessage = "Error: $err";
			return KalturaBatchJobStatus::ALMOST_DONE;
		}		
		
		preg_match('/\<status\>([\w\s]*)\<\/status\>/', $responseXml, $status);
		$status = (isset($status[1]) ? $status[1] : null); 
		if (!$status)
		{
			$errMessage = 'status not found';
			return KalturaBatchJobStatus::ALMOST_DONE;
		}
		
		if(strtolower($status) == "error")
		{
			preg_match_all('/\<description\>([^<]*)\<\/description\>/', $responseXml, $description);
			$errMessage = implode("\n", $description[1]);
			return KalturaBatchJobStatus::FAILED;
		}
		
		if(strtolower($status) != "finished")
		{
			$errMessage = $status;
			return KalturaBatchJobStatus::ALMOST_DONE;
		}
		
		preg_match('/\<s3_destination\>(.*)\<\/s3_destination\>/', $responseXml, $s3_destination);
		$s3_destination = (isset($s3_destination[1]) ? $s3_destination[1] : null);
		$data->destFileSyncRemoteUrl = $s3_destination;
		$errMessage = "Remote url: $s3_destination";
		return KalturaBatchJobStatus::FINISHED;
	}
	
	/**
	 * @param string $requestXml
	 * @param string $err
	 * @return false|string
	 */
	private function sendRequest($requestXml, &$err)
	{
		KalturaLog::info("sendRequest($requestXml)");

		$url = $this->getUrl();
		
		$this->logConvert("url: $url");
		$this->logConvert("send request:\n$requestXml");
		
		$fields = array(
			"xml" => $requestXml
		);
		 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$result = curl_exec($ch);
		$this->logConvert("received response:\n$result");
		
		if(!$result)
		{
			$err = curl_error($ch);
			$this->logConvert("curl error: $err");
		}
		
		curl_close($ch);
		
		KalturaLog::info("request results: ($result)");
		return $result;
	}
}
