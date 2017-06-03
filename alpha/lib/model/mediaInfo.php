<?php

/**
 * Subclass for representing a row from the 'media_info' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class mediaInfo extends BasemediaInfo
{
	const MEDIA_INFO_BIT_RATE_MODE_CBR = 1;
	const MEDIA_INFO_BIT_RATE_MODE_VBR = 2;
	 
	public function getCacheInvalidationKeys()
	{
		return array("mediaInfo:flavorAssetId=".strtolower($this->getFlavorAssetId()));
	}
	
	public function setIsFastStart($v)	{$this->putInCustomData('IsFastStart', $v);}
	public function getIsFastStart()	{return $this->getFromCustomData('IsFastStart', null, 1);}
	
	public function setContentStreams($v)	{$this->putInCustomData('ContentStreams', $v);}
	public function getContentStreams()	{return $this->getFromCustomData('ContentStreams', null, null);}
	
	public function setComplexityValue($v)	{$this->putInCustomData('ComplexityValue', $v);}
	public function getComplexityValue()	{return $this->getFromCustomData('ComplexityValue', null, null);}
	
	public function setMaxGOP($v)	{$this->putInCustomData('MaxGOP', $v);}
	public function getMaxGOP()	{return $this->getFromCustomData('MaxGOP', null, null);}
	
	public function getRawDataXml()
	{
		$rawData = $this->getRawData();
		$rawDataLinesArray = explode(PHP_EOL, $rawData);
	
		$rawDataXml = new DOMDocument();
		$rootNode = $rawDataXml->createElement("RawData");
		$root = $rawDataXml->appendChild($rootNode);
		foreach ($rawDataLinesArray as $rawDataLine)
		{
			$rawDataLine = trim($rawDataLine);
			if(!$rawDataLine)
				continue;
								
			list($key, $value) = explode(":", $rawDataLine);
			$key = str_replace(" ", "",$key);
			$key = str_replace(array('?', '|', '*', '\\', '/' , '>' , '<', '&', '[', ']',' ','%', '(', ')'), "_", $key);
			
			if (!$value)
			{
				$parentNode = $rawDataXml->createElement($key);
				$root->appendChild($parentNode);
			}
			else
			{
				$value = trim($value);
				$node = $rawDataXml->createElement($key);
				$value = $rawDataXml->createTextNode (htmlspecialchars($value));
				$node->appendChild($value);
				$parentNode->appendChild($node);
			}
		}
		
		return $rawDataXml->saveXML();
	}
}
