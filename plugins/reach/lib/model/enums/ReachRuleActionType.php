<?php
/**
 * @package plugins.reach
 * @subpackage model.enum
 */
class ReachRuleActionType implements IKalturaPluginEnum, RuleActionType
{
	const ADD_ENTRY_VENDOR_TASK = 'ADD_ENTRY_VENDOR_TASK';
	
	/**
	 * 
	 * Returns the dynamic enum additional values
	 */
	public static function getAdditionalValues()
	{
		return array(
			'ADD_ENTRY_VENDOR_TASK' => self::ADD_ENTRY_VENDOR_TASK,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			ReachPlugin::getApiValue(self::ADD_ENTRY_VENDOR_TASK) => 'Add entry vendor task for entry being processed in current execution scope',
		);
	}
}