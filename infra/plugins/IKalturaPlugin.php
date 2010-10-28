<?php
interface IKalturaPlugin
{
	/**
	 * @return string the name of the plugin
	 */
	public static function getPluginName();
	
	/**
	 * Return all instances in the current plugin that implements the interface
	 * @param string $intrface
	 * @return array<IKalturaPlugin>
	 */
	public function getInstances($intrface);
}