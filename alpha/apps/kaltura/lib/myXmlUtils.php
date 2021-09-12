<?php

class myXmlUtils
{
	public static function validateXmlFileContent($filePath, $partnerId)
	{
		if (!$filePath || !$partnerId)
		{
			return true;
		}

		$fileType = kFileUtils::getMimeType($filePath);
		if (strpos($fileType, 'html') !== false || strpos($fileType, 'xml') !== false)
		{
			$partner = PartnerPeer::retrieveByPK($partnerId);
			$xmlContent = kFile::getFileContent($filePath);
			
			$dom = new KDOMDocument();
			$dom->loadXML($xmlContent);
			$element = $dom->getElementsByTagName('script')->item(0);
			if($element)
			{
				return false;
			}
			
			if ($partner && $partner->getPurifyImageContent())
			{
				$modifiedContent = self::purifyField('thumbasset', 'content' , $xmlContent);
				
				if ($modifiedContent != $xmlContent)
				{
					kFile::setFileContent($filePath, $modifiedContent);
				}
			}
		}

		return true;
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