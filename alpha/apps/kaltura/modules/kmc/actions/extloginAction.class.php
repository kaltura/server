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
