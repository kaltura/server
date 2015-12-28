<?php
/**
 * @package plugins.visualRecognition
 */
class CloudsapiDetectionEngine extends BaseDetectionEngine
{
	const RECOGNIZE_URI = 'http://api.cloudsightapi.com/image_requests';
	const KEY = 'CloudSight zYF_TIv3zFjEWPHGNfl4Cw';
	const CHECK_URL = 'http://api.cloudsightapi.com/image_responses/';
	private $currentResults = array();
	private $finalResults = array();

	public function init() {
		return true;
	}

	public function shouldStopAfterFirstHit() {
		return false;
	}

	/*
	 * method that will call 3rd party service and return external token / job ID or false if not received one.
	 */
	public function initiateRecognition(array $thumbnailUrls)
	{
		$headersArray = array('Authorization: ' . self::KEY);
		foreach ($thumbnailUrls as $second => $thumbnailUrl) {
			$formData = array('image_request[remote_image_url]' => $thumbnailUrl, 'image_request[locale]' => 'en-US');
			$handle = $this->getCurlHandle(self::RECOGNIZE_URI, $headersArray, $formData);
			$result = $this->execCurl($handle);
			if (isset($result['token'])) {
				$this->currentResults['$second'] = array('token'=>$result['token'], 'completed'=> false);
			}
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
		$headersArray = array('Authorization: ' . self::KEY);
		$gotAllResults = true;
		foreach($this->currentResults as $second=>$result) {
			if ($result['completed'] == false) {
				$handle = getCurlHandle(self::CHECK_URL . $result['token'], $headersArray);
				$result = execCurl($handle);
				if (!isset($result['status']) || $result['status'] == "not completed") {
					$gotAllResults = false;
				} else {
					if ($result['status'] == "completed") {
						$result['completed'] = true;
						$categories = array();
						if (isset($result['categories'])) {
							$categories = $result['categories'];
						}
						$names = $result['name'];
						if (!is_array($names)) {
							$names = array($names);
						}

						$this->finalResults[$second] = array_merge($names, $categories);
					}
				}
			}
		}

		if ($gotAllResults) {
			return $this->finalResults;
		}

		return false;
	}

	public function asyncCall() {
		return true;
	}
}
