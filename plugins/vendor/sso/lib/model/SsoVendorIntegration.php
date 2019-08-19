<?php
/**
 * @package plugins.sso
 * @subpackage model
 */

class SsoVendorIntegration extends VendorIntegration
{
	const REDIRECT_URL = 'redirectUrl';

	public function setRedirectUrl ($v)	{ $this->putInCustomData ( self::REDIRECT_URL, $v);	}

	public function getRedirectUrl ()	{ return $this->getFromCustomData(self::REDIRECT_URL);	}
}