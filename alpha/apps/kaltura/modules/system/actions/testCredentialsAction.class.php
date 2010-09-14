<?php

class testCredentialsAction extends kalturaAction
{
	private $_test_array = NULL;
	private $_bottom_line = null;
	
	/**
	 * Will test the health of the system
	 */
	public function execute()
	{
		//$this->getUser()->setAuthenticated(true);
		$this->cred1 = $this->getCredentialByName( "test" );
		$this->setCredentialByName( "test" , "1234" );
		$this->cred2 = $this->getCredentialByName( "test" );
		
		$this->cred3 = $this->isValidExpiryCredential( "exp_cred" );
		$this->setExpiryCredential( "exp_cred" , 25 );
		$this->cred4 = $this->isValidExpiryCredential( "exp_cred" );
	}
}

?>