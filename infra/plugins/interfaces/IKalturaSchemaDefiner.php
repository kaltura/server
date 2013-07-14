<?php
/**
 * Enable the plugin to define new XML schema type
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaSchemaDefiner extends IKalturaBase
{
	/**
	 * @param SchemaType $type
	 * @return SimpleXMLElement XSD
	 */
	public static function getPluginSchema($type);
}