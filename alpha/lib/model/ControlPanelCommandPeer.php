<?php

/**
 * Subclass for performing query and update operations on the 'control_panel_command' table.
 *
 * 
 *
 * @package lib.model
 */ 
class ControlPanelCommandPeer extends BaseControlPanelCommandPeer
{
	public static function deleteBySchedulerConfigId($schedulerId)
	{
		$criteria = new Criteria();
		$criteria->add(ControlPanelCommandPeer::SCHEDULER_CONFIGURED_ID, $schedulerId);

		ControlPanelCommandPeer::doDelete($criteria);
	}
}
