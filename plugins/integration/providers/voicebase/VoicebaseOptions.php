<?php
/**
 * @package plugins.voicebase
 */
class VoicebaseOptions
{
	/**
	 * voicebase api-key
	 */
	public $apiKey;
	
	/**
	 * Voicebase password
	 */
	public $apiPassword;
	
	function __construct($apiKey, $apiPassword)
	{
		$this->apiKey = $apiKey;
		$this->apiPassword = $apiPassword;
	}
}
