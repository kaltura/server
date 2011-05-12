<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaSearchAuthData extends KalturaObject
{
	/**
	 * The authentication data that further should be used for search
	 * 
	 * @var string
	 */
	public $authData;
	
	/**
	 * Login URL when user need to sign-in and authorize the search
	 *
	 * @var string
	 */
	public $loginUrl;
	
	/**
	 * Information when there was an error
	 *
	 * @var string
	 */
	public $message;
	
	private static $map_between_objects = array
	(
	    "authData",
		"loginUrl",
		"message",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
