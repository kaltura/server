<?php

require_once KALTURA_ROOT_PATH.'/vendor/facebook-sdk-php-v5-customed/autoload.php';

/**
 *  @package infra
 *  @subpackage general
 */
class FacebookGraphSdkUtils
{
	const MAX_VIDEO_SIZE = 1750000000; //bytes
	const MAX_VIDEO_DURATION = 2700; //seconds

	/**
	 *
	 * Returns facebook long-lived access token (valid for 60 days)
	 *
	 * @param string $appId
	 * @param string $appSecret
	 * @param $dataHandler can be string (memory/session) or implementation of PersistentDataInterface
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
			KalturaLog::debug('getting long lived access token for '.$accessTokenValue);
			$oAuth2Client = $fb->getOAuth2Client();
			$longLivedaccessToken = $oAuth2Client->getLongLivedAccessToken($userAccessToken);
			if(isset($longLivedaccessToken))
				$accessTokenValue = $longLivedaccessToken->getValue();
			else
				$accessTokenValue = null;
		}
		return $accessTokenValue;
	}

	/**
	 *
	 * Get user access token
	 * @param string $appId
	 * @param string $appSecret
	 * @param $dataHandler can be string or implementation of PersistentDataInterface
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
	 * @param unknown_type $appId
	 * @param unknown_type $appSecret
	 * @param unknown_type $userAccessToken
	 * @param unknown_type $pageId
	 * @param Facebook\PersistentData\PersistentDataInterface can be string or implementation of PersistentDataInterface
	 * @throws Facebook\Exceptions\FacebookResponseException, Facebook\Exceptions\FacebookSDKException, Exception
	 */
	public static function getPageAccessToken($appId, $appSecret, $userAccessToken, $pageId, $dataHandler)
	{
		$fb = self::createFacebookInstance($appId, $appSecret, $dataHandler);
		KalturaLog::debug('Getting page access token');

		// Returns a `Facebook\FacebookResponse` object
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
	}

	/**
	 *
	 * Get facebook login url
	 * @param string $appId
	 * @param string $appSecret
	 * @param string $redirectUrl
	 * @param string $permissions
	 * @param $dataHandler can be string or implementation of PersistentDataInterface
	 * @param bool|unknown_type $reRequestPermissions
	 * @return null|string
	 */
	public static function getLoginUrl($appId, $appSecret, $redirectUrl, $permissions, $dataHandler, $reRequestPermissions = false)
	{
		$fb = self::createFacebookInstance($appId, $appSecret, $dataHandler);
		$loginHelper = $fb->getRedirectLoginHelper();
		$loginUrl = null;
		if($reRequestPermissions) {
			KalturaLog::debug("Generating facebook re authenticate URL ".print_r($permissions, true));
			$loginUrl = $loginHelper->getReRequestUrl($redirectUrl, $permissions);
		} else {
			KalturaLog::debug("Generating facebook login URL");
			$loginUrl = $loginHelper->getLoginUrl($redirectUrl, $permissions);
		}

		KalturaLog::debug('facebook login URL: '.$loginUrl);

		return $loginUrl;
	}

	/**
	 *
	 * Upload video to facebook using resumable API
	 * @param unknown_type $appId
	 * @param unknown_type $appSecret
	 * @param unknown_type $entityId
	 * @param unknown_type $accessToken
	 * @param unknown_type $filePath
	 * @param unknown_type $fileSize
	 * @param unknown_type $baseWorkingDir
	 * @throws Facebook\Exceptions\FacebookResponseException, Facebook\Exceptions\FacebookSDKException, Exception
	 */
	public static function uploadVideo($appId, $appSecret, $entityId, $accessToken, $filePath, $fileSize, $baseWorkingDir, $metadata = array())
	{
		$fb = self::createFacebookInstance($appId, $appSecret);
		$dirName = basename($filePath, '.'.pathinfo($filePath, PATHINFO_EXTENSION));
		$workingDir = $baseWorkingDir.DIRECTORY_SEPARATOR.$dirName;
		try
		{

			$uploadStartData = self::startVideoUploadSession($fb, $accessToken, $entityId, $fileSize);
			$startOffset = $uploadStartData['start_offset'];
			$endOffset = $uploadStartData['end_offset'];

			while($startOffset < $endOffset)
			{
				$transferData = self::transferVideoChunk($fb, $accessToken, $uploadStartData['upload_session_id'], $startOffset, $endOffset, $filePath, $workingDir);
				$startOffset = $transferData['start_offset'];
				$endOffset = $transferData['end_offset'];
			}

			self::finishVideoUpload($fb, $accessToken, $uploadStartData['upload_session_id'], $metadata);

			return $uploadStartData['video_id '];
		}
		catch(Facebook\Exceptions\FacebookResponseException $e)
		{
			KalturaLog::err('Graph returned an error: ' . $e->getMessage());
		}
		catch(Facebook\Exceptions\FacebookSDKException $e)
		{
			KalturaLog::err('Facebook SDK returned an error: ' . $e->getMessage());
		}

		return null;
	}

	public static function uploadCaptions($appId, $appSecret, $accessToken, $videoId, $filePath, $locale, $baseWorkingDir)
	{
		$fb = self::createFacebookInstance($appId, $appSecret);

		//create file name in format: filename.locale.srt
		$newFilePath = basename($filePath, '.'.pathinfo($filePath, PATHINFO_EXTENSION)).'.'.$locale.'.srt';
		copy($filePath, $newFilePath);

		$data = array(
			'id' => $videoId,
			'default_locale' => 'none',
			'video_file_chunk' => $fb->videoToUpload($newFilePath),
		);

		$response = $fb->post('/me/captions', $data, $accessToken);
		$graphNode = $response->getGraphNode();
		$success = $graphNode['success'];
		if($success)
			KalturaLog::debug('Captions file ['. $filePath. '] uploaded successfully');
		else
			KalturaLog::debug('Captions file ['. $filePath. '] upload failed');
		return $success;
	}

	public static function deleteCaptions($appId, $appSecret, $accessToken, $videoId, $locale)
	{
		$fb = self::createFacebookInstance($appId, $appSecret);

		$data = array(
			'id' => $videoId,
			'locale' => $locale,
		);

		$response = $fb->delete('/me/captions', $data, $accessToken);
		$graphNode = $response->getGraphNode();
		$success = $graphNode['success'];
		if($success)
			KalturaLog::debug('Captions file deleted successfully');
		else
			KalturaLog::debug('Captions file delete failed');
		return $success;
	}

	public static function isValidVideo($filePath, $fileSize, $duration, $width, $heigth)
	{
		if($fileSize > self::MAX_VIDEO_SIZE)
		{
			throw new Exception('File size too large');
		}
		if($duration > self::MAX_VIDEO_DURATION)
		{
			throw new Exception('File duration is too long');
		}
		$mimetypes = Facebook\FileUpload\Mimetypes::getInstance();
		$type = $mimetypes->fromFilename(basename($filePath));
		if(!$type)
		{
			throw new Exception('Invalid file format');
		}

		//TODO validate aspect ratio
	}

	/**
	 *
	 * Get user access token
	 * @param unknown_type $fb
	 * @param unknown_type $appId
	 * @param unknown_type $permissions
	 * @throws Facebook\Exceptions\FacebookResponseException, Facebook\Exceptions\FacebookSDKException, Exception
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
				$errorMessage = "Error: " . $loginHelper->getError() . " Error Code: " . $loginHelper->getErrorCode() .
					" Error Reason: " . $loginHelper->getErrorReason() .  " Error Description: " . $loginHelper->getErrorDescription();
				KalturaLog::err($errorMessage);
				throw new Exception($errorMessage);
			}
			else
			{
				KalturaLog::err($errorMessage);
				throw new Exception($errorMessage);
			}
		}

		KalturaLog::debug('User access token: ' . $accessToken->getValue(). ' expiration: '.print_r($accessToken->getExpiresAt(),true));

		self::doValidateAccessToken($fb, $appId, $accessToken, $permissions);
		return $accessToken;
	}

	/**
	 *
	 * Validate the token has the required permissions
	 *
	 * @param string $fb
	 * @param string $appId
	 * @param string $accessToken
	 * @param array $permissions
	 * @return bool|null
	 * @throws Facebook\Exceptions\FacebookResponseException, Facebook\Exceptions\FacebookSDKException, Exception
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

	private static function startVideoUploadSession($fb, $accessToken, $entityId, $fileSize)
	{
		$data = array(
			'id' => $entityId,
			'upload_phase' => 'start',
			'file_size' => $fileSize,
		);

		$response = $fb->post('/me/videos', $data, $accessToken);
		$graphNode = $response->getGraphNode();
		KalturaLog::debug(print_r($graphNode, true));
		return $graphNode;
	}

	private static function transferVideoChunk($fb, $accessToken, $sessionId, $startOffset, $endOffset, $filePath, $workingDir)
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

		$response = $fb->post('/me/videos', $data, $accessToken);
		$graphNode = $response->getGraphNode();
		KalturaLog::debug(print_r($graphNode, true));
		return $graphNode;
	}

	private static function finishVideoUpload($fb, $accessToken, $sessionId, $data)
	{
		$data['upload_phase'] = 'finish';
		$data['upload_session_id'] = $sessionId;

		$response = $fb->post('/me/videos', $data, $accessToken);
		$graphNode = $response->getGraphNode();
		KalturaLog::debug(print_r($graphNode, true));
		return $graphNode;
	}

	/**
	 * Retuns a new Facebook client using teh facebook sdk using the arguments
	 * @param $appId
	 * @param $appSecret
	 * @param string $dataHandler
	 * @return \Facebook\Facebook
	 */
	public static function createFacebookInstance($appId, $appSecret, $dataHandler = "session"){
		return new Facebook\Facebook(
			array (
				'app_id' => $appId,
				'app_secret' => $appSecret,
				'default_graph_version' => 'v2.4',
				'default_access_token' => 'APP-ID|APP-SECRET',
				'persistent_data_handler' => $dataHandler,
			));
	}

}