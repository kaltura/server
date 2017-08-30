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
	 * @param DOMElement $element
	 * @param array $style
	 * @return array
	 */
	function parseStyles(DOMElement $element, $style = array())
	{
		if ($element->hasAttributeNS($this->ttsNS, 'fontWeight') && $element->getAttributeNS($this->ttsNS, 'fontWeight') == 'bold')
			$style['bold'] = true;
		if ($element->hasAttributeNS($this->ttsNS, 'fontStyle') && $element->getAttributeNS($this->ttsNS, 'fontStyle') == 'italic')
			$style['italic'] = true;
		
		$copiedAtts = array('textAlign', 'displayAlign', 'color', 'backgroundColor', 'fontFamily', 'fontSize');
		foreach ($copiedAtts as $copiedAtt)
			if ($element->hasAttributeNS($this->ttsNS, $copiedAtt))
				$style[$copiedAtt] = $element->getAttributeNS($this->ttsNS, $copiedAtt);
		return $style;
	}
	
	/**
	 * @param DOMNode $curNode
	 * @param array $style
	 * @return array  
	 */
	function getTextContent(DOMNode $curNode, $style)
	{
		$result = array();
		for ($i = 0; $i < $curNode->childNodes->length; $i++)
		{
			$childNode = $curNode->childNodes->item($i);
			switch ($childNode->nodeType)
			{
			case XML_TEXT_NODE:
				$result[] = array('text' => $childNode->textContent, 'style' => $style);
				break;
			
			case XML_ELEMENT_NODE:
				switch (strtolower($childNode->nodeName))
				{
				case 'span':
					$innerStyle = $this->parseStyles($childNode, $style);
					$result = array_merge($result, $this->getTextContent($childNode, $innerStyle));
					break;
					
				case 'br':
					$result[] = array('text' => "\n", 'style' => $style);
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
	 * @return array  
	 */
	function parseBody(DOMNode $curNode, $styleId = null)
	{	
		$itemsData = array();
		for ($i = 0; $i < $curNode->childNodes->length; $i++)
		{
			$childNode = $curNode->childNodes->item($i);
			if ($childNode->nodeType != XML_ELEMENT_NODE)
				continue;
			
			$curStyleId = $childNode->hasAttribute('style') ? $childNode->getAttribute('style') : $styleId;
						
			if (strtolower($childNode->nodeName) != 'p')
			{
				$itemsData = array_merge($itemsData, $this->parseBody($childNode, $curStyleId));
				continue;
			}
			
			$startTime = $this->parseStrTTTime($childNode->getAttribute('begin'));
			$endTime = $startTime;
			if($childNode->hasAttribute('end'))
			{
				$endTime = $this->parseStrTTTime($childNode->getAttribute('end'));
			}
			elseif($childNode->hasAttribute('dur'))
			{
				$duration = floatval($childNode->getAttribute('dur')) * 1000;
				$endTime = $startTime + $duration;
			}
			
			$style = array();
			if (!is_null($curStyleId) && isset($this->styles[$curStyleId]))
				$style = $this->styles[$curStyleId];
			
			$itemsData[] = array('startTime' => $startTime, 'endTime' => $endTime, 'content' => $this->getTextContent($childNode, $style));
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
			$xml->loadXML(trim($content, " \r\n\t"));
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage());
			return array();
		}

		// parse styles
		$xmlNS = $xml->lookupNamespaceURI('xml');
		$this->ttsNS = $xml->lookupNamespaceURI('tts');
		
		$elements = $xml->getElementsByTagName('style');
		$this->styles = array();
		foreach($elements as $element)
		{
			if ($element->hasAttribute('id'))
				$id = $element->getAttribute('id');
			else if ($element->hasAttributeNS($xmlNS, 'id'))
				$id = $element->getAttributeNS($xmlNS, 'id');
			else 
				continue;

			$style = $this->parseStyles($element);

			$this->styles[$id] = $style;
		}

		
		// parse content
		$itemsData = $this->parseBody($xml);
		if(! $itemsData)
		{
			KalturaLog::err("XML element <p> not found");
			return array();
		}
		
		return $itemsData;
	}
	
	private function parseStrTTTime($timeStr)
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


    public function buildDfxpFile($captionContent, $clipStartTime, $clipEndTime)
    {
        $xml = new KDOMDocument();
        try
        {
            $captionContent = trim($captionContent, " \r\n\t");
            $xml->loadXML($captionContent);
        }
        catch(Exception $e)
        {
            KalturaLog::err($e->getMessage());
            return null;
        }
        $xmlUpdatedContent = $this->editBody($xml, $clipStartTime, $clipEndTime);
        $xmlUpdatedContent = trim($xmlUpdatedContent, " \r\n\t");
        $xmlUpdatedContent = str_replace("      \n", "", $xmlUpdatedContent);
        return $xmlUpdatedContent;
    }

    private function editBody(DOMNode $curNode, $clipStartTime, $clipEndTime)
    {
        for ($i = 0; $i < $curNode->childNodes->length; $i++)
        {
            $childNode = $curNode->childNodes->item($i);
            if ($childNode->nodeType != XML_ELEMENT_NODE)
                continue;

            if (strtolower($childNode->nodeName) != 'p')
            {
                $this->editBody($childNode, $clipStartTime, $clipEndTime);
                continue;
            }

            $captionStartTime = $this->parseStrTTTime($childNode->getAttribute('begin'));
            $captionEndTime = $captionStartTime;
            if($childNode->hasAttribute('end'))
            {
                $captionEndTime = $this->parseStrTTTime($childNode->getAttribute('end'));
            }
            elseif($childNode->hasAttribute('dur'))
            {
                $duration = floatval($childNode->getAttribute('dur')) * 1000;
                $captionEndTime = $captionStartTime + $duration;
            }
            if(!$this->onTimeRange($captionStartTime, $captionEndTime, $clipStartTime, $clipEndTime))
                $curNode->removeChild($childNode);
            else{
                $adjustedStartTime = $captionStartTime - $clipStartTime;
                if ($adjustedStartTime < 0)
                    $adjustedStartTime = 0;
                $adjustedEndTime = $captionEndTime - $clipStartTime;

                $childNode->setAttribute('begin',kXml::integerToTime($adjustedStartTime));
                if($childNode->hasAttribute('end'))
                    $childNode->setAttribute('end',kXml::integerToTime($adjustedEndTime));
            }
        }
        $content = "";
        if(!$curNode instanceof DOMElement) {
            $content = $curNode->saveXML();
        }
        return $content;
    }

    private function onTimeRange($captionStartTime, $captionEndTime, $clipStartTime, $clipEndTime){
        if(($captionEndTime >= $clipStartTime) && ($captionEndTime <= $clipEndTime) && ($captionStartTime <= $clipStartTime))
            return true;

        if (($captionStartTime >= $clipStartTime) && ($captionStartTime <= $clipEndTime))
            return true;

        return false;
    }

}