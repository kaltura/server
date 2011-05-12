<?php
/**
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaMemoryCleaner extends IKalturaBase
{
	public static function cleanMemory();
}