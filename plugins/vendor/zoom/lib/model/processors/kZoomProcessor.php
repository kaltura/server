<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */

abstract class kZoomProcessor
{
	const ONE_DAY_IN_SECONDS = 86400;
	const ZOOM_PREFIX = 'Zoom_';
	const ZOOM_LOCK_TTL = 120;
	const URL_ACCESS_TOKEN = '?access_token=';
	const REFERENCE_FILTER = '_eq_reference_id';
	const CMS_USER_FIELD = 'cms_user_id';

	/**
	 * @var kZoomClient
	 */
	protected $zoomClient;

	/**
	 * kZoomRecordingProcessor constructor.
	 * @param string $zoomBaseUrl
	 */
	public function __construct($zoomBaseUrl)
	{
		$this->zoomClient = new kZoomClient($zoomBaseUrl);
	}

	/**
	 * @param string $userName
	 * @param ZoomVendorIntegration $zoomIntegration
	 * @return string kalturaUserName
	 */
	protected function processZoomUserName($userName, $zoomIntegration)
	{
		$result = $userName;
		switch ($zoomIntegration->getUserMatching())
		{
			case kZoomUsersMatching::DO_NOT_MODIFY:
				break;
			case kZoomUsersMatching::ADD_POSTFIX:
				$postFix = $zoomIntegration->getUserPostfix();
				if (!kString::endsWith($result, $postFix, false))
				{
					$result = $result . $postFix;
				}

				break;
			case kZoomUsersMatching::REMOVE_POSTFIX:
				$postFix = $zoomIntegration->getUserPostfix();
				if (kString::endsWith($result, $postFix, false))
				{
					$result = substr($result, 0, strlen($result) - strlen($postFix));
				}

				break;
			case kZoomUsersMatching::CMS_MATCHING:
				$zoomUser = $this->zoomClient->retrieveZoomUser($userName);
				if(isset($zoomUser[self::CMS_USER_FIELD]))
				{
					$result = $zoomUser[self::CMS_USER_FIELD];
				}
				break;
		}

		return $result;
	}

	/**
	 * user logged in - need to re-init kPermissionManager in order to determine current user's permissions
	 * @param kuser $dbUser
	 * @param bool $isAdmin
	 * @throws kCoreException
	 */
	protected function initUserPermissions($dbUser, $isAdmin = false)
	{
		$ks = null;
		kSessionUtils::createKSessionNoValidations($dbUser->getPartnerId(), $dbUser->getPuserId() , $ks, self::ONE_DAY_IN_SECONDS , $isAdmin , "" , '*' );
		KalturaLog::debug('changing to ks: ' . $ks);
		kCurrentContext::initKsPartnerUser($ks);
		kPermissionManager::init();
	}

	/**
	 * @param $recordingUuId
	 * @return entry
	 * @throws PropelException
	 */
	protected function getZoomEntryByRecordingId($recordingUuId)
	{
		$entryFilter = new entryFilter();
		$pager = new KalturaFilterPager();
		$entryFilter->setPartnerSearchScope(baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE);
		$entryFilter->set(self::REFERENCE_FILTER, self::ZOOM_PREFIX . $recordingUuId);
		$c = KalturaCriteria::create(entryPeer::OM_CLASS);
		$pager->attachToCriteria($c);
		$entryFilter->attachToCriteria($c);
		$c->add(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_SYSTEM, Criteria::NOT_EQUAL);
		if (kEntitlementUtils::getEntitlementEnforcement() && !kCurrentContext::$is_admin_session && entryPeer::getUserContentOnly())
		{
			entryPeer::setFilterResults(true);
		}

		$entry = entryPeer::doSelectOne($c);
		if($entry)
		{
			KalturaLog::debug('Found entry:' . $entry->getId());
		}

		return $entry;
	}

	/**
	 * @param string $hostEmail
	 * @param ZoomVendorIntegration $zoomIntegration
	 * @return kuser
	 */
	protected function getEntryOwner($hostEmail, $zoomIntegration)
	{
		$partnerId = $zoomIntegration->getPartnerId();
		$zoomUser = new kZoomUser();
		$zoomUser->setOriginalName($hostEmail);
		$zoomUser->setProcessedName($this->processZoomUserName($hostEmail, $zoomIntegration));
		$dbUser = $this->getKalturaUser($partnerId, $zoomUser);
		if (!$dbUser)
		{
			if ($zoomIntegration->getCreateUserIfNotExist())
			{
				$dbUser = kuserPeer::createKuserForPartner($partnerId, $zoomUser->getProcessedName());
			}
			else
			{
				$dbUser = kuserPeer::getKuserByPartnerAndUid($partnerId, $zoomIntegration->getDefaultUserEMail(), true);
			}
		}

		return $dbUser;
	}

	/**
	 * @param int $partnerId
	 * @param kZoomUser $kZoomUser
	 * @return kuser
	 */
	protected function getKalturaUser($partnerId, $kZoomUser)
	{
		$dbUser = kuserPeer::getKuserByPartnerAndUid($partnerId, $kZoomUser->getProcessedName(), true);
		if (!$dbUser)
		{
			$dbUser = kuserPeer::getKuserByEmail($kZoomUser->getOriginalName(), $partnerId);
		}

		return $dbUser;
	}
}