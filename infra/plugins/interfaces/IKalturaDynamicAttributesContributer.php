<?php
/**
 * Enable the plugin to return additional data to be saved on indexed object
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaDynamicAttributesContributer extends IKalturaBase
{
	/**
	 * Return dynamicAttributes to be added to entry's dynamic attributes
	 *
	 * @param IIndexable $object
	 * @return array
	 */
	public static function getDynamicAttributes(IIndexable $object);
}