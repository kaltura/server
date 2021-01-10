<?php

class myXmlUtils
{
	public static function validateXmlFileContent($filePath)
	{
		if(!$filePath)
		{
			return true;
		}

		$fileType = kFileUtils::getMimeType($filePath);
		if(strpos($fileType, 'html') !== false || strpos($fileType, 'xml') !== false)
		{
			$xmlContent = kFile::getFileContent($filePath);
			if($xmlContent)
			{
				$dom = new KDOMDocument();
				$dom->loadXML($xmlContent);
				$element = $dom->getElementsByTagName('script')->item(0);
				if($element)
				{
					return false;
				}
			}
		}

		return true;
	}
}