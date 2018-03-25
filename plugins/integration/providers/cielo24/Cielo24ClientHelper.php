<?php
/**
 * @package plugins.cielo24
 */
class Cielo24ClientHelper
{	
	private $supportedLanguages = array();
	private $baseEndpointUrl = null;
	private $apiCredentialsStr = null;
	private $additionalParams = array();
	private static $getTranscriptWhitelistedParams = array (
															"emit_speaker_change_tokens_as",
															"newlines_after_paragraph",
															"newlines_after_sentence",
															"mask_profanity",
															"remove_sounds_list",
															"remove_sound_references",
															"replace_slang",
															"timecode_every_paragraph",
														);
	private static $getCaptionWhitelistedParams = array (
															"disallow_dangling",
															"remove_disfluencies",
															"display_speaker_id",
															"emit_speaker_change_tokens_as",
															"mask_profanity",
															"replace_slang",
															"remove_sound_references",
															);

	private static $performTranscriptionWhitelistedParams = array (
															"priority",
															"turnaround_time_hours",
														);
	 
	
	public function __construct($username, $password, $baseUrl = null, $additionalParams = array())
	{
		$cielo24ParamsMap = kConf::get('cielo24','integration');
		if(!is_null($baseUrl))
		{
			$this->baseEndpointUrl = $baseUrl;
		}
		else
		{
			$this->baseEndpointUrl = $cielo24ParamsMap['base_url'];
		}
		$this->apiCredentialsStr = "v=" . $cielo24ParamsMap['version'];
		
		$loginParams = array("username" => $username, "password" => $password);	
		$loginUrl = $this->createAPIUrl("account/login", $loginParams);
		$loginResult = self::sendAPICall($loginUrl);
		$apiToken = $loginResult->ApiToken;
		$this->apiCredentialsStr .= "&api_token=$apiToken";   
		
		$this->supportedLanguages = $cielo24ParamsMap['languages'];
		$this->additionalParams = $additionalParams;
	}

	public function getRemoteJobIdByName($entryId, $jobName, $multiple = false)
	{
		$listParams = array("ExternalID" => $entryId, "JobName" => $jobName);
		return $this->getRemoteJobId($listParams, $multiple);
	}

	public function getRemoteJobId($listParams, $multiple = false)
	{
		$remoteJobsListAPIUrl = $this->createAPIUrl("job/list", $listParams);
		$exitingJobsResult = $this->sendAPICall($remoteJobsListAPIUrl);
		if($exitingJobsResult && isset($exitingJobsResult->ActiveJobs) && count($exitingJobsResult->ActiveJobs))
		{
			if(!$multiple)
				return $exitingJobsResult->ActiveJobs[0]->JobId;
			else
			{
				$jobIds = array();
				foreach($exitingJobsResult->ActiveJobs as $activeJob)
					$jobIds[] = $activeJob->JobId;
				return $jobIds;		
			}
		}
		return false;
	}
	
	public function uploadMedia($flavorUrl, $entryId, $callBackUrl, $spokenLanguage, $priority, $fidelity, $jobName)
	{
		$languageExternalServiceParam = $this->supportedLanguages[$spokenLanguage];
		
		//adding a job
		$jobCreationParams = array("language" => $languageExternalServiceParam,"external_id" => $entryId, "job_name" => $jobName);
		$createJobAPIUrl = $this->createAPIUrl("job/new", $jobCreationParams);
		$jobAdditionResult = $this->sendAPICall($createJobAPIUrl);
		if($jobAdditionResult && isset($jobAdditionResult->JobId))
			$jobId = $jobAdditionResult->JobId;
		else
			return false;

		// attaching media to the job
		$addMediaParams = array("job_id" => $jobId, "media_url" => $flavorUrl);
		$addMediaAPIUrl = $this->createAPIUrl("job/add_media", $addMediaParams);
		$mediaAdditionResult = $this->sendAPICall($addMediaAPIUrl);
		if(!$mediaAdditionResult || !isset($mediaAdditionResult->TaskId))
			return false;
		
		//request transcription
		$requestParams = array("job_id" => $jobId,
								"transcription_fidelity" => $fidelity,
								"priority" => $priority,
								"callback_url" => $callBackUrl
								);

		if (isset($this->additionalParams['perform_transcription']))
		{
			$additionalParameters = array ();
			foreach ($this->additionalParams['perform_transcription'] as $key => $value)
			{
				if (in_array ($key, self::$performTranscriptionWhitelistedParams))
					$additionalParameters[$key] = $value;
			}

			$requestParams = array_merge($requestParams, $additionalParameters);
		}

		$requestTransctiptAPIUrl = $this->createAPIUrl("job/perform_transcription", $requestParams);
		$requestTranscriptionResult = $this->sendAPICall($requestTransctiptAPIUrl);
		if(!$requestTranscriptionResult || !isset($requestTranscriptionResult->TaskId))
			return false;
			
		return true;
	}
	
	private function sendAPICall($url, $noDecoding = false)
	{
		KalturaLog::debug("sending API call - $url");

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

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
	
	public function getRemoteTranscript($externalServiceJobId)
	{	
		$transcriptRetrievalParams = array("job_id" => $externalServiceJobId,
							"create_paragraphs" => "false",
							"remove_sound_references" => "true",
							"newlines_after_sentence" => "0",
							"emit_speaker_change_tokens_as" => "",
							"single_sentence_per_line" => "false",
							"remove_disfluencies" => "true",
							"timecode_every_paragraph" => "false"
							);
							
		if (isset($this->additionalParams['get_transcript']))
		{
			$additionalParameters = array ();
			foreach ($this->additionalParams['get_transcript'] as $key => $value)
			{
				if (in_array ($key, self::$getTranscriptWhitelistedParams))
					$additionalParameters[$key] = $value;
			}
			
			$transcriptRetrievalParams = array_merge($transcriptRetrievalParams, $additionalParameters);
		}
			
		$getTranscriptAPIUrl = $this->createAPIUrl("job/get_transcript", $transcriptRetrievalParams);
		$transcriptContentResult = $this->sendAPICall($getTranscriptAPIUrl, true);
		
		return $transcriptContentResult;
	}
	
	public function getRemoteTranscriptTokens ($externalServiceJobId)
	{
		$transcriptRetrievalParams = array("job_id" => $externalServiceJobId);
		$getTranscriptAPIUrl = $this->createAPIUrl("job/get_elementlist", $transcriptRetrievalParams);
		$transcriptContentResult = $this->sendAPICall($getTranscriptAPIUrl, true);
		
		return $transcriptContentResult;
	}
	
	public function getRemoteCaptions($externalServiceJobId, array $formats)
	{
		$captionContents = array();
		$captionRetrievalParams = array("job_id" => $externalServiceJobId);
		
		if (isset($this->additionalParams['get_caption']))
		{
			$additionalParameters = array ();
			foreach ($this->additionalParams['get_caption'] as $key => $value)
			{
				if (in_array ($key, self::$getCaptionWhitelistedParams))
					$additionalParameters[$key] = $value;
			}
			$captionRetrievalParams = array_merge($captionRetrievalParams, $additionalParameters);
		}
		
		$baseGetCaptionAPIUrl = $this->createAPIUrl("job/get_caption", $captionRetrievalParams);
		foreach($formats as $format)
		{
			$getCaptionAPIUrl = $this->appendToUrl($baseGetCaptionAPIUrl, array("caption_format" => $format));
			$captionContentResult = $this->sendAPICall($getCaptionAPIUrl, true);
			$captionContents[$format] = $captionContentResult;
		}
	
		return $captionContents;
	}
	
	public function deleteRemoteFile($externalServiceJobId)
	{	
		$deletJobParams = array("job_id" => $externalServiceJobId);
		$deleteExternalJobAPIUrl = $this->createAPIUrl("job/delete", $deletJobParams);
		
		$this->sendAPICall($deleteExternalJobAPIUrl);
	}
	
	private function createAPIUrl($actionStr, array $params)
	{
		$url = $this->baseEndpointUrl . $actionStr . "?" . $this->apiCredentialsStr;
		return $this->appendToUrl($url, $params);
	}
	
	private function appendToUrl($url, array $params)
	{
		return $url . "&" . http_build_query($params);
	}

	public function getLanguageConstantName($language)
	{
		$languagesReflectionClass = new ReflectionClass('KalturaLanguage');
		$languageNames = $languagesReflectionClass->getConstants();
		$languageName = array_search($language, $languageNames);

		return $languageName !== false ? $languageName : '';
	}

}
