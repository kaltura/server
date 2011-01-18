<?php
class extloginAction extends kalturaAction
{
	public function execute()
	{
		$this->partner_id = $this->getP ( "partner_id" );
		$this->subp_id = $this->getP ( "subp_id" );
		$this->uid = $this->getP ( "uid" );
		$this->ks = $this->getP ( "ks" );
		$this->screen_name = $this->getP ( "screen_name" );
		
		// crack KS
		$ksObj = null;
		if ($this->ks)
		{
			try { $ksObj = kSessionUtils::crackKs($this->ks); }
			catch (Exception $e) { $ksObj = null; };
		}
		
		// if no user id defined -> get from ks
		if (!$this->uid && $ksObj)
		{
			$this->uid = $ksObj->user;
		}
		
		// if no screen name defined -> get kuser object
		if (!$this->screen_name && $this->partner_id && $this->uid)
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid($this->partner_id, $this->uid, true);
			if ($kuser) {
				$this->screen_name = $kuser->getScreenName();
			}
		}		
		
		
		$exp = 0;
		$path = "/";
		
		$this->getResponse()->setCookie("pid", $this->partner_id, $exp, $path);
		$this->getResponse()->setCookie("subpid", $this->subp_id, $exp, $path);
		$this->getResponse()->setCookie("uid", $this->uid, $exp, $path);
		$this->getResponse()->setCookie("kmcks", $this->ks, $exp, $path);
		$this->getResponse()->setCookie("screen_name", $this->screen_name, $exp, $path);
		$this->redirect('kmc/kmc2');
	}
}
?>
