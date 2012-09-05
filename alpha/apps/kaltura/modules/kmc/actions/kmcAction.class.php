<?php
/**
 * @package    Core
 * @subpackage KMC
 */
require_once ( "kalturaAction.class.php" );

/**
 * @package    Core
 * @subpackage KMC
 */
class kmcAction extends kalturaAction
{
	public function execute ( ) 
	{
		$this->beta = $this->getRequestParameter( "beta" );
		$this->kmc_login_version 	= kConf::get('kmc_login_version');
		$this->setPassHashKey = $this->getRequestParameter( "setpasshashkey" );
		$this->hashKeyErrorCode = null;
		$this->displayErrorFromServer = false;
		if ($this->setPassHashKey) {
			try {
				$loginData = UserLoginDataPeer::isHashKeyValid($this->setPassHashKey);
				$partnerId = $loginData->getConfigPartnerId();
				$partner = PartnerPeer::retrieveByPK($partnerId);
				if ($partner && $partner->getPasswordStructureValidations())
					$this->displayErrorFromServer = true;  			
				
			}
			catch (kCoreException $e) {
				$this->hashKeyErrorCode = $e->getCode();
			}
		}		
		sfView::SUCCESS;
	}
}
