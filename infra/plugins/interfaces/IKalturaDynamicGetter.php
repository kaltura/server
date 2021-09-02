<?php
/**
 * enable the plugin to overload getters
 * @package infra
 * @subpackage Plugins
 */

interface IKalturaDynamicGetter extends IKalturaBase
{
	/**
	 * @param $object
	 * @param $context - binding string between the caller and the final
	 * executor
	 * @param $output - the new output value
	 * @return bool - continue execute true/false
	 */
	public function getter($object, $context, &$output);
}