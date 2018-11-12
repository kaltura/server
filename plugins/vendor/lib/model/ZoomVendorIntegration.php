<?php
/**
 * @package plugins.vendor
 * @subpackage model
 */

class ZoomVendorIntegration extends VendorIntegration
{

	const ACCESS_TOKEN = "accessToken";
	const REFRESH_TOKEN = "refreshToken";
	const EXPIRES_IN = "expiresIn";
	const DELETE_CONTENT_ON_DAUTHORIZATION = "deleteContentOnDAuthorization";
	const DEFAULT_USER_E_MAIL = "defaultUserEMail";
	const ZOOM_CATEGORY = "zoomCategory";
	const ZOOM_CATEGORY_ID = "zoomCategoryId";
	const CREATE_USER_IF_NOT_EXIST = "createUserIfNotExist";

	public function setAccessToken ($v)	{ $this->putInCustomData ( "" . self::ACCESS_TOKEN . "", $v);	}
	public function getAccessToken ( )	{ return $this->getFromCustomData(self::ACCESS_TOKEN);	}

	public function setRefreshToken ($v)	{ $this->putInCustomData ( self::REFRESH_TOKEN, $v);	}
	public function getRefreshToken ( )	{ return $this->getFromCustomData(self::REFRESH_TOKEN);	}

	public function setExpiresIn ($v)	{ $this->putInCustomData ( self::EXPIRES_IN, $v);	}
	public function getExpiresIn( )	{ return $this->getFromCustomData(self::EXPIRES_IN);	}

	public function setDeleteContentOnDAuthorization ($v)	{ $this->putInCustomData ( self::DELETE_CONTENT_ON_DAUTHORIZATION, $v);	}
	public function getDeleteContentOnDAuthorization ( )	{ return $this->getFromCustomData(self::DELETE_CONTENT_ON_DAUTHORIZATION);	}

	public function setDefaultUserEMail ($v)	{ $this->putInCustomData ( self::DEFAULT_USER_E_MAIL, $v);	}
	public function getDefaultUserEMail ( )	{ return $this->getFromCustomData(self::DEFAULT_USER_E_MAIL);	}

	public function setZoomCategory($v)	{ $this->putInCustomData ( self::ZOOM_CATEGORY, $v);	}
	public function getZoomCategory( )	{ return $this->getFromCustomData(self::ZOOM_CATEGORY);	}
	public function unsetCategory( )  {return $this->removeFromCustomData(self::ZOOM_CATEGORY);	}

	public function setZoomCategoryId($v)	{ $this->putInCustomData ( self::ZOOM_CATEGORY_ID, $v);	}
	public function getZoomCategoryId( )	{ return $this->getFromCustomData(self::ZOOM_CATEGORY_ID);	}
	public function unsetCategoryId( )  {return $this->removeFromCustomData(self::ZOOM_CATEGORY_ID);	}

	public function setCreateUserIfNotExist($v) { $this->putInCustomData ( self::CREATE_USER_IF_NOT_EXIST, $v); }
	public function getCreateUserIfNotExist() { return $this->getFromCustomData ( self::CREATE_USER_IF_NOT_EXIST,null, false); }



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

}