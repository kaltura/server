<?php
/**
 * @package plugins.cielo24
 */
class Cielo24Options
{
	/**
	 * cielo24 user-name
	 */
	public $username;
	
	/**
	 * cielo24 password
	 */
	public $password;
	
	function __construct($username, $password)
	{
		$this->username = $username;
		$this->password = $password;
	}
}
