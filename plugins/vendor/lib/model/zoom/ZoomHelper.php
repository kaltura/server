<?php
/**
 * @package plugins.venodr
 * @subpackage model.zoom
 */
class ZoomHelper
{
	/** API */
	const API_USERS_ME = '/v2/users/me';
	const API_PARTICIPANT = '/v2/report/meetings/@meetingId@/participants';
	const API_USERS_ME_PERMISSIONS = '/v2/users/me/permissions';

	/** payload data */
	const ACCOUNT_ID = "account_id";
	const PAYLOAD = 'payload';
	const DOWNLOAD_TOKEN = 'download_token';
	const MEETING = 'meeting';
	const HOST_EMAIL = 'host_email';
	const RECORDING_FILES = 'recording_files';
	const MP4 = 'MP4';
	const FILE_TYPE = 'file_type';
	const DOWNLOAD_URL = 'download_url';
	const MEETING_ID = 'id';
	const USER_EMAIL = 'user_email';
	const PARTICIPANTS = 'participants';
	/** php body */
	const PHP_INPUT = 'php://input';

	/**
	 * @param kuser $dbUser
	 * @param string $zoomCategory
	 * @param $emails
	 * @param $meetingId
	 * @return string
	 * @throws Exception
	 */
	public static function createEntryForZoom($dbUser, $zoomCategory, $emails, $meetingId)
	{
		$entry = new entry();
		$entry->setType(entryType::MEDIA_CLIP);
		$entry->setSourceType(EntrySourceType::URL);
		$entry->setMediaType(entry::ENTRY_MEDIA_TYPE_VIDEO);
		$entry->setName('Zoom_'. $meetingId);
		$entry->setPartnerId($dbUser->getPartnerId());
		$entry->setStatus(entryStatus::NO_CONTENT);
		$entry->setPuserId($dbUser->getPuserId());
		$entry->setKuserId($dbUser->getKuserId());
		$entry->setConversionProfileId(myPartnerUtils::getConversionProfile2ForPartner($dbUser->getPartnerId())->getId());
		$entry->setAdminTags('zoom');
		$entry->setCategories($zoomCategory);
		if ($emails)
		{
			foreach ($emails as $email)
			{
				kuserPeer::createUniqueKuserForPartner($dbUser->getPartnerId(), $email);
			}
			$entry->setEntitledPusersPublish(implode(",", array_unique($emails)));
		}
		$entry->save();
		return $entry->getId();
	}


	/**
	 * @param array $zoomUserPermissions
	 * @return bool
	 */
	public static function canConfigureEventSubscription($zoomUserPermissions)
	{
		if (in_array('Recording:Read', $zoomUserPermissions) && in_array('Recording:Edit', $zoomUserPermissions))
		{
			return true;
		}
		return false;
	}
	/**
	 * redirects to new URL
	 * @param $url
	 */
	public static function redirect($url)
	{
		$redirect  = new kRendererRedirect($url);
		$redirect->output();
		KExternalErrors::dieGracefully();
	}

	/**
	 * @param array $tokens
	 * @throws Exception
	 */
	public static function loadLoginPage($tokens)
	{
		$file_path = dirname(__FILE__) . "/../../api/webPage/zoom/kalturaZoomLoginPage.html";
		if (file_exists($file_path))
		{
			$page = file_get_contents($file_path);
			$tokensString = json_encode($tokens);
			$zoomConfiguration = kConf::get('ZoomAccount', 'vendor');
			$verificationToken = $zoomConfiguration['verificationToken'];
			list($enc, $iv) = AESEncrypt::encrypt($verificationToken, $tokensString);
			$page = str_replace('@BaseServiceUrl@', requestUtils::getHost(), $page);
			$page = str_replace('@encryptData@', base64_encode($enc), $page);
			$page = str_replace('@iv@', base64_encode($iv), $page);
			echo $page;
			die();
		}
	}

	/**
	 * @param ZoomVendorIntegration $zoomIntegration
	 * @param $accountId
	 * @param $ks
	 * @throws Exception
	 */
	public static function loadSubmitPage($zoomIntegration, $accountId, $ks)
	{
		$file_path = dirname(__FILE__) . "/../../api/webPage/zoom/KalturaZoomRegistrationPage.html";
		if (file_exists($file_path))
		{
			$page = file_get_contents($file_path);
			/** @noinspection PhpUndefinedMethodInspection */
			$page = str_replace('@ks@', $ks->getOriginalString(), $page);
			$page = str_replace('@BaseServiceUrl@', requestUtils::getHost(), $page);
			if (!is_null($zoomIntegration))
			{
				$page = str_replace('@defaultUserID@', $zoomIntegration->getDefaultUserEMail() , $page);
				$page = str_replace('@zoomCategory@', $zoomIntegration->getZoomCategory() ? $zoomIntegration->getZoomCategory()  : 'Zoom Recordings'  , $page);
			}
			else {
				$page = str_replace('@defaultUserID@', '' , $page);
				$page = str_replace('@zoomCategory@', 'Zoom Recordings' , $page);
			}
			$page = str_replace('@accountId@', $accountId , $page);
			echo $page;
			die();
		}
		throw new KalturaAPIException('unable to find submit page, please contact support');
	}

	/**
	 * @param $tokensDataAsArray
	 * @param $accountId
	 * @param ZoomVendorIntegration $zoomClientData
	 * @throws PropelException
	 */
	public static function saveNewTokenData($tokensDataAsArray, $accountId, $zoomClientData = null)
	{
		if (!$zoomClientData) // create new vendorIntegration during oauth first time
		{
			$zoomClientData = new ZoomVendorIntegration();
			$zoomClientData->setStatus(VendorStatus::DISABLED);
		}
		$zoomClientData->saveNewTokenData($tokensDataAsArray, $accountId);
	}


	/**
	 * verify headers tokens, if not equal die
	 * @throws Exception
	 */
	public static function verifyHeaderToken()
	{
		$headers = self::getAllHeaders();
		$zoomConfiguration = kConf::get('ZoomAccount', 'vendor');
		$verificationToken = $zoomConfiguration['verificationToken'];
		if ($verificationToken !== $headers['Authorization'])
		{
			KExternalErrors::dieGracefully('ZOOM - Received verification token is different from existing token');
		}
	}

	/**
	 * @param string $downloadURL
	 * @param string $downloadToken
	 * @return string
	 */
	public static function parseDownloadUrl($downloadURL, $downloadToken)
	{
		return $downloadURL . '?access_token=' . $downloadToken;
	}

	/**
	 * @param $data
	 * @return array
	 */
	public static function extractDataFromRecordingCompletePayload($data)
	{
		KalturaLog::debug('recordingcomplete data recived from zoom:');
		KalturaLog::debug(print_r($data, true));
		$payload = $data[self::PAYLOAD];
		$downloadToken = $data[self::DOWNLOAD_TOKEN];
		$accountId = $payload[self::ACCOUNT_ID];
		$meeting = $payload[self::MEETING];
		$hostEmail = $meeting[self::HOST_EMAIL];
		$recordingFiles = $meeting[self::RECORDING_FILES];
		$downloadURL = self::getDownloadUrl($recordingFiles);
		$meetingId = $meeting[self::MEETING_ID];
		return array($accountId, $downloadToken, $hostEmail, $downloadURL, $meetingId);
	}

	/**
	 * @param $data
	 * @return string
	 */
	public static function extractAccountIdFromDeAuthPayload($data)
	{
		$payload = $data[self::PAYLOAD];
		$accountId = $payload[self::ACCOUNT_ID];
		return $accountId;
	}

	/**
	 * @param $meetingId
	 * @param ZoomVendorIntegration $zoomIntegration
	 * @param $accountId
	 * @return array
	 * @throws Exception
	 */
	public static function extractCoHosts($meetingId, $zoomIntegration, $accountId)
	{
		$emails = array();
		$meetingApi = str_replace('@meetingId@', $meetingId, self::API_PARTICIPANT);
		list($tokens, $participants) = ZoomWrapper::retrieveZoomDataAsArray($meetingApi, false, $zoomIntegration->getTokens(), $accountId);
		if ($zoomIntegration->getAccessToken() !== $tokens[kZoomOauth::ACCESS_TOKEN])
		{
			// token changed -> refresh tokens
			self::saveNewTokenData($tokens, $accountId, $zoomIntegration);
		}
		if ($participants)
		{
			$participants = $participants[self::PARTICIPANTS];
			foreach ($participants as $participant)
			{
				if (isset($participant[self::USER_EMAIL]) && $participant[self::USER_EMAIL])
				{
					$emails[] = $participant[self::USER_EMAIL];
				}
			}
		}
		return $emails;
	}

	/**
	 * @return array
	 */
	private static function getAllHeaders()
	{
		if (!function_exists('getallheaders'))
		{
			$headers = array();
			foreach ($_SERVER as $name => $value)
			{
				/* RFC2616 (HTTP/1.1) defines header fields as case-insensitive entities. */
				if (strtolower(substr($name, 0, 5)) == 'http_')
				{
					$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
				}
			}
			return $headers;
		}
		else
		{
			return getallheaders();
		}
	}

	/**
	 * @param array $recordingFiles
	 * @return string
	 */
	private static function getDownloadUrl($recordingFiles)
	{
		$downloadURL = '';
		foreach ($recordingFiles as $recordingFile) {
			if ($recordingFile[self::FILE_TYPE] === self::MP4)
			{
				$downloadURL = $recordingFile[self::DOWNLOAD_URL];
			}
		}
		if (!$downloadURL)
		{
			KExternalErrors::dieGracefully('Zoom - MP4 downland url was not found');
		}
		return $downloadURL;
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public static function getPayloadData()
	{
		$request_body = file_get_contents(self::PHP_INPUT);
		$data = json_decode($request_body, true);
		return $data;
	}

}