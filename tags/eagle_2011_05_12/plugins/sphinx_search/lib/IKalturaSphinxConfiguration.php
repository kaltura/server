<?php
interface IKalturaSphinxConfiguration extends IKalturaBase
{
	/**
	 * @return string path to configuration file
	 */
	public static function getSphinxConfigPath();
}