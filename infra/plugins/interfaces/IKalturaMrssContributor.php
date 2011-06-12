<?php
/**
 * Enable the plugin to add additional XML nodes and attributes to entry MRSS
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaMrssContributor extends IKalturaBase
{
	/**
	 * @param entry $entry
	 * @param SimpleXMLElement $mrss
	 * @return SimpleXMLElement
	 */
	public function contribute(entry $entry, SimpleXMLElement $mrss);	
}