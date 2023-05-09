<?php
/**
 * @package plugins.ZoomDropFolder
 */

abstract class zoomProcessor
{
	const ONE_DAY_IN_SECONDS = 86400;
	const ZOOM_PREFIX = 'Zoom_';
	const ZOOM_LOCK_TTL = 120;
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
		$accountId = isset($folder->accountId) ? $folder->accountId : null;
		$refreshToken = isset($folder->refreshToken) ? $folder->refreshToken : null;
		$accessToken = isset($folder->accessToken) ? $folder->accessToken : null;
		$accessExpiresIn = isset($folder->accessExpiresIn) ? $folder->accessExpiresIn : null;
		$zoomAuthType = isset($folder->zoomAuthType) ? $folder->zoomAuthType : null;
		$this->zoomClient = new kZoomClient($zoomBaseUrl, $accountId, $refreshToken, $accessToken, $accessExpiresIn, $zoomAuthType);
		$this->dropFolder = $folder;
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
	
	protected function getZoomRedirectUrlFromFile($recording)
	{
		if (!isset($recording->recordingFile->downloadToken) && !isset($this->dropFolder->accessToken))
		{
			return null;
		}
		
		if (isset($recording->recordingFile->downloadToken))
		{
			$accessToken = $recording->recordingFile->downloadToken;
		}
		else if (isset($this->dropFolder->accessToken))
		{
			$accessToken = $this->dropFolder->accessToken;
		}
		
		$authorizationHeader = "Authorization: Bearer $accessToken";
		$redirectUrl = ZoomHelper::getRedirectUrl($recording->recordingFile->downloadUrl, array($authorizationHeader));
		$headerStringObject = new KalturaString();
		$headerStringObject->value = $authorizationHeader;
		return array($redirectUrl, array($headerStringObject));
	}
}