<?php
/**
 * @package Var
 * @subpackage Partners
 */
class Kaltura_Validate_PartnerEmail extends Zend_Validate_Abstract 
{
	const PARTNER_EMAIL_ALREADY_EXISTS = 'Email Address already exists';
	
	protected $_messageTemplates = array (self::PARTNER_EMAIL_ALREADY_EXISTS => "'%value%' - Email Address already exists" );
	
	public function isValid($value) {
		$this->_setValue ( $value );
		
		$client = Infra_ClientHelper::getClient();
		// get results and paginate
		$filter = new Kaltura_Client_Type_UserLoginDataFilter();
		$filter->loginEmailEqual = $value;
		
		$otherUsersWithTheSameEmail = $client->user->checkLoginDataExists($filter);
		
		if ( $otherUsersWithTheSameEmail ) 
		{
			try 
			{
				// allow to use email of admin console users
				$client->user->getByLoginId($value);
			} catch (Exception $ex) {
				$this->_error ();
				return false;	
			}
		}
		
		return true;
	}
}