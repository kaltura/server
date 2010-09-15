<?php

require_once ( "kalturaAction.class.php" );

class logoutAction extends kalturaAction
{
	public function execute ( ) 
	{
		$ksStr = $this->getP("kmcks");
		if($ksStr)
			kSessionUtils::killKSession($ksStr);
		
		setcookie('pid', "", 0, "/");
		setcookie('subpid', "", 0, "/");
		setcookie('uid', "", 0, "/");
		setcookie('kmcks', "", 0, "/");
		setcookie('screen_name', "", 0, "/");
		setcookie('email', "", 0, "/");
		
		$queryString = isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
		$this->redirect('/kmc/kmc' . $queryString);
	}
}
?>