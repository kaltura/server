<?php
/**
 * Enable the plugin to return additional data to be saved on indexed object
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaDynamicAttributeContributer extends IKalturaBase
{
	/**
	 * Return dynamicAttribute to be added to entry's dynamic attributes
	 *
	 * @param entry $entry
	 * @return ArrayObject
	 */
	public static function getDynamicAttribute(entry $entry);
}