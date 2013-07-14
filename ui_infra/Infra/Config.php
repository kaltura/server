<?php
/**
 * @package UI-infra
 * @subpackage Config
 */

class Infra_Config {

	/**
	 * Simplfies getting application configuration values
	 * Usage: Infra_Config::get('settings.serviceUrl')
	 * 
	 * @param string $str
	 * @return Zend_Config
	 */
	public static function get( $str = null )
	{
		if( $str ) {
			$config = Zend_Registry::get('config');

			foreach (explode('.', $str) as $key) {
		        if (isset($config->$key)) {
		            $config = $config->$key;
		        }
		        else { return false; }
		    }

			return $config;
		}
	}
}