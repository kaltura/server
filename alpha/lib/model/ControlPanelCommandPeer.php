<?php

/**
 * Subclass for performing query and update operations on the 'control_panel_command' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class ControlPanelCommandPeer extends BaseControlPanelCommandPeer
{
	public static function deleteBySchedulerConfigId($schedulerConfiguredId)
	{
		$criteria = new Criteria();
		$criteria->add(ControlPanelCommandPeer::SCHEDULER_CONFIGURED_ID, $schedulerConfiguredId);

		ControlPanelCommandPeer::doDelete($criteria);
	}
}
