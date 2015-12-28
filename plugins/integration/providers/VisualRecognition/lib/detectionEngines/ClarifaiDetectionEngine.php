<?php
/**
 * @package plugins.visualRecognition
 * TODO: this api can get in one call mulitple images - we can optimize here.
 */
class ClarifaiDetectionEngine extends BaseDetectionEngine
{
	const GET_TOKEN_URL = 'https://api.clarifai.com/v1/token/';
	const CLIENT_ID = 'A6uzUk5JZEHD36ApI7EdvXe3oGvHDMHE0249CR_E';
	const CLIENT_SECRET = 'qZvbJsNZFyjverGO_IHEw0AfhmonqCiSt1SZm3lp';
	const RECOGNIZE_URL = 'https://api.clarifai.com/v1/tag/?url=';
	private static $token;
	private $currentResults = array();


	public function init() {
		$formData = array('grant_type' => 'client_credentials', 'client_id' => self::CLIENT_ID, 'client_secret' =>  self::CLIENT_SECRET, );
		$handle = $this->getCurlHandle(self::GET_TOKEN_URL, null, $formData);
		$result = $this->execCurl($handle);
		if (isset($result['access_token'])) {
			self::$token = $result['access_token'];
			return true;
		} else {
			return false;
		}
	}

	public function shouldStopAfterFirstHit() {
		return false;
	}

	/*
	 * method that will call 3rd party service and return external token / job ID or false if not received one.
	 */
	public function initiateRecognition($thumbnailUrl)
	{
		$headersArray = array('Authorization: Bearer ' . self::$token);
		$handle = $this->getCurlHandle(self::RECOGNIZE_URL, $headersArray);
		$result = $this->execCurl($handle);
		if (isset($result['status_code']) && $result['status_code'] == 'OK' && isset($result['results']) &&
			isset($result['results'][0]) &&	isset($result['results'][0]['result']) &&
			isset($result['results'][0]['result']['tag']) &&
			isset($result['results'][0]['result']['tag']['classes']) &&
			is_array($result['results'][0]['result']['tag']['classes'])) {
			$this->currentResults = $result['results'][0]['result']['tag']['classes'];
		} else {
			return false;
		}
		return true;
	}

	/*
	 * method that gets a token / job ID of previous call to the 3rd party,
	 * and makes the necessary call to return the data if done
	 *
	 * @return array of words or false if job not done
	 */
	public function checkRecognitionStatus($jobId)
	{
		return $this->currentResults;
	}

	public function asyncCall() {
		return false;
	}

}
