<?php
/**
 * Enable to plugin to add images to external applications
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaApplicationImages extends IKalturaBase
{
	/**
	 * Returns the physical path to the image file
	 * @param string $imgName
	 * @return string
	 */
	public static function getImagePath($imgName);	
}