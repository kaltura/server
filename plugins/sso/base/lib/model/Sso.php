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

	const DATA = 'data';

	public function setData($v)	{ $this->putInCustomData ( self::DATA, $v);	}

	public function getData ()	{ return $this->getFromCustomData(self::DATA);	}

} // Sso
