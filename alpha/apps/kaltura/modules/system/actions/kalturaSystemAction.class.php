<?php
require_once ( "kalturaAction.class.php" );

abstract class kalturaSystemAction extends kalturaAction
{
	const COOKIE_NAME = "kalsysauth";
	
	const SYSTEM_CRED_EXPIRY_SEC = 86400; // one day 

	protected function forceSystemAuthentication()
	{
		myDbHelper::$use_alternative_con = myDbHelper::DB_HELPER_CONN_PROPEL3;
		
		$kal_sys_auth = @$_COOKIE[self::COOKIE_NAME];
		if ( $kal_sys_auth == $this->authKey() ) //$this->getUser()->isAuthenticated() && $this->isValidExpiryCredential( "system_auth"))
		{
			return true;
		}
		$this->setFlash('sign_in_referer', $_SERVER["REQUEST_URI"]);
		//echo "forceSystemAuthentication - false";
		return $this->forward('system','login');
	}
	
	protected function systemAuthenticated( )
	{
		setcookie( self::COOKIE_NAME , $this->authKey() , time() + self::SYSTEM_CRED_EXPIRY_SEC , "/"  );
//		$this->getUser()->setAuthenticated(true);
//		$this->setExpiryCredential( "system_auth" , self::SYSTEM_CRED_EXPIRY_SEC );
	}

	protected function systemLogout( )
	{
		setcookie( self::COOKIE_NAME , "" , time()-10 , "/"  );
		
		//$this->getUser()->setAuthenticated(false);
		//$this->getUser()->clearCredentials();	
	}
	
	
	protected function authKey ()
	{
		$ip = requestUtils::getRemoteAddress();
		$hash = kConf::get ( "system_pages_login_password" );
		return sha1($hash.$ip);
	}
}
?>