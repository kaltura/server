<?php
/**
 * Enable the plugin to add additional XML nodes and attributes to specific schema type
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaSchemaContributor extends IKalturaBase
{
	/**
	 * @param SchemaType $type
	 * @return bool
	 */
	public static function isContributingToSchema($type);
	
	/**
	 * @param SchemaType $type
	 * @param SimpleXMLElement $xsd
	 * @return SimpleXMLElement XSD
	 */
	public static function contributeToSchema($type, SimpleXMLElement $xsd);
	
	/**
	 * @param SchemaType $type
	 * @return SimpleXMLElement XSD
	 */
	public static function getPluginSchema($type);
}