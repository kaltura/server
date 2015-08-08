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
	
	function __construct($params)
	{
		if(isset($params['apiKey']))
			$this->apiKey = $params['apiKey'];
		if(isset($params['apiPassword']))
			$this->apiPassword = $params['apiPassword'];
	}
}
