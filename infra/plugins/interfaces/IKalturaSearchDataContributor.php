<?php
/**
 * Enable the plugin to return additional data to be saved on indexed object
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaSearchDataContributor extends IKalturaBase
{
	/**
	 * Return textual search data to be associated with the object
	 * 
	 * @param BaseObject $object
	 * @return ArrayObject
	 */
	public static function getSearchData(BaseObject $object);
}