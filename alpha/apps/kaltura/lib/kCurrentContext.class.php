<?php
/**
 * Will hold the curernt context of the API call / current running batch.
 * The inforamtion is static per call and can be used from anywhare in the code. 
 */
class kCurrentContext
{
	/**
	 * @var string
	 */
	public static $ks;
	
	/**
	 * @var int
	 */
	public static $partner_id;

	/**
	 * @var int
	 */
	public static $ks_partner_id;
	
	/**
	 * @var string
	 */
	public static $uid;
	
	/**
	 * @var string
	 */
	public static $ks_uid;

	/**
	 * @var string
	 */
	public static $ps_vesion;
	
	/**
	 * @var string
	 */
	public static $call_id;
	
	/**
	 * @var string
	 */
	public static $service;
	
	/**
	 * @var string
	 */
	public static $action;
	
	/**
	 * @var string
	 */
	public static $host;
	
	/**
	 * @var string
	 */
	public static $client_version;
	
	/**
	 * @var string
	 */
	public static $client_lang;
	
	/**
	 * @var string
	 */
	public static $user_ip;
	
}
?>