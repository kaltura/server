<?php
/**
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaVersion extends IKalturaBase
{
	/**
	 * @return KalturaVersion
	 */
	public static function getVersion();
}