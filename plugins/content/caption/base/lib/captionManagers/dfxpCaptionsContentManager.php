<?php
/**
 * @package plugins.caption
 * @subpackage lib
 */
class dfxpCaptionsContentManager extends kCaptionsContentManager
{
	/**
	 * @var string
	 */
	protected $ttsNS;
	
	/**
	 * @var array
	 */
	protected $styles;

	/**
	 * @var array
	 */
	protected $regions;

	function parseElementsByName(KDOMDocument $xml, $elementName)
	{
		$xmlNS = $xml->lookupNamespaceURI('xml');
		$elements = $xml->getElementsByTagName($elementName);
		$parsedElements = array();
		foreach($elements as $element)
		{
			if ($element->hasAttribute('id'))
				$id = $element->getAttribute('id');
			else if ($element->hasAttributeNS($xmlNS, 'id'))
				$id = $element->getAttributeNS($xmlNS, 'id');
			else
				continue;

			$parsedElements[$id] = $this->parseStyleAttributes($element);
		}
		return $parsedElements;
	}
	/**
	 * @param DOMElement $element
	 * @param array $style
	 * @return array
	 */
	function parseStyleAttributes(DOMElement $element, $style = array())
	{
		if ($element->hasAttributeNS($this->ttsNS, 'fontWeight') && $element->getAttributeNS($this->ttsNS, 'fontWeight') === 'bold')
			$style['bold'] = true;
		if ($element->hasAttributeNS($this->ttsNS, 'fontStyle') && $element->getAttributeNS($this->ttsNS, 'fontStyle') === 'italic')
			$style['italic'] = true;
		
		$copiedAtts = array('textAlign', 'displayAlign', 'color', 'backgroundColor', 'fontFamily', 'fontSize', 'origin', 'extent');
		foreach ($copiedAtts as $copiedAtt)
		{
			if ($element->hasAttributeNS($this->ttsNS, $copiedAtt))
			{
				$value = $element->getAttributeNS($this->ttsNS, $copiedAtt);
				if( (($copiedAtt === 'origin') || ($copiedAtt === 'extent')) && (!preg_match("/([0-9]?[0-9]|100)% ([0-9]?[0-9]|100)%/", $value)) )
				{
					continue;
				}
				$style[$copiedAtt] = $value;
			}
		}
		return $style;
	}

	function getInnerElement(DOMElement $childNode, $elementName, $elements, $origElement)
	{
		$innerElement = $origElement;
		$innerId = $childNode->getAttribute($elementName);
		if ($innerId && isset($elements[$innerId]))
		{
			$innerElement = $elements[$innerId];
		}

		return $innerElement;
	}

	function getAndUpdateStyleAttributes($childNode, $style, $region)
	{
		$styleAttributes = $this->parseStyleAttributes($childNode);
		foreach ($styleAttributes as $key => $value)
		{
			if(isset($key, $style))
			{
				$style[$key] = $value;
			}

			if(isset($key, $region))
			{
				$region[$key] = $value;
			}
		}
		return array($style, $region);
	}
	/**
	 * @param DOMNode $curNode
	 * @param array $style
	 * @param array $region
	 * @return array
	 */
	function getTextContent(DOMNode $curNode, $style, $region)
	{
		$result = array();
		for ($i = 0; $i < $curNode->childNodes->length; $i++)
		{
			$childNode = $curNode->childNodes->item($i);
			/* @var $childNode DOMElement */

			switch ($childNode->nodeType)
			{
			case XML_TEXT_NODE:
				$result[] = array('text' => $childNode->textContent, 'style' => array_merge($region, $style));
				break;
			
			case XML_ELEMENT_NODE:
				switch (strtolower($childNode->nodeName))
				{
				case 'span':
					$innerStyle = $this->getInnerElement($childNode, 'style', $this->styles, $style);
					$innerRegion = $this->getInnerElement($childNode, 'region', $this->regions, $region);

					list($innerStyle, $innerRegion) = $this->getAndUpdateStyleAttributes($childNode, $innerStyle, $innerRegion);
					$result = array_merge($result, $this->getTextContent($childNode, $innerStyle, $innerRegion));
					break;
					
				case 'br':
					$result[] = array('text' => "\n");
					break;
				}
				break;
			}
		}
		return $result;
	}

	/**
	 * @param DOMNode $curNode
	 * @param string $styleId
	 * @param string $regionId
	 * @return array  
	 */
	function parseBody(DOMNode $curNode, $styleId = null, $regionId = null)
	{	
		$itemsData = array();
		for ($i = 0; $i < $curNode->childNodes->length; $i++)
		{
			$childNode = $curNode->childNodes->item($i);
			if ($childNode->nodeType != XML_ELEMENT_NODE)
				continue;

			$curStyleId = $childNode->hasAttribute('style') ? $childNode->getAttribute('style') : $styleId;
			$regionId = $childNode->hasAttribute('region') ? $childNode->getAttribute('region') : $regionId;

			if (strtolower($childNode->nodeName) != 'p')
			{
				$itemsData = array_merge($itemsData, $this->parseBody($childNode, $curStyleId, $regionId));
				continue;
			}
			
			$startTime = $this->parseDfxpStrTTTime($childNode->getAttribute('begin'));
			$endTime = $startTime;
			if($childNode->hasAttribute('end'))
			{
				$endTime = $this->parseDfxpStrTTTime($childNode->getAttribute('end'));
			}
			elseif($childNode->hasAttribute('dur'))
			{
				$duration = floatval($childNode->getAttribute('dur')) * 1000;
				$endTime = $startTime + $duration;
			}
			
			$style = array();
			if (!is_null($curStyleId) && isset($this->styles[$curStyleId]))
				$style = $this->styles[$curStyleId];

			$region = array();
			if ($regionId && isset($this->regions[$regionId]))
				$region = $this->regions[$regionId];

			list($style, $region) = $this->getAndUpdateStyleAttributes($childNode, $style, $region);
			$itemsData[] = array('startTime' => $startTime, 'endTime' => $endTime, 'content' => $this->getTextContent($childNode, $style, $region));
		}
		
		return $itemsData;
	}
	
	/* (non-PHPdoc)
	 * @see kCaptionsContentManager::parse()
	 */
	public function parse($content)
	{
		$xml = new KDOMDocument();
		try
		{
			@$xml->loadXML(trim($content, " \r\n\t"));
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage());
			return array();
		}

		// parse styles
		$this->ttsNS = $xml->lookupNamespaceURI('tts');
		$this->styles = $this->parseElementsByName($xml, 'style');
		$this->regions = $this->parseElementsByName($xml, 'region');
		
		// parse content
		$itemsData = $this->parseBody($xml);
		if(! $itemsData)
		{
			KalturaLog::err("XML element <p> not found");
			return array();
		}
		
		return $itemsData;
	}
	
	private function parseDfxpStrTTTime($timeStr)
	{
		$matches = null;
		if(preg_match('/(\d+)s/', $timeStr))
			return intval($matches[1]) * 1000;
			
		return kXml::timeToInteger($timeStr);
	}
	
	/* (non-PHPdoc)
	 * @see kCaptionsContentManager::getContent()
	 */
	public function getContent($content)
	{
		$xml = new KDOMDocument();
		try
		{
			$xml->loadXML($content);
			$xml->formatOutput = true;
			$content = $xml->saveXML();
			$xml->loadXML($content);
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage());
			return null;
		}
		
		return trim(preg_replace('/\s+/', ' ', $xml->textContent));
	}
	
	/**
	 * @return dfxpCaptionsContentManager
	 */
	public static function get()
	{
		return new dfxpCaptionsContentManager();
	}

	protected function createAdjustedTimeLine($matches, $clipStartTime, $clipEndTime, $globalOffset)
	{
	}

	public function buildFile($content, $clipStartTime, $clipEndTime, $globalOffset = 0)
	{
		$xml = new KDOMDocument();
		try
		{
			$content = trim($content, " \r\n\t");
			$xml->loadXML($content);
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage());
			return '';
		}
		$xmlUpdatedContent = $this->editBody($xml, $clipStartTime, $clipEndTime, $globalOffset);
		$xmlUpdatedContent = trim($xmlUpdatedContent, " \r\n\t");
		$xmlUpdatedContent = str_replace("      \n", "", $xmlUpdatedContent);
		return $xmlUpdatedContent;
	}

	private function editBody(DOMNode $curNode, $clipStartTime, $clipEndTime, $globalOffset)
	{
		for ($i = 0; $i < $curNode->childNodes->length; $i++)
		{
			$childNode = $curNode->childNodes->item($i);
			if ($childNode->nodeType != XML_ELEMENT_NODE)
				continue;

			if (strtolower($childNode->nodeName) != 'p')
			{
				$this->editBody($childNode, $clipStartTime, $clipEndTime, $globalOffset);
				continue;
			}

			$captionStartTime = $this->parseDfxpStrTTTime($childNode->getAttribute('begin'));
			$captionEndTime = $captionStartTime;
			if($childNode->hasAttribute('end'))
			{
				$captionEndTime = $this->parseDfxpStrTTTime($childNode->getAttribute('end'));
			}
			elseif($childNode->hasAttribute('dur'))
			{
				$duration = floatval($childNode->getAttribute('dur')) * 1000;
				$captionEndTime = $captionStartTime + $duration;
			}
			if(!TimeOffsetUtils::onTimeRange($captionStartTime, $captionEndTime, $clipStartTime, $clipEndTime))
			{
				$curNode->removeChild($childNode);
				$i--;
			}
			else
				{
				$adjustedStartTime = TimeOffsetUtils::getAdjustedStartTime($captionStartTime, $clipStartTime, $globalOffset);
				$adjustedEndTime = TimeOffsetUtils::getAdjustedEndTime($captionEndTime, $clipStartTime, $clipEndTime, $globalOffset);

				$childNode->setAttribute('begin',kXml::integerToTime($adjustedStartTime));
				if($childNode->hasAttribute('end'))
					$childNode->setAttribute('end',kXml::integerToTime($adjustedEndTime));
			}
		}
		$content = "";
		if(!$curNode instanceof DOMElement)
		{
			$content = $curNode->saveXML();
		}
		return $content;
	}

	/**
	 * @param string $content
	 * @param string $toAppend
	 * @return string
	 */
	public function merge($content, $toAppend)
	{
		$contentXML = new KDOMDocument();
		$toAppendXML = new KDOMDocument();
		try
		{
			$contentXML->loadXML($content);
			$toAppendXML->loadXML($toAppend);
			$contentBody = $contentXML->getElementsByTagName('body')->item(0);
			$divToAppend = $toAppendXML->getElementsByTagName('div');
			/** @var DOMElement $candidate*/
			for ($i = 0; $i < $divToAppend ->length; $i ++)
			{
				$this->appendToDestination($divToAppend->item($i), $contentBody, $contentXML);
			}

		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage());
			return $content;
		}
		$content = $contentXML->saveXML();
		$content = trim($content, " \r\n\t");
		$content = str_replace("    \n", "", $content);
		return $content;
	}

	/**
	 * @param DOMElement $candidate
	 * @param DOMElement $contentBody
	 * @param DOMDocument $contentXML
	 */
	private function appendToDestination($candidate, $contentBody, $contentXML)
	{
		$shouldAddDiv = true;
		/** @var DOMElement $existingLanguage */
		foreach ($contentBody->getElementsByTagName('div') as $existingLanguage)
		{
			if ($existingLanguage->getAttribute('xml:lang') === $candidate->getAttribute('xml:lang'))
			{
				$shouldAddDiv = false;
				foreach ($candidate->childNodes as $line)
				{
					// Import the line, and all its children, to the $contentXML doc
					$line = $contentXML->importNode($line, true);
					// append imported line to existing language
					$existingLanguage->appendChild($line);
				}
				break;
			}
		}
		if ($shouldAddDiv)
		{
			// Import the language, and all its children, to the $contentXML doc
			$candidate = $contentXML->importNode($candidate, true);
			// append imported language to body
			$contentBody->appendChild($candidate);
		}
	}
}