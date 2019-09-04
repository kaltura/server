<?php


/**
 * Skeleton subclass for representing a row from the 'sso' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.sso
 * @subpackage model
 */
class Sso extends BaseSso {

	const REDIRECT_URL = 'redirectUrl';

	public function setRedirectUrl ($v)	{ $this->putInCustomData ( self::REDIRECT_URL, $v);	}

	public function getRedirectUrl ()	{ return $this->getFromCustomData(self::REDIRECT_URL);	}

} // Sso
