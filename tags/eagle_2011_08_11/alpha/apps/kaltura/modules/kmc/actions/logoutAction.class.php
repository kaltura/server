<?php

require_once ( "kalturaAction.class.php" );

class logoutAction extends kalturaAction
{
	public function execute ( ) 
	{
		$ksStr = $this->getP("ks");
		if($ksStr) {
			kSessionUtils::killKSession($ksStr);
			KalturaLog::debug("Killing session with ks - [$ksStr], decoded - [".base64_decode($ksStr)."]");
		}
		else {
			KalturaLog::err('logoutAction called with no KS');
		}
		
		setcookie('pid', "", 0, "/");
		setcookie('subpid', "", 0, "/");
		setcookie('uid', "", 0, "/");
		setcookie('kmcks', "", 0, "/");
		setcookie('screen_name', "", 0, "/");
		setcookie('email', "", 0, "/");

		return sfView::NONE; //redirection to kmc/kmc is done from java script
	}
}
?>