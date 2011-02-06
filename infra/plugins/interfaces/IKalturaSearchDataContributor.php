<?php
/**
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaSearchDataContributor extends IKalturaBase
{
	/**
	 * Return textual search data to be associated with the object
	 * 
	 * @param BaseObject $object
	 * @return string
	 */
	public static function getSearchData(BaseObject $object);
}