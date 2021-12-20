<?php
/**
 * @package plugins.ZoomDropFolder
 */

abstract class zoomProcessor
{
	const ONE_DAY_IN_SECONDS = 86400;
	const ZOOM_PREFIX = 'Zoom_';
	const ZOOM_LOCK_TTL = 120;
	const URL_ACCESS_TOKEN = '?access_token=';
	const REFERENCE_FILTER = '_eq_reference_id';
	const CMS_USER_FIELD = 'cms_user_id';
	const MAX_PUSER_LENGTH = 100;
	const EMAIL = 'email';
	
	/**
	 * @var kZoomClient
	 */
	protected $zoomClient;
	
	protected $dropFolder;
	
	/**
	 * zoomProcessor constructor.
	 * @param string $zoomBaseUrl
	 * @param KalturaZoomDropFolder $folder
	 */
	public function __construct($zoomBaseUrl, KalturaZoomDropFolder $folder)
	{
		$jwtToken = isset($folder->jwtToken) ? $folder->jwtToken : null;
		$refreshToken = isset($folder->refreshToken) ? $folder->refreshToken : null;
		$clientId = isset($folder->clientId) ? $folder->clientId : null;
		$clientSecret = isset($folder->clientSecret) ? $folder->clientSecret : null;
        $accessToken = isset($folder->accessToken) ? $folder->accessToken : null;
        $expiresIn = isset($folder->expiresIn) ? $folder->expiresIn : null;
		$this->zoomClient = new kZoomClient($zoomBaseUrl, $jwtToken, $refreshToken, $clientId, $clientSecret, $accessToken, $expiresIn);
		$this->dropFolder = $folder;
	}
	
	/**
	 * @param string $userName
	 * @return string kalturaUserName
	 */
	protected function processZoomUserName($userName)
	{
		$result = $userName;
		switch ($this->dropFolder->zoomVendorIntegration->zoomUserMatchingMode)
		{
			case kZoomUsersMatching::ADD_POSTFIX:
				$postFix = $this->dropFolder->zoomVendorIntegration->zoomUserPostfix;
				if (!kString::endsWith($result, $postFix, false))
				{
					$result = $result . $postFix;
				}
				
				break;
			case kZoomUsersMatching::REMOVE_POSTFIX:
				$postFix = $this->dropFolder->zoomVendorIntegration->zoomUserPostfix;
				if (kString::endsWith($result, $postFix, false))
				{
					$result = substr($result, 0, strlen($result) - strlen($postFix));
				}
				
				break;
			case kZoomUsersMatching::CMS_MATCHING:
				$zoomUser = $this->zoomClient->retrieveZoomUser($userName);
				if(isset($zoomUser[self::CMS_USER_FIELD]) && !empty($zoomUser[self::CMS_USER_FIELD]))
				{
					$result = $zoomUser[self::CMS_USER_FIELD];
				}
				break;
			case kZoomUsersMatching::DO_NOT_MODIFY:
			default:
				break;
		}
		
		return $result;
	}
	
	/**
	 * @param $recordingUuId
	 * @param $recordingPartnerId
	 * @return KalturaMediaEntry
	 * @throws PropelException
	 */
	protected function getZoomEntryByRecordingId($recordingUuId, $recordingPartnerId)
	{
		$entryPager = new KalturaFilterPager();
		$entryPager->pageSize = 1;
		$entryPager->pageIndex = 1;
		
		$entryFilter = new KalturaBaseEntryFilter();
		$entryFilter->referenceIdEqual = self::ZOOM_PREFIX . $recordingUuId;
		
		KBatchBase::impersonate($recordingPartnerId);
		$kalturaEntry = KBatchBase::$kClient->baseEntry->listAction($entryFilter, $entryPager);
		KBatchBase::unimpersonate();
		
		if($kalturaEntry->objects)
		{
			KalturaLog::debug('Found entry:' . $kalturaEntry->objects[0]->id);
		}
		return $kalturaEntry->objects;
	}
	
	/**
	 * @param string $hostEmail
	 * @return string
	 */
	protected function getEntryOwnerId($hostEmail)
	{
		$partnerId = $this->dropFolder->partnerId;
		$zoomUser = new kZoomUser();
		$zoomUser->setOriginalName($hostEmail);
		$zoomUser->setProcessedName($this->processZoomUserName($hostEmail));
		KBatchBase::impersonate($partnerId);
		/* @var $user KalturaUser */
		$user = $this->getKalturaUser($partnerId, $zoomUser);
		KBatchBase::unimpersonate();
		$userId = '';
		if ($user)
		{
			$userId = $user->id;
		}
		else
		{
			if ($this->dropFolder->zoomVendorIntegration->createUserIfNotExist)
			{
				$userId = $zoomUser->getProcessedName();
			}
			else if ($this->dropFolder->zoomVendorIntegration->defaultUserId)
			{
				$userId = $this->dropFolder->zoomVendorIntegration->defaultUserId;
			}
		}
		return $userId;
	}
	
	/**
	 * @param int $partnerId
	 * @param kZoomUser $kZoomUser
	 * @return kuser
	 */
	protected function getKalturaUser($partnerId, $kZoomUser)
	{
		$pager = new KalturaFilterPager();
		$pager->pageSize = 1;
		$pager->pageIndex = 1;
		
		$filter = new KalturaUserFilter();
		$filter->partnerIdEqual = $partnerId;
		$filter->idEqual = $kZoomUser->getProcessedName();
		$kalturaUser = KBatchBase::$kClient->user->listAction($filter, $pager);
		if (!$kalturaUser->objects)
		{
			$email = $kZoomUser->getOriginalName();
			$filterUser = new KalturaUserFilter();
			$filterUser->partnerIdEqual = $partnerId;
			$filterUser->emailStartsWith = $email;
			$kalturaUser = KBatchBase::$kClient->user->listAction($filterUser, $pager);
			if (!$kalturaUser->objects || strcasecmp($kalturaUser->objects[0]->email, $email) != 0)
			{
				return null;
			}
		}
		
		if($kalturaUser->objects)
		{
			return $kalturaUser->objects[0];
		}
		return null;
	}
	
	protected function createNewUser($partnerId, $puserId)
	{
		if (!is_null($puserId))
		{
			$puserId = substr($puserId, 0, self::MAX_PUSER_LENGTH);
		}
		
		$user = new KalturaUser();
		$user->id = $puserId;
		$user->screenName = $puserId;
		$user->firstName = $puserId;
		$user->isAdmin = false;
		$user->type = KalturaUserType::USER;
		$kalturaUser = KBatchBase::$kClient->user->add($user);
		return $kalturaUser;
	}
	
	protected function getRedirectUrl($recording)
	{
		$url = null;
		$redirectUrl = null;
		if (isset($recording->recordingFile->downloadToken))
		{
			$redirectUrl = $recording->recordingFile->downloadUrl . self::URL_ACCESS_TOKEN . $recording->recordingFile->downloadToken;
		}
		else if (isset($this->dropFolder->accessToken))
		{
			$url = $recording->recordingFile->downloadUrl . self::URL_ACCESS_TOKEN . $this->dropFolder->accessToken;
		}
		else if (isset($this->dropFolder->jwtToken))
		{
			$url = $recording->recordingFile->downloadUrl . self::URL_ACCESS_TOKEN . $this->dropFolder->jwtToken;
		}
		
		if ($url)
		{
			$redirectUrl = $url;
			$curl = curl_init($url);
			curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($curl, CURLOPT_FOLLOWLOCATION, true);
			$result = curl_exec($curl);
			if ($result !== false)
			{
				$redirectUrl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
			}
		}
		return $redirectUrl;
	}
}