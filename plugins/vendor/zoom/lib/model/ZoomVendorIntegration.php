<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */

class ZoomVendorIntegration extends VendorIntegration
{
	const ACCESS_TOKEN = 'accessToken';
	const REFRESH_TOKEN = 'refreshToken';
	const EXPIRES_IN = 'expiresIn';
	const DEFAULT_USER_E_MAIL = 'defaultUserEMail';
	const ZOOM_CATEGORY = 'zoomCategory';
	const ZOOM_WEBINAR_CATEGORY = 'zoomWebinarCategory';
	const CREATE_USER_IF_NOT_EXIST = 'createUserIfNotExist';
	const LAST_ERROR = 'lastError';
	const LAST_ERROR_TIMESTAMP = 'lastErrorTimestamp';
	const USER_MATCHING = 'userMatching';
	const HANDLE_PARTICIPANTS_MODE = 'HandleParticipantsMode';
	const USER_POSTFIX = 'UserPostfix';
	const ENABLE_WEBINAR_UPLOADS = 'enableWebinarUploads';
	const CONVERSION_PROFILE_ID = 'conversionProfileId';
	const JWT_TOKEN = 'jwtToken';
	const DELETE_POLICY = 'deletionPolicy';
	const ENABLE_ZOOM_TRANSCRIPTION =  'enableZoomTranscription';
	const ZOOM_ACCOUNT_DESCRIPTION = 'zoomAccountDescription';
	const ENABLE_MEETING_UPLOAD = 'enableMeetingUpload';
	const OPT_OUT_GROUP_NAMES = 'optOutGroupNames';
	const OPT_IN_GROUP_NAMES = 'optInGroupNames';
	const GROUP_PARTICIPATION_TYPE = 'groupParticipationType';

	public function setAccessToken ($v)	{ $this->putInCustomData ( self::ACCESS_TOKEN, $v);	}
	public function getAccessToken ( )	{ return $this->getFromCustomData(self::ACCESS_TOKEN);	}
	
	public function setJwtToken ($v)	{ $this->putInCustomData ( self::JWT_TOKEN, $v);	}
	public function getJwtToken ( )	{ return $this->getFromCustomData(self::JWT_TOKEN);	}
	
	public function setDeletionPolicy ($v)	{ $this->putInCustomData ( self::DELETE_POLICY, $v);	}
	public function getDeletionPolicy ( )	{ return $this->getFromCustomData(self::DELETE_POLICY);	}
	
	public function setEnableZoomTranscription ($v)	{ $this->putInCustomData ( self::ENABLE_ZOOM_TRANSCRIPTION, $v);	}
	public function getEnableZoomTranscription ( )	{ return $this->getFromCustomData(self::ENABLE_ZOOM_TRANSCRIPTION);	}
	
	public function setRefreshToken ($v)	{ $this->putInCustomData ( self::REFRESH_TOKEN, $v);	}
	public function getRefreshToken ( )	{ return $this->getFromCustomData(self::REFRESH_TOKEN);	}

	public function setExpiresIn ($v)	{ $this->putInCustomData ( self::EXPIRES_IN, $v);	}
	public function getExpiresIn( )	{ return $this->getFromCustomData(self::EXPIRES_IN);	}

	public function setDefaultUserEMail ($v)	{ $this->putInCustomData ( self::DEFAULT_USER_E_MAIL, $v);	}
	public function getDefaultUserEMail ( )	{ return $this->getFromCustomData(self::DEFAULT_USER_E_MAIL);	}

	public function setZoomCategory($v)	{ $this->putInCustomData ( self::ZOOM_CATEGORY, $v);	}
	public function getZoomCategory( )	{ return $this->getFromCustomData(self::ZOOM_CATEGORY);	}
	public function unsetCategory( )  {return $this->removeFromCustomData(self::ZOOM_CATEGORY);	}

	public function setZoomWebinarCategory($v)	{ $this->putInCustomData ( self::ZOOM_WEBINAR_CATEGORY, $v);	}
	public function getZoomWebinarCategory( )	{ return $this->getFromCustomData(self::ZOOM_WEBINAR_CATEGORY);	}
	public function unsetWebinarCategory( )  {return $this->removeFromCustomData(self::ZOOM_WEBINAR_CATEGORY);	}

	public function setCreateUserIfNotExist($v) { $this->putInCustomData ( self::CREATE_USER_IF_NOT_EXIST, $v); }
	public function getCreateUserIfNotExist() { return $this->getFromCustomData ( self::CREATE_USER_IF_NOT_EXIST,null, false); }

	public function setUserMatching($v) { $this->putInCustomData ( self::USER_MATCHING, $v); }
	public function getUserMatching() { return $this->getFromCustomData ( self::USER_MATCHING,null, kZoomUsersMatching::DO_NOT_MODIFY); }

	public function setHandleParticipantsMode($v) { $this->putInCustomData ( self::HANDLE_PARTICIPANTS_MODE, $v); }
	public function getHandleParticipantsMode() { return $this->getFromCustomData ( self::HANDLE_PARTICIPANTS_MODE,null, kHandleParticipantsMode::ADD_AS_CO_PUBLISHERS); }

	public function setUserPostfix($v) { $this->putInCustomData ( self::USER_POSTFIX, $v); }
	public function getUserPostfix() { return $this->getFromCustomData ( self::USER_POSTFIX,null, ""); }

	public function setEnableWebinarUploads($v) { $this->putInCustomData ( self::ENABLE_WEBINAR_UPLOADS, $v); }
	public function getEnableWebinarUploads() { return $this->getFromCustomData ( self::ENABLE_WEBINAR_UPLOADS,null, true); }

	public function setConversionProfileId($v) { $this->putInCustomData ( self::CONVERSION_PROFILE_ID, $v); }
	public function getConversionProfileId() { return $this->getFromCustomData ( self::CONVERSION_PROFILE_ID,null, null); }
	
	public function setZoomAccountDescription ($v)	{ $this->putInCustomData ( self::ZOOM_ACCOUNT_DESCRIPTION, $v);	}
	public function getZoomAccountDescription ( )	{ return $this->getFromCustomData(self::ZOOM_ACCOUNT_DESCRIPTION);	}
	
	public function setEnableMeetingUpload ($v)	{ $this->putInCustomData ( self::ENABLE_MEETING_UPLOAD, $v);	}
	public function getEnableMeetingUpload ( )	{ return $this->getFromCustomData(self::ENABLE_MEETING_UPLOAD);	}

	public function setLastError($v)
	{
		$this->putInCustomData(self::LAST_ERROR_TIMESTAMP, time());
		$this->putInCustomData(self::LAST_ERROR, $v);
	}

	public function saveLastError($v)
	{
		$this->setLastError($v);
		$this->save();
	}

	/**
	 * returns all tokens as array
	 * @return array
	 */
	public function getTokens()
	{
		return array(kZoomOauth::ACCESS_TOKEN => $this->getAccessToken(), kZoomOauth::REFRESH_TOKEN => $this->getRefreshToken(),
			kZoomOauth::EXPIRES_IN => $this->getExpiresIn(), self::JWT_TOKEN => $this->getJwtToken());
	}

	/**
	 * @param array $tokensDataAsArray
	 * @throws PropelException
	 */
	public function saveTokensData($tokensDataAsArray)
	{
		$this->setTokensData($tokensDataAsArray);
		$this->save();
	}

	public function setTokensData($tokensDataAsArray)
	{
		$this->setExpiresIn($tokensDataAsArray[kZoomOauth::EXPIRES_IN]);
		$this->setAccessToken($tokensDataAsArray[kZoomOauth::ACCESS_TOKEN]);
		$this->setRefreshToken($tokensDataAsArray[kZoomOauth::REFRESH_TOKEN]);
		$this->setJwtToken($tokensDataAsArray[self::JWT_TOKEN]);
		$this->setVendorType(VendorTypeEnum::ZOOM_ACCOUNT);
	}
	
	public function getOptOutGroupNames()
	{
		return $this->getFromCustomData (self::OPT_OUT_GROUP_NAMES);
	}
	public function getOptInGroupNames()
	{
		return $this->getFromCustomData (self::OPT_IN_GROUP_NAMES);
	}
	
	public function setOptOutGroupNames($v)
	{
		if ($this->getGroupParticipationType() == kZoomGroupParticipationType::OPT_OUT)
		{
			$this->putInCustomData(self::OPT_OUT_GROUP_NAMES, $v);
		}
	}
	
	public function addGroupNameToOptOutGroups($v)
	{
		if ($this->getGroupParticipationType() == kZoomGroupParticipationType::OPT_OUT)
		{
			$this->putInCustomData(self::OPT_OUT_GROUP_NAMES, $this->getOptOutGroupNames() . "\r\n" . $v);
		}
	}
	
	public function setOptInGroupNames($v)
	{
		if ($this->getGroupParticipationType() == kZoomGroupParticipationType::OPT_IN)
		{
			$this->putInCustomData(self::OPT_IN_GROUP_NAMES, $v);
		}
	}
	
	public function addGroupNameToOptInGroups($v)
	{
		if ($this->getGroupParticipationType() == kZoomGroupParticipationType::OPT_IN)
		{
			$this->putInCustomData(self::OPT_IN_GROUP_NAMES, $this->getOptInGroupNames() . "\r\n" . $v);
		}
	}
	
	public function setGroupParticipationType($v)
	{
		if ($v != $this->getGroupParticipationType())
		{
			$this->putInCustomData(self::GROUP_PARTICIPATION_TYPE, $v);
			$this->putInCustomData(self::OPT_IN_GROUP_NAMES, '');
			$this->putInCustomData(self::OPT_OUT_GROUP_NAMES, '');
		}
	}
	
	public function getGroupParticipationType()
	{
		return $this->getFromCustomData(self::GROUP_PARTICIPATION_TYPE, null, kZoomGroupParticipationType::NO_CLASSIFICATION);
	}
	
	public function shouldExcludeUserRecordingsIngest($puserId)
	{
		$kuser = kuserPeer::getKuserByPartnerAndUid($this->partner_id, $puserId);
		$userGroupsArray = KuserKgroupPeer::retrievePgroupIdsByKuserIds(array($kuser->getId()));
		if ($this->getGroupParticipationType() == kZoomGroupParticipationType::OPT_IN)
		{
			$optInGroupNames = explode("\r\n", $this->getOptInGroupNames());
			KalturaLog::debug('Account is configured to OPT IN users that are members of the groups ['. print_r($optInGroupNames, true) .']');
			return $this->intersectPolicyGroupsAndUserGroups($userGroupsArray, $optInGroupNames);
		}
		else
		{
			$optOutGroupNames = explode("\r\n", $this->getOptOutGroupNames());
			KalturaLog::debug('Account is configured to OPT OUT users that are members of the groups ['. print_r($optOutGroupNames, true) .']');
			return !($this->intersectPolicyGroupsAndUserGroups($userGroupsArray, $optOutGroupNames));
		}
	}
	
	protected function intersectPolicyGroupsAndUserGroups($userGroupsArray, $vendorGroupsNamesArray)
	{
		if (!empty(array_intersect($userGroupsArray, $vendorGroupsNamesArray)))
		{
			return false;
		}
		return true;
	}
}