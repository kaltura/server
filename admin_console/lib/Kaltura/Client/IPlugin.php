<?php
/**
 * @package Admin
 * @subpackage Client
 */
interface Kaltura_Client_IPlugin
{
	/**
	 * @return Kaltura_Client_ClientPlugin
	 */
	public static function get(Kaltura_Client_Client $client);
	
	/**
	 * @return array<Kaltura_Client_ServiceBase>
	 */
	public function getServices();
	
	/**
	 * @return string
	 */
	public function getName();
}
