<?php
/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
require_once ( "kalturaSystemAction.class.php" );

/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
class loginAction extends kalturaSystemAction
{
	private static $PASSWORD = "30d390fb24c8e80a880e4f8bfce7a3a06757f1c7";

	public function execute ()
	{
		$this->result = 0;
		if ( @$_REQUEST["exit"] == "true" )
		{
			$this->systemLogout();
			$login = NULL;
			$password = NULL;		
		}
		else
		{
			$login = @$_REQUEST["login"];
			$password = @$_REQUEST["pwd"];
		}
		//echo "login: $login, password: $password";

		$this->login = $login;
		
		$this->sign_in_referer = @$_REQUEST ["sign_in_referer"];
		if ( empty ( $this->sign_in_referer ) )
		 	$this->sign_in_referer = $this->getFlash( "sign_in_referer");
		
		if ( empty ( $login ) || empty ( $password ))
		{
			$this->result = 0;
		}
		else
		{
			if ( sha1($password) == kConf::get ( "system_pages_login_password" ) ) //self::$PASSWORD )
			{
				$this->systemAuthenticated();
				
				if ( empty ( $this->sign_in_referer ) ) 
				{
					// should go back - the original hit was to this page - no reason to go back or refresh
					$this->result = 2;	
				}
				else
				{
					$this->result = 1;
				}
			}
			else
			{
				$this->result = -1;
			}
		}
	}
}
?>