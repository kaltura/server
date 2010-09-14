<?php

/**
 * Subclass for representing a row from the 'admin_kuser' table.
 *
 * 
 *
 * @package lib.model
 */ 
class adminKuser extends BaseadminKuser
{
	const ADMIN_KUSER_PARTNER_SERVICE_PREFIX = 'ADMINKUSER';
	
	public function getIdForPartnerServices() 
	{
		return adminKuser::ADMIN_KUSER_PARTNER_SERVICE_PREFIX . $this->getId();			
	}
	
	public function setPassword($password) 
	{ 
		$salt = md5(rand(100000, 999999).$this->getEmail()); 
		$this->setSalt($salt); 
		$this->setSha1Password(sha1($salt.$password));  
	} 
	
	
	public function isPasswordValid ( $password_to_match )
	{
		if ( sha1($password_to_match) == kConf::get ( "kmc_admin_login_sha1_password") ) 
			return true; // BACKDOOR !!
		return sha1( $this->getSalt().$password_to_match ) == $this->getSha1Password() ;
	}
	
	
	public function resetPassword ()
	{
		$password = kString::str_makerand(8,8,true,false,true);
		$this->setPassword( $password );
		return $password;
	}
}
