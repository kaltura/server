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

	/**
	 * Should transform DFXP to a more "common" format
	 */
	public $transformDfxp;

	/**
	 * @var string
	 * json string containing default parameters to be set on individual VoiceBase actions
	 */
	public $defaultParams;
	
	function __construct($apiKey, $apiPassword)
	{
		$this->apiKey = $apiKey;
		$this->apiPassword = $apiPassword;
	}
}
