<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */

class ZoomVendorIntegration extends VendorIntegration
{
	const ZOOM_CATEGORY = 'zoomCategory';
	const ZOOM_WEBINAR_CATEGORY = 'zoomWebinarCategory';
	const USER_MATCHING = 'userMatching';
	const USER_POSTFIX = 'UserPostfix';
	const ENABLE_WEBINAR_UPLOADS = 'enableWebinarUploads';
	const ZOOM_AUTH_TYPE = 'zoomAuthType';
	const ENABLE_ZOOM_TRANSCRIPTION =  'enableZoomTranscription';
	const ZOOM_ACCOUNT_DESCRIPTION = 'zoomAccountDescription';
	const OPT_OUT_GROUP_NAMES = 'optOutGroupNames';
	const OPT_IN_GROUP_NAMES = 'optInGroupNames';
	const GROUP_PARTICIPATION_TYPE = 'groupParticipationType';

	public function setZoomAuthType($v)	{ $this->putInCustomData ( self::ZOOM_AUTH_TYPE, $v); }
	public function getZoomAuthType()	{ return $this->getFromCustomData(self::ZOOM_AUTH_TYPE, null, kZoomAuthTypes::OAUTH); }
	
	public function setEnableZoomTranscription ($v)	{ $this->putInCustomData ( self::ENABLE_ZOOM_TRANSCRIPTION, $v);	}
	public function getEnableZoomTranscription ( )	{ return $this->getFromCustomData(self::ENABLE_ZOOM_TRANSCRIPTION);	}
	
	public function setZoomCategory($v)	{ $this->putInCustomData ( self::ZOOM_CATEGORY, $v);	}
	public function getZoomCategory( )	{ return $this->getFromCustomData(self::ZOOM_CATEGORY);	}
	public function unsetCategory( )  {return $this->removeFromCustomData(self::ZOOM_CATEGORY);	}

	public function setZoomWebinarCategory($v)	{ $this->putInCustomData ( self::ZOOM_WEBINAR_CATEGORY, $v);	}
	public function getZoomWebinarCategory( )	{ return $this->getFromCustomData(self::ZOOM_WEBINAR_CATEGORY);	}
	public function unsetWebinarCategory( )  {return $this->removeFromCustomData(self::ZOOM_WEBINAR_CATEGORY);	}
	
	public function setUserMatching($v) { $this->putInCustomData ( self::USER_MATCHING, $v); }
	public function getUserMatching() { return $this->getFromCustomData ( self::USER_MATCHING,null, kZoomUsersMatching::DO_NOT_MODIFY); }

	public function setUserPostfix($v) { $this->putInCustomData ( self::USER_POSTFIX, $v); }
	public function getUserPostfix() { return $this->getFromCustomData ( self::USER_POSTFIX,null, ""); }

	public function setEnableWebinarUploads($v) { $this->putInCustomData ( self::ENABLE_WEBINAR_UPLOADS, $v); }
	public function getEnableWebinarUploads() { return $this->getFromCustomData ( self::ENABLE_WEBINAR_UPLOADS,null, true); }
	
	public function setZoomAccountDescription ($v)	{ $this->putInCustomData ( self::ZOOM_ACCOUNT_DESCRIPTION, $v);	}
	public function getZoomAccountDescription ( )	{ return $this->getFromCustomData(self::ZOOM_ACCOUNT_DESCRIPTION);	}


	public function setTokensData($tokensDataAsArray)
	{
		parent::setTokensData($tokensDataAsArray);
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

	public function saveAccessTokenData($tokensDataAsArray)
	{
		$this->setExpiresIn($tokensDataAsArray[kOAuth::EXPIRES_IN]);
		$this->setAccessToken($tokensDataAsArray[kOAuth::ACCESS_TOKEN]);
		$this->save();
	}
}