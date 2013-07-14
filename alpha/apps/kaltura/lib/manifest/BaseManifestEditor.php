<?php
/**
 * Base class for manifest content editors 
 * 
 * @package Core
 *
 */
abstract class BaseManifestEditor
{
	/**
	 * @param string $header
	 * @return string
	 */
	public function editManifestHeader ($header)
	{
		return $header;
	}
	
	/**
	 * @param string $footer
	 * @return string
	 */
	public function editManifestFooter ($footer)
	{
		return $footer;
	}
	
	/**
	 * @param array $flavors
	 * @return array
	 */
	public function editManifestFlavors (array $flavors)
	{
		return $flavors;
	}
}