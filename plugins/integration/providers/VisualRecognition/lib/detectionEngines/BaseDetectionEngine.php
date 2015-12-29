<?php
/**
 * @package plugins.visualRecognition
 */
abstract class BaseDetectionEngine implements IDetectionEngine
{
	/*
	 * $thumbnailUrl - the thumbnail url
	 * $duration - duration of the video in seconds
	 * $interval - the sampling interval for the video in seconds
	 */
	static function getThumbnailUrls($thumbnailUrl, $duration, $interval)
	{
		$result = array();
		$interval = round($interval);
		//verify duration is bigger than interval
		if ($duration <= $interval) {
			$result[] = $thumbnailUrl;
		} else {
			for ($sec=0; $sec < $duration; $sec+=$interval) {
				$result[$sec] = $thumbnailUrl . "/width/0/vid_sec/" . $sec;
			}
		}

		return $result;
	}

	public function getCurlHandle($url, array $headers = null, $formData = null) {
		$handle = curl_init($url);
		if (!empty($formData)) {
			curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($formData));
			curl_setopt($handle, CURLOPT_POST, 1);
		}

		if (!empty($headers)) {
			curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
		}

		curl_setopt($handle,  CURLOPT_RETURNTRANSFER, 1);
		return $handle;
	}

	public function execCurl($handle, $isJson = true) {
		$result = curl_exec($handle);
		if ($isJson) {
			$result = json_decode($result, true);
		}
		$curlError = curl_error($handle);
		$curlErrorCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		if ($curlErrorCode != '200') {
			KalturaLog::log('VisualRecognitionEngineAbstract call error: ' . print_r($curlError, true));
            KalturaLog::info("VisualRecognitionEngineAbstract HTTP response code is $curlErrorCode");
            KalturaLog::info("VisualRecognitionEngineAbstract HTTP response body is $result");
		}
		curl_close($handle);
		return $result;
	}

	public function init(){}

	/*
	 * method that will call 3rd party service and return external token / job ID
	 */
	public function initiateRecognition(array $thumbnailUrls){}

	/*
	 * method that gets a token / job ID of previous call to the 3rd party,
	 * and makes the necessary call to return the data if done
	 *
	 * @return array of words or false if job not done
	 */
	public function checkRecognitionStatus(array $jobIds){}
}
