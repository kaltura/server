<?php
/**
 * @package plugins.httpNotification
 * @subpackage model.data
 */
abstract class kHttpNotificationData
{
	/**
	 * Applies scope upon creation
	 * @param kScope $scope
	 */
	abstract public function setScope(kScope $scope);
}