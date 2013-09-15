<?php
require_once(__DIR__ . '/WebexXmlException.class.php');

class WebexXmlObject
{
	protected function getAttributeType($attributeName)
	{
		return null;
	}
	
	public function getAttributeValue($type, array $values)
	{
		$value = reset($values);
		switch($type)
		{
			case 'int':
			case 'integer':
			case 'long':
				return intval($value);
				
			case 'boolean':
				return (bool) $value;
				
			case 'string':
				return strval($value);
				
			case 'float':
				return floatval($value);
				
			case 'time':
				return strtotime(strval($value));
				
			default:
				if(class_exists($type))
					return new $type($value);
					
				$matches = null;
				if(preg_match('/^WebexXmlArray<([^>]+)>$/', $type, $matches))
				{
					$ret = array();
					foreach($values as $value)
						$ret[] = $this->getAttributeValue($matches[1], array($value));
						
					return $ret;
				}
		}
		
		throw new WebexXmlException("Type [$type] not found");
		return null;
	}
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		if(!$xml)
			return;
			
		foreach($xml->children() as $nodeName => $node)
		{
			if(!is_null($this->$nodeName))
				continue;
				
			$type = $this->getAttributeType($nodeName);
			if(is_null($type))
				throw new WebexXmlException("No type found for " . get_class($this) . "->{$nodeName}, XML: " . $xml->saveXML());
				
			$this->$nodeName = $this->getAttributeValue($type, $xml->xpath($nodeName));
		}
		
//		foreach($xml->attributes($prefix, true) as $attributeName => $attribute)
//			$this->attributes[$attributeName] = strval($attribute);
	}
}
