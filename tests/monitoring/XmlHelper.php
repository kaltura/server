<?php

class XmlHelper
{
	public static function getXMLResult($value, $executionTime, $description, array $errors = array())
	{
		$xml = new SimpleXMLElement('<data xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="monitor.xsd" />');
		$xml->addChild('value', $value);
		$xml->addChild('executionTime', $executionTime);
		$xml->addChild('description', $description);
		foreach ($errors as $error) {
			if ($error->level != null) {
				$errorXml = $xml->addChild('error', $error->description);
				$errorXml->addAttribute('level', $error->level);
				if ($error->code != null)
					$errorXml->addAttribute('code', $error->code);
				}
		}
		return $xml;
	}

	public static function fromXmlResult($xmlResult)
	{
		$xml = new SimpleXmlElement($xmlResult);
		$res = new MonitorResult();
		$res->value = $xml->value;;
		$res->exeTime = $xml->executionTime;	
		$res->description = $xml->description;
		if ($xml->error != null) {
			foreach ($xml->error as $error)
			{
				$monitorErr = new MonitorError();
				$monitorErr->level = $error['level'];
				$monitorErr->code = $error['code'];
				$monitorErr->description = $error;
				$res->errors[] = $monitorErr;
				
			}
		}
		return $res;
	}
}

class MonitorError
{
	public $level;
	public $code;
	public $description;
}

Class MonitorResult
{
	public $value;
	public $execTime;
	public $description;
	public $errors;
}
