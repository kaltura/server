<?php

require_once ( "kalturaAction.class.php" );

class kmcAction extends kalturaAction
{
	public function execute ( ) 
	{
		$this->beta = $this->getRequestParameter( "beta" );
		$this->kmc_login_version 	= kConf::get('kmc_login_version');
		$this->setPassHashKey = $this->getRequestParameter( "setpasshashkey" );
		$this->hashKeyErrorCode = null;
		if ($this->setPassHashKey) {
			try {
				if (!adminKuserPeer::isHashKeyValid($this->setPassHashKey)) {
					$this->hashKeyErrorCode = kAdminKuserException::NEW_PASSWORD_HASH_KEY_INVALID;
				}
			}
			catch (kCoreException $e) {
				$this->hashKeyErrorCode = $e->getCode();
			}
		}
		sfView::SUCCESS;
	}
}
?>