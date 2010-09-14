<?php

require_once ( "kalturaAction.class.php" );

class kmcAction extends kalturaAction
{
	public function execute ( ) 
	{
		$this->beta = $this->getRequestParameter( "beta" );
		$this->setPassHashKey = $this->getRequestParameter( "setpasshashkey" );
		$this->hashKeyErrorCode = null;
		try {
			if (adminKuserPeer::isHashKeyValid()) {
				$this->hashKeyErrorCode = kAdminKuserException::NEW_PASSWORD_HASH_KEY_INVALID;
			}
		}
		catch (kCoreException $e) {
			$this->hashKeyErrorCode = $e->getCode();
		}
		sfView::SUCCESS;
	}
}
?>