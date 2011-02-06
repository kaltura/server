<?php
/**
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaEventConsumers extends IKalturaBase
{
	/**
	 * @return array
	 */
	public static function getEventConsumers();	
}