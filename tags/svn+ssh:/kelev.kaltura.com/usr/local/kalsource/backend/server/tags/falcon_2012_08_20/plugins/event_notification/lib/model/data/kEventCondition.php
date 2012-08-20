<?php
/**
 * @package plugins.eventNotification
 * @subpackage model.data
 * @abstract
 */
abstract class kEventCondition
{
	/**
	 * Evaluates the condition
	 * @param kEventScope $scope
	 * @return boolean
	 */
	abstract public function fulfilled(kEventScope $scope);
}
