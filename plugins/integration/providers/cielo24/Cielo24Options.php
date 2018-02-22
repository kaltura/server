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
	
	/**
	 * cielo24 base url
	 */
	 public $baseUrl;
	
	/**
 	 * Should transform DFXP
 	 */
 	public $transformDfxp;
	
	/**
	 * Default set of parameters to be sent for get_caption and get_transcript
	 * @var string
	 */
	public $defaultParams;

	function __construct($username, $password, $baseUrl = null)
	{
		$this->username = $username;
		$this->password = $password;
		if(isset($baseUrl))
			$this->baseUrl = $baseUrl;
	}
}
