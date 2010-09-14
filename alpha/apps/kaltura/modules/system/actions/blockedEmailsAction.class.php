<?php
require_once ( "kalturaSystemAction.class.php" );

class blockedEmailsAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();		
	}
}
?>