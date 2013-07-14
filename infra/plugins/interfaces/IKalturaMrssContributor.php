<?php
/**
 * Enable the plugin to add additional XML nodes and attributes to entry MRSS
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaMrssContributor extends IKalturaBase
{
	/**
	 * @param BaseObject $object
	 * @param SimpleXMLElement $mrss
	 * @param kMrssParameters $mrssParams
	 * @return SimpleXMLElement
	 */
	public function contribute(BaseObject $object, SimpleXMLElement $mrss, kMrssParameters $mrssParams = null);	

	/**
	 * Function returns the object feature type for the use of the KmrssManager
	 * 
	 * @return int
	 */
	public function getObjectFeatureType ();
}