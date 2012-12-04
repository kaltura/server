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
		// Prevent the page fron being embeded in an iframe
		header( 'X-Frame-Options: DENY' );

		// Check if user already logged in and redirect to kmc2
		if( $this->getRequest()->getCookie('kmcks') ) {
			$this->redirect('kmc/kmc2');
		}
		
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
