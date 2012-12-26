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
	abstract public function editManifestHeader ($header);
	
	/**
	 * @param string $footer
	 * @return string
	 */
	abstract public function editManifestFooter ($footer);
	
	/**
	 * @param array $flavors
	 * @return array
	 */
	abstract public function editManifestFlavors ($flavors);
}