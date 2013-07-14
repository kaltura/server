<?php

class KalturaMonitorError
{
    const EMERG   = 'EMERG';
    const ALERT   = 'ALERT';
    const CRIT    = 'CRIT';
    const ERR     = 'ERR';
    const WARN    = 'WARN';
    const NOTICE  = 'NOTICE';
    const INFO    = 'INFO';
    const DEBUG   = 'DEBUG';
    
	public $level;
	public $code;
	public $description;
}

class KalturaMonitorResult
{
	public $value;
	public $executionTime;
	public $description;
	public $errors = array();
	
	/**
	 * @return SimpleXMLElement
	 */
	public function toXml()
	{
		$xml = new SimpleXMLElement('<data/>');
		$xml->addChild('value', $this->value);
		$xml->addChild('executionTime', $this->executionTime);
		$xml->addChild('description', $this->description);
		foreach($this->errors as $error)
		{
			if($error instanceof KalturaMonitorError && $error->level != null)
			{
				$errorXml = $xml->addChild('error', $error->description);
				$errorXml->addAttribute('level', $error->level);
				if($error->code != null)
					$errorXml->addAttribute('code', $error->code);
			}
		}
		
		return $xml;
	}
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->toXml()->asXML();
	}
	
	/**
	 * @param string $xmlString
	 * @return KalturaMonitorResult
	 */
	public static function fromXml($xmlString)
	{
		$xml = new SimpleXMLElement($xmlString);
		
		$monitorResult = new KalturaMonitorResult();
		$monitorResult->value = floatval($xml->value);
		$monitorResult->executionTime = strval($xml->executionTime);
		$monitorResult->description = strval($xml->description);
		
		if($xml->error != null)
		{
			foreach($xml->error as $error)
			{
				$monitorErr = new KalturaMonitorError();
				$monitorErr->level = strval($error['level']);
				$monitorErr->code = strval($error['code']);
				$monitorErr->description = strval($error);
				$monitorResult->errors[] = $monitorErr;
			
			}
		}
		
		return $monitorResult;
	}
}
