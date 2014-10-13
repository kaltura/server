<?php


/**
 * Skeleton subclass for representing a row from the 'invalid_session' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class invalidSession extends BaseinvalidSession {
	
	const INVALID_SESSION_TYPE_KS = 0;
	const INVALID_SESSION_TYPE_SESSION_ID = 1;

	public function getCacheInvalidationKeys()
	{
		return array("invalidSession:ks=".strtolower($this->getKs()));
	}
} // invalidSession
