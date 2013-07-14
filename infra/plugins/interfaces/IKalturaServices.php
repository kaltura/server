<?php
/**
 * Enable adding new API V3 services
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaServices extends IKalturaBase
{
	/**
	 * @return array<string,string> in the form array[serviceName] = serviceClass
	 */
	public static function getServicesMap();
	
}