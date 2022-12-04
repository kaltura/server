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

	public function setWebexCategory($v)	{ $this->putInCustomData ( self::WEBEX_CATEGORY, $v); }
	public function getWebexCategory( )	{ return $this->getFromCustomData(self::WEBEX_CATEGORY); }
	public function unsetCategory( ) 	{ return $this->removeFromCustomData(self::WEBEX_CATEGORY); }
	
	public function setUserMatchingMode($v)	{ $this->putInCustomData ( self::USER_MATCHING_MODE, $v); }
	public function getUserMatchingMode( )	{ return $this->getFromCustomData ( self::USER_MATCHING_MODE,null, kZoomUsersMatching::DO_NOT_MODIFY); }
	
	public function setUserPostfix($v)	{ $this->putInCustomData ( self::USER_POSTFIX, $v); }
	public function getUserPostfix( )	{ return $this->getFromCustomData(self::USER_POSTFIX); }
	
	public function setEnableTranscription($v)	{ $this->putInCustomData(self::ENABLE_TRANSCRIPTION, $v); }
	public function getEnableTranscription( )	{ return $this->getFromCustomData(self::ENABLE_TRANSCRIPTION); }
	
	public function setWebexAccountDescription ($v)	{ $this->putInCustomData ( self::WEBEX_ACCOUNT_DESCRIPTION, $v);	}
	public function getWebexAccountDescription ( )	{ return $this->getFromCustomData(self::WEBEX_ACCOUNT_DESCRIPTION);	}
	

	public function setTokensData($tokensDataAsArray)
	{
		parent::setTokensData($tokensDataAsArray);
		$this->setVendorType(VendorTypeEnum::WEBEX_API_ACCOUNT);
	}
}
