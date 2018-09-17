<?php


/**
 * Skeleton subclass for representing a row from the 'vendor_integration' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.vendor
 * @subpackage model
 */
class VendorIntegration extends BaseVendorIntegration {

	public function setAccessToken ($v)	{ $this->putInCustomData ( "accessToken" , $v);	}
	public function getAccessToken ( )	{ return $this->getFromCustomData( "accessToken" );	}

	public function setRefreshToken ($v)	{ $this->putInCustomData ( "refreshToken" , $v);	}
	public function getRefreshToken ( )	{ return $this->getFromCustomData( "refreshToken" );	}

	public function setExpiresIn ($v)	{ $this->putInCustomData ( "expiresIn" , $v);	}
	public function getExpiresIn( )	{ return $this->getFromCustomData( "expiresIn" );	}

	public function setDeleteContentOnDAuthorization ($v)	{ $this->putInCustomData ( "deleteContentOnDAuthorization" , $v);	}
	public function getDeleteContentOnDAuthorization ( )	{ return $this->getFromCustomData( "deleteContentOnDAuthorization" );	}
	public function setDefaultUserEMail ($v)	{ $this->putInCustomData ( "defaultUserEMail" , $v);	}
	public function getDefaultUserEMail ( )	{ return $this->getFromCustomData( "defaultUserEMail" );	}

	public function setZoomCategory($v)	{ $this->putInCustomData ( "zoomCategory" , $v);	}
	public function getZoomCategory( )	{ return $this->getFromCustomData( "zoomCategory" );	}


	/**
	 * returns all tokens as array
	 * @return array
	 */
	public function getTokens()
	{
		return array(kZoomOauth::ACCESS_TOKEN => $this->getAccessToken(), kZoomOauth::REFRESH_TOKEN => $this->getRefreshToken(),
			kZoomOauth::EXPIRES_IN => $this->getExpiresIn());
	}

	/**
	 * @param array $tokensDataAsArray
	 * @param string $accountId
	 * @throws PropelException
	 */
	public function saveNewTokenData($tokensDataAsArray, $accountId)
	{
		$this->setExpiresIn($tokensDataAsArray[kZoomOauth::EXPIRES_IN]);
		$this->setAccessToken($tokensDataAsArray[kZoomOauth::ACCESS_TOKEN]);
		$this->setRefreshToken($tokensDataAsArray[kZoomOauth::REFRESH_TOKEN]);
		$this->setAccountId($accountId);
		$this->setVendorType(VendorTypeEnum::ZOOM_ACCOUNT);
		$this->save();
	}


} // VendorIntegration
