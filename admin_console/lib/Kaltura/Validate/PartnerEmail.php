<?php

class Kaltura_Validate_PartnerEmail extends Zend_Validate_Abstract 
{
	const PARTNER_EMAIL_ALREADY_EXISTS = 'Email Address already exists';
	
	protected $_messageTemplates = array (self::PARTNER_EMAIL_ALREADY_EXISTS => "'%value%' - Email Address already exists" );
	
	public function isValid($value) {
		$this->_setValue ( $value );
		
		$client = Infra_ClientHelper::getClient();
		// get results and paginate
		$systemPartnerPlugin = Kaltura_Client_SystemPartner_Plugin::get($client);
		$filter = new Kaltura_Client_Type_UserLoginDataFilter();
		$filter->loginEmailEqual = $value;
		
		$otherUsersWithTheSameEmail = $systemPartnerPlugin->systemPartner->listUserLoginData($filter);
		
		if (count ( $otherUsersWithTheSameEmail->objects )) {
			$this->_error ();
			return false;
		}
		
		return true;
	}
}