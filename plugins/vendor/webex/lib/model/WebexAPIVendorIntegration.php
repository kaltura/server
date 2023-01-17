<?php
/**
 * @package plugins.WebexAPIDropFolder
 * @subpackage webex.model
 */
class WebexAPIVendorIntegration extends VendorIntegration
{
	const WEBEX_CATEGORY = 'webexCategory';
	const ENABLE_TRANSCRIPTION =  'enableTranscription';
	const USER_MATCHING_MODE = 'userMatchingMode';
	const USER_POSTFIX = 'UserPostfix';
	const WEBEX_ACCOUNT_DESCRIPTION = 'webexAccountDescription';
	const OPT_OUT_GROUP_NAMES = 'optOutGroupNames';
	const OPT_IN_GROUP_NAMES = 'optInGroupNames';
	const GROUP_PARTICIPATION_TYPE = 'groupParticipationType';
	const SITE_URL = 'site_url';
	const DEFAULT_WEBEX_CATEGORY = 'webexCategory';
	
	
	public function setWebexCategory($v)	{ $this->putInCustomData ( self::WEBEX_CATEGORY, $v); }
	public function getWebexCategory( )	{ return $this->getFromCustomData(self::WEBEX_CATEGORY, null, self::DEFAULT_WEBEX_CATEGORY); }
	public function unsetCategory( ) 	{ return $this->removeFromCustomData(self::WEBEX_CATEGORY); }
	
	public function setUserMatchingMode($v)	{ $this->putInCustomData ( self::USER_MATCHING_MODE, $v); }
	public function getUserMatchingMode( )	{ return $this->getFromCustomData ( self::USER_MATCHING_MODE,null, kWebexAPIUsersMatching::DO_NOT_MODIFY); }
	
	public function setUserPostfix($v)	{ $this->putInCustomData ( self::USER_POSTFIX, $v); }
	public function getUserPostfix( )	{ return $this->getFromCustomData(self::USER_POSTFIX); }
	
	public function setEnableTranscription($v)	{ $this->putInCustomData(self::ENABLE_TRANSCRIPTION, $v); }
	public function getEnableTranscription( )	{ return $this->getFromCustomData(self::ENABLE_TRANSCRIPTION, null, true); }
	
	public function setWebexAccountDescription ($v)	{ $this->putInCustomData ( self::WEBEX_ACCOUNT_DESCRIPTION, $v);	}
	public function getWebexAccountDescription ( )	{ return $this->getFromCustomData(self::WEBEX_ACCOUNT_DESCRIPTION);	}
	
	public function getOptOutGroupNames()
	{
		return $this->getFromCustomData(self::OPT_OUT_GROUP_NAMES);
	}
	
	public function getOptInGroupNames()
	{
		return $this->getFromCustomData(self::OPT_IN_GROUP_NAMES);
	}
	
	public function setOptOutGroupNames($v)
	{
		if ($this->getGroupParticipationType() == kWebexAPIGroupParticipationType::OPT_OUT)
		{
			$this->putInCustomData(self::OPT_OUT_GROUP_NAMES, $v);
		}
	}
	
	public function addGroupNameToOptOutGroups($v)
	{
		if ($this->getGroupParticipationType() == kWebexAPIGroupParticipationType::OPT_OUT)
		{
			$this->putInCustomData(self::OPT_OUT_GROUP_NAMES, $this->getOptOutGroupNames() . "\r\n" . $v);
		}
	}
	
	public function setOptInGroupNames($v)
	{
		if ($this->getGroupParticipationType() == kWebexAPIGroupParticipationType::OPT_IN)
		{
			$this->putInCustomData(self::OPT_IN_GROUP_NAMES, $v);
		}
	}
	
	public function addGroupNameToOptInGroups($v)
	{
		if ($this->getGroupParticipationType() == kWebexAPIGroupParticipationType::OPT_IN)
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
		return $this->getFromCustomData(self::GROUP_PARTICIPATION_TYPE, null, kWebexAPIGroupParticipationType::NO_CLASSIFICATION);
	}
	
	
	public function getSiteUrl()
	{
		return $this->getFromCustomData(self::SITE_URL);
	}
	
	public function setSiteUrl($v)
	{
		$this->putInCustomData(self::SITE_URL, $v);
	}

	public function setTokensData($tokensDataAsArray)
	{
		parent::setTokensData($tokensDataAsArray);
		$this->setVendorType(VendorTypeEnum::WEBEX_API_ACCOUNT);
	}
}
