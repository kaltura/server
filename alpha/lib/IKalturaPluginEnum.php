<?php
interface IKalturaPluginEnum
{
	/**
	 * @return string
	 */
	public function getEnumClass();
	
	/**
	 * @return string
	 */
	public function getPluginName();
	
	/**
	 * @return array
	 */
	public static function getAdditionalValues();
	
	/**
	 * @return IKalturaPluginEnum
	 */
	public static function get();
}