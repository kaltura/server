<?php
require_once ( "kalturaAction.class.php" );

abstract class kalturaSalesAction extends kalturaAction
{
	const COOKIE_NAME = "kalsalesauth";
	
	const SYSTEM_CRED_EXPIRY_SEC = 43200; // one day
	
	//const SALESTOOLS_LOGIN_PASSWORD = '4a21c2dfbc35c19988a8a5d60548de75'; // md5 4aru0tlak9
	const SALESTOOLS_LOGIN_PASSWORD = 'd1317f0aa61e14390a61bd96d49cea5b'; // md5 6tricks*

	protected function forceSystemAuthentication($forward = true)
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;
		
		$kal_sys_auth = @$_COOKIE[self::COOKIE_NAME];
		if ( $kal_sys_auth == $this->authKey() ) //$this->getUser()->isAuthenticated() && $this->isValidExpiryCredential( "system_auth"))
		{
			return true;
		}
		$this->setFlash('sign_in_referer', $_SERVER["REQUEST_URI"]);
		//echo "forceSystemAuthentication - false";
		if($forward)
			return $this->forward('salestools','login');
		
		return false;
	}
	
	protected function systemAuthenticated( )
	{
		setcookie( self::COOKIE_NAME , $this->authKey() , time() + self::SYSTEM_CRED_EXPIRY_SEC , "/"  );
//		$this->getUser()->setAuthenticated(true);
//		$this->setExpiryCredential( "system_auth" , self::SYSTEM_CRED_EXPIRY_SEC );
	}

	public function systemLogout( )
	{
		setcookie( self::COOKIE_NAME , "" , time()-10 , "/"  );
		
		//$this->getUser()->setAuthenticated(false);
		//$this->getUser()->clearCredentials();	
	}
	
	public function validatePassword( $password )
	{
		return ( md5($password) == self::SALESTOOLS_LOGIN_PASSWORD );
	}
	
	protected function authKey ()
	{
		$ip = requestUtils::getRemoteAddress();
		$hash = self::SALESTOOLS_LOGIN_PASSWORD;
		return sha1($hash.$ip);
	}
}
?>