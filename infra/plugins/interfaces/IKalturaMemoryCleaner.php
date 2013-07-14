<?php
/**
 * Enable the plugin to clean unused memory, instances and pools
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaMemoryCleaner extends IKalturaBase
{
	public static function cleanMemory();
}