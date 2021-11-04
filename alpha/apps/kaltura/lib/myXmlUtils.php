<?php

class myXmlUtils
{
	public static function validateXmlFileContent($filePath, $purifyParams = array())
	{
		if (!$filePath)
		{
			return true;
		}
		
		$fileType = kFileUtils::getMimeType($filePath);
		if (strpos($fileType, 'html') !== false || strpos($fileType, 'xml') !== false)
		{
			$xmlContent = kFile::getFileContent($filePath);
			
			$dom = new KDOMDocument();
			$dom->loadXML($xmlContent);
			$element = $dom->getElementsByTagName('script')->item(0);
			if ($element)
			{
				return false;
			}
			
			if ($purifyParams)
			{
				self::purifyXmlContent($filePath, $xmlContent, $purifyParams);
			}
		}
		
		return true;
	}
	
	public static function purifyXmlContent($filePath, $xmlContent, $purifyParams)
	{
		if (!is_null($purifyParams[0]) && !is_null($purifyParams[1]))
		{
			$modifiedContent = self::purifyField($purifyParams[0], $purifyParams[1] , $xmlContent);
			
			if ($modifiedContent != $xmlContent)
			{
				kFile::setFileContent($filePath, $modifiedContent);
			}
		}
	}
	
	public static function purifyField($className, $fieldName, $fieldValue)
	{
		if (!isset(kCurrentContext::$HTMLPurifierBehaviour) || kCurrentContext::$HTMLPurifierBehaviour == HTMLPurifierBehaviourType::IGNORE)
		{
			return $fieldValue;
		}
		
		try
		{
			return kHtmlPurifier::purify($className, $fieldName, $fieldValue);
		}
		catch (Exception $e)
		{
			throw new KalturaAPIException(KalturaErrors::UNSAFE_HTML_TAGS, $className, $fieldName);
		}
	}
}