<?php
/**
 * This class will make common tasks in the action classes much easier.
 *
 */
abstract class kalturaAction extends sfAction
{
  private $cookieSecret = 'y3tAno3therS$cr3T';
  
	const COOKIE_CREDENTAIL_IV = "76Abigail13bisca"; 
	const COOKIE_EXPIRY = 100000;
	 
	const VERIFICATION_MODE_CONTRIB = "contrib";
	const VERIFICATION_MODE_EDIT = "edit";
	const VERIFICATION_MODE_CUSTOMIZE = "customize";
	const VERIFICATION_MODE_VIEW = "view";
	
	const ON_ERROR_RETURN_RESULT = 1;
	const ON_ERROR_WIZARD = 2 ;
	const ON_ERROR_FULL_SCREEN = 3;
	 
	// likuser === Logged In Kuser
	protected $likuser_id = NULL;
	protected $likuser = NULL;
	protected $lipuser_id = NULL;

	protected function getP ( $param_name , $default_val = NULL )
	{
		return requestUtils::getParameter ( $param_name , $default_val );
	}
	
	protected function injectIfEmpty ( $map_of_parameterts )
	{
		foreach ( $map_of_parameterts as $param => $val )
		{
			if ( $this->getP ( $param ) == "" )
				$_REQUEST[$param] = $val;
		}
	}
	
	
	protected function getLoggedInUserId ( )
	{
		try
		{
			if ( $this->likuser_id == NULL )
			{
				list($this->likuser_id, $email, $screenname) = $this->getUserzoneCookie();
			}
			return $this->likuser_id;
		}
		catch ( Exception $ex )
		{
			return NULL;
		}
	}

	protected function getLoggedInPuserId ( )
	{
		try
		{
			if ( $this->lipuser_id == NULL )
			{
				list($id, $this->lipuser_id, $screenname) = $this->getUserzoneCookie();
			}
			return $this->lipuser_id;
		}
		catch ( Exception $ex )
		{
			return NULL;
		}
	}
	
	protected function getLoggedInUser (  )
	{
		if ( $this->likuser != NULL )
		{
			return $this->likuser;
		}
		try
		{
			$id = $this->getLoggedInUserId();
			if ( $id == NULL )
			{
				return NULL;
			}
			$this->likuser = kuser::getKuserById ( $id );
			return $this->likuser;
		}
		catch ( Exception $ex )
		{
			return NULL;
		}
	}
	
	protected function logOut ()
	{
		// TODO - add kill credentails & invalidate the authentication 
		self::removeAllSecureCookies();
	}
	
	protected function playDead( $msg = "" )
	{
		// a page worth displaying
		if ( $msg != NULL )
		{
			$this->setFlash( 'message_404', $msg );
			return $this->forward404( $msg );
		}
		
		// no dispaly - exit now.
		die();
	}
	
	/**
	 * A common task is to force authentication
	 */
	protected function forceAuthentication ( $allow_redirect = true )
	{
		if ( !$this->getUserzoneCookie() )
		{
			//echo ( "forceAuthentication [$allow_redirect]" ); 
			if ( $allow_redirect )
			{
				
				$this->setFlash('sign_in_referer', $_SERVER["REQUEST_URI"]);
				return $this->forward('login','signinAjaxShowForm');
			}
			else
			{
				// this must be an action the kuser is deliberatly trying to hack into
				return false;
			}
		}
		
		return true;
	}

	protected function forceContribPermissions ( $kshow , $kshow_id , $allow_redirect = true , $full_window = false)
	{
		return $this->forcePermissionsImpl ( $kshow ,$kshow_id , self::VERIFICATION_MODE_CONTRIB , $allow_redirect , $full_window );
	}

	protected function forceEditPermissions ( $kshow ,$kshow_id , $allow_redirect = true , $full_window = false)
	{
		return $this->forcePermissionsImpl ( $kshow ,$kshow_id , self::VERIFICATION_MODE_EDIT , $allow_redirect , $full_window );
	}

	protected function forceCustomizePermissions ( $kshow ,$kshow_id , $allow_redirect = true , $full_window = false)
	{
		return $this->forcePermissionsImpl ( $kshow ,$kshow_id , self::VERIFICATION_MODE_CUSTOMIZE , $allow_redirect , $full_window );
	}
	
	protected function forceViewPermissions ( $kshow ,$kshow_id , $allow_redirect = true , $full_window = false )
	{
		return $this->forcePermissionsImpl ( $kshow ,$kshow_id , self::VERIFICATION_MODE_VIEW , $allow_redirect , $full_window );
	}
	
	// if $allow_redirect == true  $full_window can be true too which will cause a page to open and only then open the authentication wizard 
	// 
	private function forcePermissionsImpl ( $kshow ,$kshow_id , $verification_mode , $allow_redirect = true , $full_window = false)
	{	
		if ( $kshow == NULL )	$kshow = kshowPeer::retrieveByPK( $kshow_id);
		if ( !$kshow )
		{
			$this->playDead( "This Kaltura is no longer available. (Message No." .$kshow_id.")" );
			//throw new Exception ( "Cannot force permission for show $kshow_id");
		}
		
		$likuser_id = $this->getLoggedInUserId();
		
		if ( kuser::isAdmin ( $likuser_id ) )
		{
			return true;
		}
		
		// if the user is eother the producer or an admin - return true
		$viewer_type = myKshowUtils::getViewerType($kshow , $likuser_id ) ;
		
		if ( $viewer_type == KshowKuser::KSHOWKUSER_VIEWER_PRODUCER ) return true;
				
		$this->setCredentialByName ( "requestKshow" , $kshow_id );
		$this->setCredentialByName ( "verificationMode" , $verification_mode );
		
//		echo ("verificationMode: " .  $this->getCredentialByName ( "verificationMode" ) );
		
		if ( $full_window )
		{
			// check if all's well - if not - forward
			$result = $this->forcePermissionsDoCheckOrRedirect( $kshow ,$kshow_id , $verification_mode , false );
			if ( $result )
				return true;// ALL IS OK !
			$this->setFlash('vm', $verification_mode);
			$this->setFlash('kshow_id', $kshow_id);
			return $this->forward('login','openAuthenticate');
		}
		else
		{
			return $this->forcePermissionsDoCheckOrRedirect( $kshow ,$kshow_id , $verification_mode , $allow_redirect );
		}
	}
	
	private function forcePermissionsDoCheckOrRedirect ( $kshow ,$kshow_id , $verification_mode , $allow_redirect = true )
	{
		$this->setCredentialByName ( "requestKshowName" , $kshow->getName() );

		$force_auth = false;
		if ( $verification_mode == self::VERIFICATION_MODE_CONTRIB )
		{
			$permissions = $kshow->getContribPermissions();
			$pwd = $kshow->getContribPassword();
		}
		else if ( $verification_mode == self::VERIFICATION_MODE_EDIT  )
		{
			$permissions = $kshow->getEditPermissions ();
			$pwd = $kshow->getEditPassword();
			// in this case - force authentication when not for everyone 
			$force_auth = true;
		}
		else if ( $verification_mode == self::VERIFICATION_MODE_VIEW  )
		{
			$permissions = $kshow->getViewPermissions ();
			$pwd = $kshow->getViewPassword();
		}
		else if ( $verification_mode == self::VERIFICATION_MODE_CUSTOMIZE  )
		{
			// only the producer can customize
			if ( ! $this->isProducer( $kshow ) )
			{
				$this->playDead( NULL );
			}
			return true;
		}		
		else
		{
			throw new Exception ( "Cannot force permission for type $verification_mode");
		}
/*		
		echo "kshow_id: $kshow_id, verification_mode: $verification_mode<br>" .
			"producer: " . $kshow->getProducerId() . ", likuser id: " . $this->likuser_id . "<br>". 
			"permission: $permissions, pwd: $pwd<br>";
*/
		/*
		 const KSHOW_PERMISSION_EVERYONE = 1;
		 const KSHOW_PERMISSION_JUST_ME = 2;
		 const KSHOW_PERMISSION_INVITE_ONLY = 3;
		 const KSHOW_PERMISSION_REGISTERED = 4;
		 */

//		echo ( "$kshow_id , $verification_mode , $allow_redirect , $permissions\n" );
		
		$res = true;

		debugUtils::log ( "kshow_id [$kshow_id], verification_mode: " . $verification_mode . " permissions: $permissions" );
		
		switch ( $permissions )
		{
			case kshow::KSHOW_PERMISSION_EVERYONE:
				break;
			case kshow::KSHOW_PERMISSION_REGISTERED:
				// if users are authenticated already - there will be no work here
				$res = $this->forceAuthentication( $allow_redirect );
				break;
			case kshow::KSHOW_PERMISSION_JUST_ME:
				if ( $force_auth )	
				{
					$res = $this->forceAuthentication( $allow_redirect );
					if ( !$res ) break; // user was not authenticated but was supposed to be - don't continue
				}
				
				if ( ! $this->isProducer( $kshow ) ) // ( $kshow->getProducerId() != $this->likuser_id )
				{
					$res = $this->justMe ( $allow_redirect );
				}
				break;
			case kshow::KSHOW_PERMISSION_INVITE_ONLY:
				if ( $force_auth )	
				{
					$res = $this->forceAuthentication( $allow_redirect );
					if ( !$res ) break; // user was not authenticated but was supposed to be - don't continue
				}
				// no need to force verification on producer himself
				if ( ! $this->isProducer( $kshow ) ) // $kshow->getProducerId() != $this->likuser_id )
				{
					$res = $this->inviteOnly ( $kshow , $verification_mode , $allow_redirect ) ;
				}
				break;
			case kshow::KSHOW_PERMISSION_NONE:
				// do nothing - exit
				throw new sfStopException();
				break;				
		}

		return $res;
	}

	protected function isProducer ( $kshow )
	{
		return 	( $kshow->getProducerId() == $this->getLoggedInUserId() );
	}
	
	protected function justMe ( $allow_redirect = true )
	{
		if ( ! $allow_redirect ) return false;
		$this->setFlash('sign_in_referer', $_SERVER["REQUEST_URI"]);
		return $this->forward('login','justMe');
	}

	protected function inviteOnly ( $kshow , $verification_mode , $allow_redirect = true )
	{
		$kshow_id = $kshow->getId();

		if ( ! $this->isValidExpiryCredential ( "$verification_mode" . "kshow" . $kshow_id) )
		{
/*
 * TODO - PRIVILEGES - should not enforce authentication
 */

			//$this->forceAuthentication( $allow_redirect );
			if ( ! $allow_redirect ) return false;
/*			
			$this->setCredentialByName ( "requestKshow" , $kshow_id );
			$this->setCredentialByName ( "requestKshowName" , $kshow->getName() );
			$this->setCredentialByName ( "verificationMode" , $verification_mode );
	*/		
			// be sure the likuser can 
			$this->setFlash('sign_in_referer', $_SERVER["REQUEST_URI"]);
			return $this->forward('login','inviteOnlyForm');
		}
		
		return true;
	}
	
	
	protected function setCredentialByName  ( $cred_name , $cred_val )
	{
//		debugUtils::log( "setCredentialByName: [$cred_name]=[$cred_val]" );
		$use_cookies = $this->getLoggedInUser() == NULL;
		if ( $use_cookies )
		{
			requestUtils::setSecureCookie( $cred_name , $cred_val , self::COOKIE_CREDENTAIL_IV , self::COOKIE_EXPIRY  );
//			self::addToSecureCookieList ( $cred_name );
		}
		else
		{
			// if the user is logged in - use the credential mechaism
			$real_cred = $this->findCredential ( $cred_name );
			if ( $real_cred )	$this->getUser()->removeCredential( $real_cred );
			$this->getUser()->addCredential ( $cred_name . ":" . $cred_val );
		}
		
	}

	protected function getCredentialByName ( $cred_name )
	{
		$use_cookies = ( $this->getLoggedInUser() == NULL );
		if ( $use_cookies )
		{
			$val = requestUtils::getSecureCookie( $cred_name , self::COOKIE_CREDENTAIL_IV );
		}
		else
		{
			// if the user is logged in - use the credential mechaism
			$real_cred = $this->findCredential ( $cred_name );
			if ( !$real_cred ) return NULL;
			
//			$cred_list = $this->getUser()->listCredentials();
			$values = explode ( ":" , $real_cred );
			$val = $values[1];
		}
		
//		debugUtils::log ( "getCredentialByName  [$cred_name]=[$val]" );
		
		return $val;
	}	

	// use $response->setCookie to reset the values 
	public static function removeAllSecureCookiesFromResponse ( $response )
	{
		requestUtils::removeAllSecureCookies ();
/*		
		$secure_cookie_list = requestUtils::getSecureCookie( "secure_cookie_list" , self::COOKIE_CREDENTAIL_IV );
		
		if ( empty ( $secure_cookie_list ) ) return;

		$response->setCookie( "secure_cookie_list" , '', time()-3600, '/');		
		
		$arr = explode ( ";" , $secure_cookie_list );
		foreach ( $arr as $secure_cookie_hashed_name )
		{
			$response->setCookie($secure_cookie_hashed_name, '', time()-3600, '/');  
		}
*/		 
	}
	
	public static function removeAllSecureCookies ( )
	{
		requestUtils::removeAllSecureCookies ();
/*		
		$secure_cookie_list = requestUtils::getSecureCookie( "secure_cookie_list" , self::COOKIE_CREDENTAIL_IV );
		if ( empty ( $secure_cookie_list )) return;
			
		$arr = explode ( ";" , $secure_cookie_list );
		foreach ( $arr as $secure_cookie_hashed_name )
		{
			requestUtils::removeSecureCookieByName ( $secure_cookie_hashed_name );
		}
		requestUtils::removeSecureCookieByName( "secure_cookie_list" );
*/
	}
/*	
	private function addToSecureCookieList ( $name )
	{
		 $secure_cookie_list = requestUtils::getSecureCookie( "secure_cookie_list" , self::COOKIE_CREDENTAIL_IV );
		 if ( empty ( $secure_cookie_list ) )
		 	$secure_cookie_list = "";
		 $hashname = requestUtils::getSecureCookieName ( $name );
		 if ( strpos ( $secure_cookie_list , $hashname . ";" ) === false )
		 {
		 	$secure_cookie_list .= $hashname . ";";
		 	requestUtils::setSecureCookie( "secure_cookie_list" , $secure_cookie_list , self::COOKIE_CREDENTAIL_IV , self::COOKIE_EXPIRY );
		 }
	}
*/
	protected function hasCredentialByName  ( $cred_name )
	{
		return ( $this->findCredential( $cred_name ) != NULL );
	}
	
	private function findCredential ( $cred_name )
	{
		$cred_list = $this->getUser()->listCredentials();
		$prefix = $cred_name . ":";
		foreach ( $cred_list as $cred_index => $val )
		{
			if ( kString::beginsWith( $val , $prefix))		return $val;
		}

		return NULL;
	}
	
	protected function isValidExpiryCredential ( $cred_name )
	{
		// assume the val is time that might have expired
		$val = $this->getCredentialByName ( $cred_name );
//		echo ( "isValidExpiryCredential, $cred_name = $val" );
		if ( !$val ) return false;
		return $val > time() ;
	}
	
	protected function setExpiryCredential ( $cred_name , $ttl_in_sec )
	{
		// assume the val is time that might have expired
		$val = time() + $ttl_in_sec ;
		$this->setCredentialByName( $cred_name , $val );
	}	
 
  protected function getUserzoneCookie() 
  {
  	$cookie = $this->getContext()->getRequest()->getCookie('userzone');
  	$length = strlen($cookie);
  	if ($length <= 0)
  		return null;
  		
  	$serialized_data = substr($cookie, 0, $length - 32);
  	$hash_signiture = substr($cookie, $length - 32);
  	  	 
  	// check the signiture
  	if (md5($serialized_data . $this->cookieSecret) != $hash_signiture)
  		return null;
  	
  	$userzone_data = unserialize(base64_decode($serialized_data));
  	
  	return array($userzone_data['id'], $userzone_data['email'], $userzone_data['screenname']);
  }
  
  protected function followRedirectCookie()
  {
	$return_to = @$_COOKIE["kaltura_redirect"];
	if ($return_to)
	{
		$return_to = base64_decode($return_to);
		// make the redirect cookie expire
		setcookie( 'kaltura_redirect', '', time() - 86400 , '/' );
		
		$this->redirect( "http://$return_to/".$_SERVER["REQUEST_URI"] );
	}
  }
}

?>