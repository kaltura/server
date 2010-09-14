<?php


/**
 * Skeleton subclass for representing a row from the 'sphinx_log' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class SphinxLog extends BaseSphinxLog {

	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		
		$this->setDc(kDataCenterMgr::getCurrentDcId());
	}
	
} // SphinxLog
