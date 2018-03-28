<?php
/**
 * @package plugins.dailymotionDistribution
 * @subpackage lib
 */
class DailyMotionImpl 
{
	/**
	 * @var Dailymotion
	 */
	private $api = null;
	
	private $apiKey = "c53ca34fc66da3f98867";
	private $apiSecret = "aa8e888a2927dc1d54f2d1a0bd98ca51d1e65a98";
	private $user = "";
	private $pass = "";
	
	private static $categoriesMap = array ("animals" => "Animals", "creation" => "Arts", "auto" => "Auto Moto", "school" => "College", "shortfilms" => "Film and TV", "fun" => "Funny", "videogames" => "Gaming", "lifestyle" => "Life and Style", "music" => "Music", "news" => "News and Politics", "people" => "People and Family", "sexy" => "Sexy", "sport" => "Sports and Extreme", "tech" => "Tech and Science", "travel" => "Travel", "webcam" => "Webcam and Vlogs" );
	
	const NEW_API_END_POINT_URL = 'https://api.dailymotion.com';
	
	public function __construct($user, $pass) {
		$this->api = new Dailymotion ();
		$this->user = $user;
		$this->pass = $pass;
		$this->_connect ();
	}
	
	private function call($method, $args = array()) {
		KalturaLog::info ( "Call [$method] args [" . print_r ( $args, true ) . "]" );
		$result = $this->api->call ( $method, $args );
		KalturaLog::info ( "Result [" . print_r ( $result, true ) . "]" );
		
		return $result;
	}
	
	private function _connect() {
		$perms = array ();
		$perms [] = 'read';
		$perms [] = 'write';
		$perms [] = 'delete';
		$this->api->setGrantType ( Dailymotion::GRANT_TYPE_PASSWORD, $this->apiKey, $this->apiSecret, $perms, array ('username' => $this->user, 'password' => $this->pass ) );
		$result = $this->call ( 'auth.info' );
	}
	
	public function upload($file) {
		$url = $this->api->uploadFile ( $file );
		$result = $this->call ( 'video.create', array ('url' => $url ) );
		$remoteId = $result ['id'];
		return $remoteId;
	}
	
	public function update($id, $propsArray) {
		$dailymotionArray = array ('id' => $id );
		foreach ( $propsArray as $key => $value ) {
			if (! empty ( $key ) && (! empty ( $value ) || is_bool ( $value ))) {
				$dailymotionArray [$key] = $value;
			}
		}
		$this->call ( 'video.edit', $dailymotionArray );
	
	}
	
	public function delete($id) {
		$this->call ( 'video.delete', array ('id' => $id ) );
	}
	
	public function getStatus($id) {
		$result = $this->call ( 'video.status', array ('id' => $id ) );
		return $result ['status'];
	}
	
	public function setOption($option, $value) {
		if (! property_exists ( $this->api, $option ))
			throw new Exception ( 'The option "' . $option . '" doesn\'t exists for Dailymotion API Client Library' );
		
		$this->api->$option = $value;
	}
	
	public static function getCategoriesMap() {
		return self::$categoriesMap;
	}
	
	public function uploadSubtitle($remoteVideoId, KalturaDailymotionDistributionCaptionInfo $captionInfo, $filePath)
	{
		$url = $this->api->uploadFile ( $filePath );
		$args = array ();
		$args ['url'] = $url;
		$args ['language'] = $captionInfo->language;
		$args ['format'] = $this->getCaptionFormate ( $captionInfo->format );
		$tempApiEndpointUrl = $this->api->apiEndpointUrl;
		$this->api->apiEndpointUrl = self::NEW_API_END_POINT_URL;
		$response = $this->call ( "POST /video/$remoteVideoId/subtitles", $args );
		$this->api->apiEndpointUrl = $tempApiEndpointUrl;
		return $response['id'];
	}
	
	public function updateSubtitle($remoteSubtitleId, KalturaDailymotionDistributionCaptionInfo $captionInfo, $filePath)
	{
		$url = $this->api->uploadFile ( $filePath );
		$args = array ();
		$args ['url'] = $url;
		$args ['language'] = $captionInfo->language;
		$args ['format'] = $this->getCaptionFormate ( $captionInfo->format );
		$tempApiEndpointUrl = $this->api->apiEndpointUrl;
		$this->api->apiEndpointUrl = self::NEW_API_END_POINT_URL;
		$response = $this->call ( "POST /subtitle/$remoteSubtitleId", $args );
		$this->api->apiEndpointUrl = $tempApiEndpointUrl;
	}
	
	public function deleteSubtitle($remoteSubtitleId) {
		$tempApiEndpointUrl = $this->api->apiEndpointUrl;
		$this->api->apiEndpointUrl = self::NEW_API_END_POINT_URL;
		$response = $this->call ( "DELETE /subtitle/$remoteSubtitleId" );
		$this->api->apiEndpointUrl = $tempApiEndpointUrl;
	}
	
	private function getCaptionFormate($format) {
		switch ($format) {
			case KalturaDailymotionDistributionCaptionFormat::TT :
				return 'TT';
			case KalturaDailymotionDistributionCaptionFormat::SRT :
				return 'SRT';
			case KalturaDailymotionDistributionCaptionFormat::STL :
				return 'STL';
		}
	}
}

