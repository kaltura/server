<?php
/**
 * @package plugins.cielo24
 */
class Cielo24ClientHelper
{
	const API_CALL_NAME_PATTERN = "{API-CALL-NAME}";
	
	private $supportedLanguages = array();
	private $baseEndpointUrl = null;
	
	public function __construct($username, $password)
	{
		$cielo24ParamsMap = kConf::get('cielo24','integration');
		$baseUrl = $cielo24ParamsMap['base_url'];
		$version = $cielo24ParamsMap['version'];
		$baseUrl = $baseUrl . "account/login?v=$version";
		$loginParams = array("username" => $username, "password" => $password);	
		$loginUrl = $this->addUrlParams($baseUrl, $loginParams);
	
		$loginResult = self::sendAPICall($loginUrl);
		$apiToken = $loginResult->ApiToken;
			
		$this->baseEndpointUrl = str_replace("account/login", self::API_CALL_NAME_PATTERN, $baseUrl);
		$this->baseEndpointUrl .= "&api_token=$apiToken";   
		
		$this->supportedLanguages = $cielo24ParamsMap['languages'];
	}
	
	public function getRemoteFinishedJobId($entryId)
	{
		$remoteJobsListAPIUrl = str_replace(self::API_CALL_NAME_PATTERN, "job/list", $this->baseEndpointUrl);
		$listParams = array("ExternalID" => $entryId, "JobStatus" => "Complete");
		$remoteJobsListAPIUrl = $this->addUrlParams($remoteJobsListAPIUrl, $listParams);
	
		$exitingJobsResult = $this->sendAPICall($remoteJobsListAPIUrl);
		if($exitingJobsResult && isset($exitingJobsResult->ActiveJobs) && count($exitingJobsResult->ActiveJobs))
		{
			return $exitingJobsResult->ActiveJobs[0]->JobId;
		}
		return false;
	}
	
	public function uploadMedia($flavorUrl, $entryId, $callBackUrl, $spokenLanguage, $priority, $fidelity)
	{
		$languageExternalServiceParam = $this->supportedLanguages[$spokenLanguage];
		
		//adding a job
		$createJobAPIUrl = str_replace(self::API_CALL_NAME_PATTERN, "job/new", $this->baseEndpointUrl);
		$jobCreationParams = array("language" => $languageExternalServiceParam,
						"external_id" => $entryId
						);
		$createJobAPIUrl = $this->addUrlParams($createJobAPIUrl, $jobCreationParams);
		$jobAdditionResult = $this->sendAPICall($createJobAPIUrl);
		if($jobAdditionResult && isset($jobAdditionResult->JobId))
			$jobId = $jobAdditionResult->JobId;
		else
			return false;
		
		// attaching media to the job
		$addMediaAPIUrl = str_replace(self::API_CALL_NAME_PATTERN, "job/add_media", $this->baseEndpointUrl);
		$addMediaParams = array("job_id" => $jobId, "media_url" => $flavorUrl);
		$addMediaAPIUrl = $this->addUrlParams($addMediaAPIUrl, $addMediaParams);
		$mediaAdditionResult = $this->sendAPICall($addMediaAPIUrl);
		if(!$mediaAdditionResult || !isset($mediaAdditionResult->TaskId))
			return false;
		
		//request transcription
		$requestTransctiptAPIUrl = str_replace(self::API_CALL_NAME_PATTERN, "job/perform_transcription", $this->baseEndpointUrl);
		$requestParams = array("job_id" => $jobId,
					"transcription_fidelity" => $fidelity,
					"priority" => $priority,
					"callback_url" => $callBackUrl
					);
		$requestTransctiptAPIUrl = $this->addUrlParams($requestTransctiptAPIUrl, $requestParams);
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
				KalturaLog::err("bad response from service provider");
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
		$getTranscriptAPIUrl = str_replace(self::API_CALL_NAME_PATTERN, "job/get_transcript", $this->baseEndpointUrl);
		$transcriptRetrievalParams = array("job_id" => $externalServiceJobId,
							"create_paragraphs" => "false",
							"remove_sound_references" => "true",
							"newlines_after_sentence" => "0",
							"emit_speaker_change_tokens_as" => "",
							"single_sentence_per_line" => "false",
							"remove_disfluencies" => "true",
							"timecode_every_paragraph" => "false"
							);
		$getTranscriptAPIUrl = $this->addUrlParams($getTranscriptAPIUrl, $transcriptRetrievalParams);
		$transcriptContentResult = $this->sendAPICall($getTranscriptAPIUrl, true);
		
		return $transcriptContentResult;
	}
	
	public function getRemoteCaptions($externalServiceJobId, array $formats)
	{
		$captionContents = array();
		
		$baseGetCaptionAPIUrl = str_replace(self::API_CALL_NAME_PATTERN, "job/get_caption", $this->baseEndpointUrl);
		$captionRetrievalParam = array("job_id" => $externalServiceJobId);
		$baseGetCaptionAPIUrl = $this->addUrlParams($baseGetCaptionAPIUrl, $captionRetrievalParam);
		foreach($formats as $format)
		{
			$formatParam = array("caption_format" => $format);
			$getCaptionAPIUrl = $this->addUrlParams($baseGetCaptionAPIUrl, $formatParam);
			$captionContentResult = $this->sendAPICall($getCaptionAPIUrl, true);
			$captionContents[$format] = $captionContentResult;
		}
	
		return $captionContents;
	}
	
	public function deleteRemoteFile($externalServiceJobId)
	{	
		$deleteExternalJobAPIUrl = str_replace(self::API_CALL_NAME_PATTERN, "job/delete", $this->baseEndpointUrl);
		$deletJobParams = array("job_id" => $externalServiceJobId);
		$deleteExternalJobAPIUrl = $this->addUrlParams($deleteExternalJobAPIUrl, $deletJobParams);
		
		$this->sendAPICall($deleteExternalJobAPIUrl);
	}
	
	private function addUrlParams($url, array $params)
	{
		return $url . "&" . http_build_query($params);
	}
}
