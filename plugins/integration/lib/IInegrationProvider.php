<?php
/**
 * @package plugins.integration
 * @subpackage lib
 * 
 */
interface IIntegrationProvider
{
	/**
	 * @return array<Permission>
	 */
	public function getPermissions($partnerId);

	/**
	 * @return bool 
	 */
	public function validateKs($ks, $job);
}
