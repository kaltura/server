<?php


/**
 * Skeleton subclass for representing a row from the 'audit_trail_config' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.audit
 * @subpackage model
 */
class AuditTrailConfig extends BaseAuditTrailConfig {

	public function actionEnabled($action)
	{
		$actions = explode(',', $this->getActions());
		return in_array($action, $actions);
	}
	
} // AuditTrailConfig
