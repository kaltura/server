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
 * @package plugins.reach
 * @subpackage model
 */
class VendorIntegration extends BaseVendorIntegration {

	public function setAccessToken ($v)	{ $this->putInCustomData ( "accessToken" , $v);	}
	public function getAccessToken ( )	{ return $this->getFromCustomData( "accessToken" );	}

	public function setRefreshToken ($v)	{ $this->putInCustomData ( "refreshToken" , $v);	}
	public function getRefreshToken ( )	{ return $this->getFromCustomData( "refreshToken" );	}

	public function setDeleteContentOnDAuthorization ($v)	{ $this->putInCustomData ( "deleteContentOnDAuthorization" , $v);	}
	public function getDeleteContentOnDAuthorization ( )	{ return $this->getFromCustomData( "deleteContentOnDAuthorization" );	}

	public function setEnableUpload ($v)	{ $this->putInCustomData ( "enableUpload" , $v);	}
	public function getEnableUpload ( )	{ return $this->getFromCustomData( "enableUpload" );	}

	public function setDefaultUserEMail ($v)	{ $this->putInCustomData ( "defaultUserEMail" , $v);	}
	public function getDefaultUserEMail ( )	{ return $this->getFromCustomData( "defaultUserEMail" );	}

} // VendorIntegration
