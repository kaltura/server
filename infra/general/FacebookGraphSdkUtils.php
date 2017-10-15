<?php

require_once KALTURA_ROOT_PATH.'/vendor/facebook-sdk-php-v5-customized/autoload.php';

/**
 *  This class is a helper class for the use of Facebook's PHP client (see location in the require php file)
 *
 *  @package infra
 *  @subpackage general
 */
class FacebookGraphSdkUtils
{
	/**
	 * Returns facebook long-lived access token (valid for 60 days)
	 * @param string $appId
	 * @param string $appSecret
	 * @param Facebook\PersistentData\PersistentDataInterface|string $dataHandler
	 * @param array $permissions - required permissions to be granted by the user to the app
	 * @return null|string the access token created
	 * @throws Facebook\Exceptions\FacebookResponseException, Facebook\Exceptions\FacebookSDKException, Exception
	 */
	public static function getLongLivedUserAccessToken($appId, $appSecret, $dataHandler, $permissions = array())
	{
		$fb = self::createFacebookInstance($appId, $appSecret, $dataHandler);

		$userAccessToken = self::doGetUserAccessToken($fb, $appId, $permissions);
		if(!isset($userAccessToken))
			return null;

		$accessTokenValue = $userAccessToken->getValue();

		if (!$userAccessToken->isLongLived())
		{
			// Exchanges a short-lived access token for a long-lived one
			KalturaLog::debug('Getting long lived access token for '.$accessTokenValue);
			$oAuth2Client = $fb->getOAuth2Client();
			$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($userAccessToken);
			if(isset($longLivedAccessToken))
				$accessTokenValue = $longLivedAccessToken->getValue();
			else
				$accessTokenValue = null;
		}
		return $accessTokenValue;
	}

	/**
	 * Get user access token
	 * @param string $appId
	 * @param string $appSecret
	 * @param Facebook\PersistentData\PersistentDataInterface|string $dataHandler
	 * @param array $permissions
	 * @return null
	 * @throws Facebook\Exceptions\FacebookResponseException, Facebook\Exceptions\FacebookSDKException, Exception
	 */
	public static function getUserAccessToken($appId, $appSecret, $dataHandler, $permissions = array())
	{
		$fb = self::createFacebookInstance($appId, $appSecret, $dataHandler);
		$userAccessToken = self::doGetUserAccessToken($fb, $appId, $permissions);
		if(!isset($userAccessToken))
			return null;

		return $userAccessToken->getValue();
	}

	/**
	 *
	 * Validate access token permissions
	 * @param string $appId
	 * @param string $appSecret
	 * @param string $accessToken
	 * @param array $permissions
	 * @return null|bool
	 * @throws Facebook\Exceptions\FacebookResponseException, Facebook\Exceptions\FacebookSDKException, Exception
	 */
	public static function validateAccessToken($appId, $appSecret, $accessToken, $permissions = array())
	{
		$fb = self::createFacebookInstance($appId, $appSecret);
		return self::doValidateAccessToken($fb, $appId, $accessToken, $permissions);
	}

	/**
	 *
	 * get page access token
	 * @param string $appId
	 * @param string $appSecret
	 * @param string $userAccessToken
	 * @param string $pageId
	 * @param Facebook\PersistentData\PersistentDataInterface|string
	 * @return string access_token
	 * @throws Exception
	 */
	public static function getPageAccessToken($appId, $appSecret, $userAccessToken, $pageId, $dataHandler)
	{
		$fb = self::createFacebookInstance($appId, $appSecret, $dataHandler);

		$response = $fb->get('/me/accounts?fields=id,access_token', $userAccessToken);
		KalturaLog::debug("page token response:".print_r($response, true));

		$pages = $response->getGraphEdge();
		foreach ($pages as $page)
		{
			if($page['id'] == $pageId)
			{
				KalturaLog::debug('Found token for page Id: '.$pageId);
				return $page['access_token'];
			}
		}
		throw new Exception("Failed to find access token for given page id :".$pageId);
	}

	/**
	 *
	 * Get facebook login url
	 * @param string $appId
	 * @param string $appSecret
	 * @param string $redirectUrl
	 * @param string $permissions
	 * @param Facebook\PersistentData\PersistentDataInterface|string $dataHandler
	 * @param bool $reRequestPermissions
	 * @return null|string
	 */
	public static function getLoginUrl($appId, $appSecret, $redirectUrl, $permissions, $dataHandler, $reRequestPermissions = false)
	{
		$fb = self::createFacebookInstance($appId, $appSecret, $dataHandler);
		$loginHelper = $fb->getRedirectLoginHelper();
		$loginUrl = null;
		if($reRequestPermissions) {
			$loginUrl = $loginHelper->getReRequestUrl($redirectUrl, $permissions);
		} else {
			$loginUrl = $loginHelper->getLoginUrl($redirectUrl, $permissions);
		}
		return $loginUrl;
	}

	/**
	 *
	 * Upload video to facebook using transfer video chunks API
	 * @param string $appId
	 * @param string $appSecret
	 * @param string $pageId
	 * @param string $accessToken
	 * @param string $videoFilePath
	 * @param string $thumbFilePath
	 * @param int $videoFileSize
	 * @param string $baseWorkingDir
	 * @param array $metadata
	 * @return videoId created or throws exception in case of failure
	 * @throws Exception
	 */
	public static function uploadVideo($appId, $appSecret, $pageId, $accessToken, $videoFilePath, $thumbFilePath ,$videoFileSize, $baseWorkingDir, $metadata = array())
	{
		if ($thumbFilePath)
			$metadata['thumb'] = new \Facebook\FileUpload\FacebookFile($thumbFilePath);
		$fb = self::createFacebookInstance($appId, $appSecret);
		$dirName = basename($videoFilePath, '.' . pathinfo($videoFilePath, PATHINFO_EXTENSION));
		$workingDir = $baseWorkingDir . DIRECTORY_SEPARATOR . $dirName;
		try {

			$uploadStartData = self::startVideoUploadSession($fb, $pageId, $accessToken, $videoFileSize);
			$startOffset = $uploadStartData['start_offset'];
			$endOffset = $uploadStartData['end_offset'];

			while ($startOffset < $endOffset) {
				$transferData = self::transferVideoChunk($fb, $accessToken, $uploadStartData['upload_session_id'], $startOffset, $endOffset, $videoFilePath, $workingDir, $pageId);
				$startOffset = $transferData['start_offset'];
				$endOffset = $transferData['end_offset'];
			}

			$uploadFinishData = self::finishVideoUpload($fb, $accessToken, $uploadStartData['upload_session_id'], $metadata, $pageId);
			if ($uploadFinishData['success'] != 1) {
				$failureReason = "Graph success status was not 1 but ".$uploadFinishData['success'];
			} else {
				return $uploadStartData['video_id'];
			}
		} catch (Facebook\Exceptions\FacebookResponseException $e) {
			$failureReason = 'Graph returned an error: ' . $e->getMessage();
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
			$failureReason = 'Facebook SDK returned an error: ' . $e->getMessage();
		}
		throw new Exception("Failed to upload video to facebook due to : ".$failureReason);

	}

	/**
	 * Uploads an image to facebook from URL
	 * @param string $appId
	 * @param string $appSecret
	 * @param string $accessToken
	 * @param string $pageId
	 * @param string $url of the image
	 * @return mixed
	 * @throws Exception
	 */
	public static function uploadPhoto($appId, $appSecret, $accessToken, $pageId, $url)
	{
		try{
			$fb = self::createFacebookInstance($appId, $appSecret);

			$data = array(
				'url' => $url
			);

			$response = $fb->post("/".$pageId."/photos", $data, $accessToken);
			$graphNode = $response->getGraphNode();
			if (array_key_exists('id', $graphNode->asArray()) ){
				$photoCreatedId = $graphNode['id'];
				return $photoCreatedId;
			} else {
				$failureReason = "Failed to upload photo - response from server was: ".print_r($response, true);
			}
		} catch (Facebook\Exceptions\FacebookResponseException $e) {
			$failureReason = 'Graph returned an error: ' . $e->getMessage();
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
			$failureReason = 'Facebook SDK returned an error: ' . $e->getMessage();
		}
		throw new Exception("Failed to upload photo to facebook due to : ".$failureReason);
	}

	/**
	 * @param $appId
	 * @param $appSecret
	 * @param $accessToken
	 * @param $videoId
	 * @param $filePath
	 * @param $locale
	 * @param $tempDirectory
	 * @return mixed
	 * @throws Exception
	 */
	public static function uploadCaptions($appId, $appSecret, $accessToken, $videoId, $filePath, $locale, $tempDirectory)
	{
		if (!file_exists($filePath))
			throw new Exception("Captions file given does not exist: ".$filePath);
		//create file name in format: filename.locale.srt
		$newFilePath = $tempDirectory.'/'.basename($filePath, '.'.pathinfo($filePath, PATHINFO_EXTENSION)).'.'.$locale.'.srt';
		copy($filePath, $newFilePath);
		$data = array (
			'captions_file' => new FacebookCaptionsFile($newFilePath),
		);
		self::helperChangeVideo($appId, $appSecret, $accessToken, $data, $videoId, false, "/captions" );
	}

	/**
	 * @param string $appId
	 * @param string $appSecret
	 * @param string $accessToken
	 * @param string $videoId
	 * @param string $locale
	 * @throws Exception
	 */
	public static function deleteCaptions($appId, $appSecret, $accessToken, $videoId, $locale)
	{
		$data = array(
			'id' => $videoId,
			'locale' => $locale,
		);
		self::helperChangeVideo($appId, $appSecret, $accessToken, $data, $videoId, true, "/captions" );
	}

	/**
	 * @param string $filePath on disk
	 * @param int $fileSize
	 * @param int $duration
	 * @throws Exception
	 */
	public static function validateVideoAttributes($filePath, $fileSize, $duration)
	{
		if($fileSize > FacebookConstants::MAX_VIDEO_SIZE)
			throw new Exception("File size too large - got ".$fileSize." MAX defined is: ".FacebookConstants::MAX_VIDEO_SIZE);
		if($duration > FacebookConstants::MAX_VIDEO_DURATION)
			throw new Exception("File duration is too long - got ".$duration." MAX defined is: ".FacebookConstants::MAX_VIDEO_DURATION);

		$mimetypes = Facebook\FileUpload\Mimetypes::getInstance();
		$type = $mimetypes->fromFilename(basename($filePath));
		if(!$type)
			throw new Exception('Invalid file format');

	}

	/**
	 * Get user access token
	 * @param Facebook/Facebook $fb facebook client
	 * @param string $appId
	 * @param array $permissions
	 * @return accessToken
	 * @throws Exception
	 */
	private static function doGetUserAccessToken($fb, $appId, $permissions = array())
	{
		$loginHelper = $fb->getRedirectLoginHelper();
		$accessToken = $loginHelper->getAccessToken();
		if (! isset($accessToken))
		{
			$errorMessage = 'Failed to get access token';
			if ($loginHelper->getError())
			{
				$errorMessage = "Error: ".$loginHelper->getError()." Error Code: ".$loginHelper->getErrorCode() .
					" Error Reason: ".$loginHelper->getErrorReason()." Error Description: ".$loginHelper->getErrorDescription();
				KalturaLog::err($errorMessage);
				throw new Exception($errorMessage);
			}
			else
			{
				KalturaLog::err($errorMessage);
				throw new Exception($errorMessage);
			}
		}
		KalturaLog::debug('User access token: '.$accessToken->getValue().' expiration: '.print_r($accessToken->getExpiresAt(),true));
		self::doValidateAccessToken($fb, $appId, $accessToken, $permissions);
		return $accessToken;
	}

	/**
	 * Validate the token has the required permissions
	 * @param string $fb
	 * @param string $appId
	 * @param string $accessToken
	 * @param array $permissions
	 * @return bool|null
	 * @throws Exception
	 */
	private static function doValidateAccessToken($fb, $appId, $accessToken, $permissions = array())
	{
		KalturaLog::debug('Validating user access token: '.$accessToken->getValue());

		$oAuth2Client = $fb->getOAuth2Client();
		$tokenMetadata = $oAuth2Client->debugToken($accessToken);
		KalturaLog::debug('token metadata: '.print_r($tokenMetadata, true));

		$tokenMetadata->validateAppId($appId);
		$grantedPermissions = $tokenMetadata->getScopes();
		foreach ($permissions as $permission)
		{
			if(!in_array($permission, $grantedPermissions))
			{
				$errorMessage = 'Token missing required permission ['.$permission.']';
				KalturaLog::debug($errorMessage);
				throw new Exception($errorMessage);
			}
		}
		KalturaLog::debug('Token is valid');
		return true;
	}

	private static function startVideoUploadSession($fb, $pageId, $accessToken, $fileSizeInBytes)
	{
		$data = array(
			'upload_phase' => 'start',
			'file_size' => $fileSizeInBytes,
		);

		$response = $fb->post("/".$pageId."/videos", $data, $accessToken);
		$graphNode = $response->getGraphNode();
		return $graphNode;
	}

	private static function transferVideoChunk($fb, $accessToken, $sessionId, $startOffset, $endOffset, $filePath, $workingDir, $pageId)
	{
		$chunkContent = kFile::getFileContent($filePath, $startOffset, $endOffset);
		$chunkFilePath = $workingDir.DIRECTORY_SEPARATOR.'file_'.$startOffset;
		kFile::setFileContent($chunkFilePath, $chunkContent);

		$data = array(
			'upload_phase' => 'transfer',
			'start_offset' => $startOffset,
			'video_file_chunk' => $fb->videoToUpload($chunkFilePath),
			'upload_session_id' => $sessionId,
		);

		$response = $fb->post("/".$pageId."/videos", $data, $accessToken);
		$graphNode = $response->getGraphNode();
		return $graphNode;
	}

	private static function finishVideoUpload($fb, $accessToken, $sessionId, $data, $pageId)
	{
		$data['upload_phase'] = 'finish';
		$data['upload_session_id'] = $sessionId;
		$response = $fb->post("/".$pageId."/videos", $data, $accessToken);
		$graphNode = $response->getGraphNode();
		return $graphNode;
	}

	/**
	 * Deletes the video ID from facebook
	 * @param $appId
	 * @param $appSecret
	 * @param $accessToken
	 * @param $videoId
	 * @return bool on success of the operation
	 */
	public static function deleteUploadedVideo($appId, $appSecret, $accessToken, $videoId)
	{
		self::helperChangeVideo($appId, $appSecret, $accessToken, array(), $videoId, true);
	}

	/**
	 * Updates the given video (by id) with the given data
	 * @param string $appId
	 * @param string $appSecret
	 * @param string $accessToken
	 * @param array $data metadata to update
	 * @param string $videoId video to update
	 * @return bool on success of the operation
	 */
	public static function updateUploadedVideo($appId, $appSecret, $accessToken, $data, $videoId)
	{
		self::helperChangeVideo($appId, $appSecret, $accessToken, $data, $videoId, false);
	}

	private static function helperChangeVideo($appId, $appSecret, $accessToken, $data, $videoId, $isDelete, $subCategory=null)
	{
		$fb = self::createFacebookInstance($appId, $appSecret);
		if ($isDelete)
			$response = $fb->delete("/".$videoId.$subCategory , $data, $accessToken);
		else
			$response = $fb->post("/".$videoId.$subCategory , $data, $accessToken);
		$graphNode = $response->getGraphNode();
		if ($graphNode['success'] != 1)
			throw new Exception("Failed to ".($isDelete? "delete " : "update ").$subCategory." video ".$videoId);
	}

	public static function updateTags($appId, $appSecret, $accessToken, $tags, $videoId)
	{
		$fb = self::createFacebookInstance($appId, $appSecret);
		foreach ($tags as $tag)
		{
			$data = array('tag_uid' => $tag);
			$response = $fb->post("/" . $videoId . "/tags", $data, $accessToken);
			$graphNode = $response->getGraphNode();
			if ($graphNode['success'] != 1)
				throw new Exception("Failed to add tag ".$tag." to video id ".$videoId);
		}
	}

	/**
	 * Retuns a new Facebook client using the facebook sdk using the arguments
	 * @param string $appId
	 * @param string $appSecret
	 * @param Facebook\PersistentData\PersistentDataInterface|string $dataHandler
	 * @return \Facebook\Facebook
	 */
	public static function createFacebookInstance($appId, $appSecret, $dataHandler = "session"){
		return new Facebook\Facebook(
			array (
				'app_id' => $appId,
				'app_secret' => $appSecret,
				'default_graph_version' => FacebookConstants::FACEBOOK_SDK_VERSION,
				'default_access_token' => 'APP-ID|APP-SECRET',
				'persistent_data_handler' => $dataHandler,
			));
	}
}

class FacebookConstants
{
	const MAX_VIDEO_SIZE = 1750000000; //bytes
	const MAX_VIDEO_DURATION = 2700000; //milliseconds
	const FACEBOOK_SDK_VERSION = 'v2.5';
	const FACEBOOK_MIN_POSTPONE_POST_IN_SECONDS = 600; // 10 minutes
	const FACEBOOK_MAX_POSTPONE_POST_IN_SECONDS = 15552000; // 6 months

	const FACEBOOK_APP_ID_REQUEST_PARAM = 'app_id';
	const FACEBOOK_APP_SECRET_REQUEST_PARAM = 'app_secret';
	const FACEBOOK_PAGE_ID_REQUEST_PARAM = 'page_id';
	const FACEBOOK_RE_REQUEST_PERMISSIONS_REQUEST_PARAM = 're_request_permissions';
	const FACEBOOK_PERMISSIONS_REQUEST_PARAM = 'permissions';
	const FACEBOOK_PROVIDER_ID_REQUEST_PARAM = 'provider_id';
	const FACEBOOK_PARTNER_ID_REQUEST_PARAM = 'partner_id';
	const FACEBOOK_NEXT_ACTION_REQUEST_PARAM = 'next_action';
	const FACEBOOK_KS_REQUEST_PARAM = 'ks';
}

class FacebookCaptionsFile extends \Facebook\FileUpload\FacebookFile
{

	/**
	 * override the original method since srt is not amongst the known file types in the facebook Mimetypes
	 */
	public function getMimetype()
	{
		return 'application/octet-stream';
	}
}