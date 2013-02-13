<?php
/**
 * Client which makes cURL call to provision a new stream
 *
 */
class AkamaiUniversalStreamClient
{
	public $systemUser;
	
	public $systemPassword;
	
	public $domainName;
	
	public static $baseServiceUrl;
	
	public function __construct($systemUser, $systemPassword, $domainName)
	{
		$this->systemUser = $systemUser;
		$this->systemPassword = $systemPassword;	
		$this->domainName = $domainName;
	}
	
	public function provisionStream (KalturaAkamaiUniversalStreamConfiguration $streamConfiguration)
	{
		$url = self::$baseServiceUrl . "/{$this->domainName}/stream";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $streamConfiguration->getXML());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
		return curl_exec($ch);
	}
	
	public function deleteStream ($streamId)
	{
		$url = self::$baseServiceUrl . "/{$this->domainName}/stream/$streamId";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
		return curl_exec($ch);
	}

	
}