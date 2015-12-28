<?php
/**
 * @package plugins.visualRecognition
 * TODO: this api can get in one call mulitple images - we can optimize here.
 */
class SightDetectionEngine extends BaseDetectionEngine
{
	const CLIENT_USER = '1421663145';
	const CLIENT_SECRET = '7WSWXFfPX83WmMgR';
	const RECOGNIZE_URL = 'https://api.sightengine.com/1.0/nudity.json';
	private $currentResults = array();


	public function init() {
		return true;
	}

	public function shouldStopAfterFirstHit() {
		return true;
	}

	/*
	 * method that will call 3rd party service and return external token / job ID or false if not received one.
	 */
	public function initiateRecognition(array $thumbnailUrls)
	{
		foreach ($thumbnailUrls as $second=>$thumbnailUrl) {
			$fullUrl = self::RECOGNIZE_URL . '?api_user=' . self::CLIENT_USER .'&api_secret=' . self::CLIENT_SECRET . '&url='
				. $thumbnailUrl;
			$handle = $this->getCurlHandle($fullUrl);
			$result = $this->execCurl($handle);
			if (isset($result['status']) && $result['status'] == 'success' && isset($result['nudity']) &&
				isset($result['nudity']['result']) && isset($result['nudity']['confidence']) &&
				$result['nudity']['result'] == true && $result['nudity']['confidence'] >= 50 ) {
				$this->currentResults = array('true');
				return true;
			}
		}
		$this->currentResults = array('false');
		return false;
	}

	/*
	 * method that gets a token / job ID of previous call to the 3rd party,
	 * and makes the necessary call to return the data if done
	 *
	 * @return array of words or false if job not done
	 */
	public function checkRecognitionStatus(array $jobIds)
	{
		return $this->currentResults;
	}

	public function asyncCall() {
		return false;
	}
}
