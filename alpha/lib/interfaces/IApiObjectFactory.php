<?php
/**
 * @package Core
 * @subpackage model.interfaces
 */ 
interface IApiObjectFactory
{
	/**
	 * Should return the API object
	 *
	 */
	public static function getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile = null);
}