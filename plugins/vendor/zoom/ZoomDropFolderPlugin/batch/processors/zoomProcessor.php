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
		$this->zoomClient = new kZoomClient($zoomBaseUrl, $this->dropFolder->refreshToken, $this->dropFolder->jwtToken);
		$this->dropFolder = $folder;
	}
	
	/**
	 * @param string $userName
	 * @return string kalturaUserName
	 */
	protected function processZoomUserName($userName)
	{
		$result = $userName;
		switch ($this->dropFolder->zoomVendorIntegration->userMatching)
		{
			case kZoomUsersMatching::DO_NOT_MODIFY:
				break;
			case kZoomUsersMatching::ADD_POSTFIX:
				$postFix = $this->dropFolder->zoomVendorIntegration->userPostfix;
				if (!kString::endsWith($result, $postFix, false))
				{
					$result = $result . $postFix;
				}
				
				break;
			case kZoomUsersMatching::REMOVE_POSTFIX:
				$postFix = $this->dropFolder->zoomVendorIntegration->userPostfix;
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
		
		try
		{
			KBatchBase::impersonate($recordingPartnerId);
			$kalturaEntry = KBatchBase::$kClient->baseEntry->listAction($entryFilter, $entryPager);
			KBatchBase::unimpersonate();
		}
		catch (KalturaException $e)
		{
			KalturaLog::debug($e->getMessage());
			$kalturaEntry = null;
		}
		
		if($kalturaEntry->objects)
		{
			KalturaLog::debug('Found entry:' . $kalturaEntry->objects[0]->id);
		}
		return $kalturaEntry;
	}
	
	/**
	 * @param string $hostEmail
	 * @return KalturaUser
	 */
	protected function getEntryOwner($hostEmail)
	{
		$partnerId = $this->dropFolder->partnerId;
		$zoomUser = new kZoomUser();
		$zoomUser->setOriginalName($hostEmail);
		$zoomUser->setProcessedName($this->processZoomUserName($hostEmail));
		KBatchBase::impersonate($partnerId);
		$kalturaUser = $this->getKalturaUser($partnerId, $zoomUser);
		if (!$kalturaUser)
		{
			if ($this->dropFolder->zoomVendorIntegration->createUserIfNotExist)
			{
				$kalturaUser = $this->createNewUser($partnerId, $zoomUser->getProcessedName());
			}
			else
			{
				$pager = new KalturaFilterPager();
				$pager->pageSize = 1;
				$pager->pageIndex = 1;
				
				$filter = new KalturaUserFilter();
				$filter->partnerIdEqual = $partnerId;
				$filter->idEqual = $this->dropFolder->zoomVendorIntegration->defaultUserEMail;
				$kalturaUser = KBatchBase::$kClient->user->listAction($filter, new KalturaFilterPager());
				if ($kalturaUser->objects)
				{
					$kalturaUser = $kalturaUser->objects[0];
				}
				else
				{
					$kalturaUser = null;
				}
			}
		}
		KBatchBase::unimpersonate();
		return $kalturaUser;
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
		$kalturaUser = KBatchBase::$kClient->user->listAction($filter, new KalturaFilterPager());
		if (!$kalturaUser->objects)
		{
			$email = $kZoomUser->getOriginalName();
			$filterUser = new KalturaUserFilter();
			$filterUser->partnerIdEqual = $partnerId;
			$filterUser->emailStartsWith = $email;
			$kalturaUser = KBatchBase::$kClient->user->listAction($filter, new KalturaFilterPager());
			if (!$kalturaUser->objects || $kalturaUser->objects[0]->email != $email)
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
		$user->partnerId = $partnerId;
		$user->isAdmin = false;
		$user->type = KalturaUserType::USER;
		$kalturaUser = KBatchBase::$kClient->user->add($user);
		return $kalturaUser;
	}
}