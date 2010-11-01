<?php
interface IKalturaPlugin extends IKalturaBase
{
	/**
	 * @return string the name of the plugin
	 */
	public static function getPluginName();
}