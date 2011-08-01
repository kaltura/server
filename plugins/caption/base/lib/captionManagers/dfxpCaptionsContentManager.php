<?php
/**
 * @package plugins.caption
 * @subpackage lib
 */
class dfxpCaptionsContentManager extends kCaptionsContentManager
{
	/* (non-PHPdoc)
	 * @see kCaptionsContentManager::parse()
	 */
	public function parse($content)
	{
		$xml = new DOMDocument();
		try{
			$xml->loadXML($content);
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage());
			return array();
		}
		
		$elements = $xml->getElementsByTagName('p');
		if(!$elements->length)
			return array();
			
		$itemsData = array();
		foreach($elements as $element)
		{
			/* @var $element DOMElement */
			$startTime = kXml::timeToInteger($element->getAttribute('begin'));
			$endTime = $startTime;
			if($element->hasAttribute('end'))
			{
				$endTime = kXml::timeToInteger($element->getAttribute('end'));
			}
			elseif($element->hasAttribute('dur'))
			{
				$duration = floatval($element->getAttribute('dur')) * 1000;
				$endTime = $startTime + $duration;
			}
			$itemsData[] = array(
				'startTime' => $startTime,
				'endTime' => $endTime, 
				'content' => $element->textContent,
			);
		}
		
		return $itemsData;
	}
	
	/* (non-PHPdoc)
	 * @see kCaptionsContentManager::getContent()
	 */
	public function getContent($content)
	{
		$xml = new DOMDocument();
		try{
			$xml->loadXML($content);
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage());
			return null;
		}
		
		return preg_replace('/\s{2,}/', ' ', $xml->textContent);
	}
	
	/**
	 * @return dfxpCaptionsContentManager
	 */
	public static function get()
	{
		return new dfxpCaptionsContentManager();
	}
}