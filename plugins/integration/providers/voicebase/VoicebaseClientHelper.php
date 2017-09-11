<?php
/**
 * @package plugins.voicebase
 */
class VoicebaseClientHelper
{
	const VOICEBASE_FAILURE_MESSAGE = "FAILURE";
	const VOICEBASE_MACHINE_COMPLETE_REQUEST_STATUS = "SUCCESS";
	const VOICEBASE_MACHINE_COMPLETE_MESSAGE = "MACHINECOMPLETE";
	const VOICE_MACHINE_FAILURE_MESSAGE = "ERROR";
	
	private $supportedLanguages = array();
	private $baseEndpointUrl = null;
	
	public function __construct($apiKey, $apiPassword)
	{
		$voicebaseParamsMap = kConf::get('voicebase','integration');
		$this->supportedLanguages = $voicebaseParamsMap['languages'];
		$version = $voicebaseParamsMap['version'];
	
		$url = $voicebaseParamsMap['base_url'];
		$params = array("version" => $version, "apikey" => $apiKey, "password" => $apiPassword);
		
		$url = $this->addUrlParams($url, $params, true);
		$this->baseEndpointUrl = $url;
	}
	
	public function checkExistingExternalContent($entryId)
	{	
		$curlResult = $this->retrieveRemoteProcess($entryId);
		if($curlResult)
		{
			if ($curlResult->requestStatus == self::VOICEBASE_FAILURE_MESSAGE || !isset($curlResult->fileStatus) || !$curlResult->fileStatus == self::VOICEBASE_MACHINE_COMPLETE_MESSAGE)
				return false;
			return true;
		}
		
		return false;
	}
	
	public function retrieveRemoteProcess ($entryId)
	{
		$params = array("action" => "getFileStatus", "externalID" => $entryId);
		$exitingEntryQueryUrl = $this->addUrlParams($this->baseEndpointUrl, $params);
	
		$curlResult = $this->sendAPICall($exitingEntryQueryUrl);
		
		return $curlResult;
	}
	
	public function uploadMedia($flavorUrl, $entryId, $callBackUrl, $spokenLanguage, $fileLocation = null)
	{
		if($spokenLanguage)
			$spokenLanguage = $this->supportedLanguages[$spokenLanguage];
		
		$params = array("action" => "uploadMedia",
						"title" => $entryId,
						"externalID" => $entryId, 
						"lang" => $spokenLanguage
						);
	
		$postParams = array("mediaURL" => $flavorUrl);
		if($fileLocation)
		{
			$adjustedLocation = $this->getFile($fileLocation);
			$postParams["transcript"] = $adjustedLocation;
			$postParams["transcriptType"] = "human";
			$postParams["humanReadyCallBack"] = $callBackUrl;
		}
		else
		{
			$postParams["transcriptType"] = "machine-bestAvailable";
			$postParams["machineReadyCallBack"] = $callBackUrl;
		}
		$uploadAPIUrl = $this->addUrlParams($this->baseEndpointUrl, $params);

		$urlOptions = array(CURLOPT_POST => 1, CURLOPT_POSTFIELDS => $postParams);

		$curlResult = $this->sendAPICall($uploadAPIUrl, $urlOptions);
	
		if($curlResult->requestStatus == self::VOICEBASE_FAILURE_MESSAGE)
			return false;
		
		return true;
	}
	
	private function sendAPICall($url, $options = null, $noDecoding = false)
	{
		KalturaLog::debug("sending API call - $url");

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
		if ($options)
			curl_setopt_array($ch, $options);

		$result = curl_exec($ch);

		if(($errString = curl_error($ch)) !== '' || ($errNum = curl_errno($ch)) !== 0)
		{
			KalturaLog::err('problem with curl - ' . $errString . ' error num - ' . $errNum);
			curl_close($ch);
			throw new Exception("curl error with url " . $url);
		}
		if(!$noDecoding)
		{
			$stringResult = $result;
			$result = json_decode($result);
				
			if (json_last_error() !== JSON_ERROR_NONE)
			{
				curl_close($ch);
				throw new Exception("json decode error with response - " . $stringResult);
			}
			
		}
		KalturaLog::debug('result is - ' . var_dump($result));
		curl_close($ch);
		
		return $result;
	}
	
	public function updateRemoteTranscript($entryId, $transcriptContent, $callBack)
	{
		$params = array("action" => "updateTranscript", "externalID" => $entryId);
		$updateTranscriptUrl = $this->addUrlParams($this->baseEndpointUrl, $params);

		$transcriptContent = $this->getFile($transcriptContent);
		$postFields = array(
				"transcript" => $transcriptContent,
				"machineReadyCallBack" => $callBack,
				"humanReadyCallBack" => $callBack,
		);
		$options = array(CURLOPT_POST => 1, CURLOPT_POSTFIELDS => $postFields);
	
		$this->sendAPICall($updateTranscriptUrl, $options);
	}
	
	public function getRemoteTranscripts($entryId, array $formats)
	{	
		$params = array("action" => "getTranscript", "externalID" => $entryId);
		$getTranscriptUrl = $this->addUrlParams($this->baseEndpointUrl, $params);
	
		$results = array();
		foreach($formats as $format)
		{
			$formatParam = array("format" => $format);
			$url = $this->addUrlParams($getTranscriptUrl, $formatParam);
			$result = $this->sendAPICall($url);
			$results[$format] = $result->transcript;
		}
		
		return $results;
	}
	
	public function calculateAccuracy($entryId)
	{
		$contentArr = $this->getRemoteTranscripts($entryId, array("JSON"));
		$transcriptWordObjects = json_decode($contentArr["JSON"]);
		$sumOfAccuracies = 0;
		$numberOfElements = 0;
		
		foreach($transcriptWordObjects as $wordObject)
		{
			if(isset($wordObject->c) && 0 <= $wordObject->c && $wordObject->c <= 1)
			{
				$sumOfAccuracies += $wordObject->c;
				$numberOfElements++;
			}
		}
	
		if($numberOfElements)
			return $sumOfAccuracies/$numberOfElements;
		
		return 0;
	}
	
	public function deleteRemoteFile($entryId)
	{	
		$params = array("action" => "deleteFile", "externalID" => $entryId);
		$deleteUrl = $this->addUrlParams($this->baseEndpointUrl, $params);
	
		$curlResult = $this->sendAPICall($deleteUrl);
	}

	private function getFile($path)
	{
		if (PHP_VERSION_ID >= 50500)
			return new \CURLFile($path);
		else
			return '@' . $path;
	}

	private function addUrlParams($url, array $params, $init = false)
	{
		$url .= $init ? '?' : '&' ;
		
		return $url . http_build_query($params);
	}
}
