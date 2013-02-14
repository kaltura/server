<?php


/**
 * Skeleton subclass for representing a row from the 'partner_load' table.
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
class PartnerLoad extends BasePartnerLoad {
	
	/**
	 * (non-PHPdoc)
	 * This trick was done in order to avoid the not-null constraint for the job sub type
	 * http://stackoverflow.com/questions/10462918/mysql-are-not-null-constraints-needed-for-primary-keys
	 * @see BasePartnerLoad::setJobSubType()
	 */
	public function setJobSubType($v) {
		if(is_null($v)) 
			$v = 0;
		parent::setJobSubType($v);
	}
	
} // PartnerLoad
