<?php
/**
 * Local configuration of a new stream opposite Akamai
 *
 */
class KalturaAkamaiUniversalStreamConfiguration
{
	
	/**
	 * @var int
	 */
	public $id;
	/**
	 * @var string
	 */
	public $streamName;
	
	/**
	 * @var string
	 */
	public $streamType;
	
	/**
	 * @var string
	 */
	public $primaryContact;
	
	/**
	 * @var string
	 */
	public $secondaryContact;
	
	/**
	 * @var string
	 */
	public $notificationEmail;
	
	/**
	 * @var KalturaDVRStatus
	 */
	public $dvrEnabled;
	
	/**
	 * @var int
	 */
	public $dvrWindow;
	
	/**
	 * @var string
	 */
	public $primaryEncodingIP;
	
	/**
	 * @var string
	 */
	public $secondaryEncodingIP;
	
	/**
	 * @var string
	 */
	public $encoderPassword; 
	
	/**
	 * @var string
	 */
	public $primaryEntryPoint;
	
	/**
	 * @var string
	 */
	public $secondaryEntryPoint;
	
	/**
	 * @var string
	 */
	public $encoderUserName;
	
	/**
	 * @return string
	 */
	public function getXML ()
	{
		$result = new SimpleXMLElement("<stream/>");
		$result->addChild("stream-type", $this->streamType);
		$result->addChild("stream-name", $this->streamName);
		$result->addChild("primary-contact-name", $this->primaryContact);
		$result->addChild("secondary-contact-name", $this->secondaryContact);
		$result->addChild("notification-email", $this->notificationEmail);
		
		$encoderSettings = $result->addChild("encoder-settings");
		$encoderSettings->addChild("primary-encoder-ip", $this->primaryEncodingIP);
		$encoderSettings->addChild("backup-encoder-ip", $this->secondaryEncodingIP);
		$encoderSettings->addChild("password", $this->encoderPassword);
		
		$dvrSettings = $result->addChild("dvr-settings");
		$dvrSettings->addChild("dvr", $this->dvrEnabled ? "Enabled" : "Disabled");
		$dvrSettings->addChild("dvr-window", $this->dvrWindow);
		
		return $result->saveXML();
	}
	
	/**
	 * This function adds/replaces values on $this stream configuration with values from the given XML.
	 * @param SimpleXMLElement $xml
	 */
	public function fromXML (SimpleXMLElement $xml)
	{
		$this->id = self::getXMLNodeValue('stream-id', $xml);
		if (!$this->id)
		{
			throw new Exception("Necessary parameter stream-id missing from returned result");
		}
		
		
		$this->streamName = self::getXMLNodeValue('stream-name', $xml);
		$encoderSettingsNodeName = 'encoder-settings';
		$encoderSettings = $xml->$encoderSettingsNodeName;
		$this->encoderUserName = strval($encoderSettings->username);
		if (!$this->encoderUserName)
		{
			throw new Exception("Necessary parameter [username] missing from returned result");
		}		
		//Parse encoding primary and secondary entry points
		$entryPoints = $xml->xpath('/stream/entrypoints/entrypoint');
		if (!$entryPoints || !count($entryPoints))
			throw new Exception('Necessary configurations for entry points missing from the returned result');
			
		foreach ($entryPoints as $entryPoint)
		{
			/* @var $entryPoint SimpleXMLElement */
			$domainNodeName = 'domain-name';
			$domainName = $entryPoint->$domainNodeName;
			if (!$domainName)
			{
				throw new Exception('Necessary URL for entry point missing from the returned result');
			}
			if (strval($entryPoint->type) == 'Backup')
			{
				$this->secondaryEntryPoint = $domainName;
			}
			else
			{
				$this->primaryEntryPoint = $domainName;
			}
		}
		
	}
	
	/**
	 * @param string $nodeName
	 * @param SimpleXMLElement $xml
	 * @return string
	 */
	private static function getXMLNodeValue ($nodeName, SimpleXMLElement $xml)
	{
		return strval($xml->$nodeName);
	}

}