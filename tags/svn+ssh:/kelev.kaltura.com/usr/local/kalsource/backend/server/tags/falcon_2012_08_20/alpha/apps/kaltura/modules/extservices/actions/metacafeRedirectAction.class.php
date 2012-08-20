<?php
/**
 * @package    Core
 * @subpackage externalServices
 */
class metacafeRedirectAction extends kalturaAction
{
	public function execute()
	{
		$error = false;
		
		// metacafe video id
		$itemId = $this->getRequestParameter("itemId");
		
		$url = "http://www.metacafe.com/api/item/" . $itemId;
		$content = kFile::downloadUrlToString($url);
		
		$doc = new DOMDocument();
		if ($doc->loadXML($content))
		{
			$xpath = new DOMXPath($doc);
			$itemNodes = $xpath->query("//item");

			if (!$itemNodes->length)
			{
				$error = true;
			}
			else
			{
				$itemNode = $itemNodes->item(0);

				$ns = "http://search.yahoo.com/mrss/";
				$swfUrl = self::getXmlNodeValue($itemNode, "content", "url", $ns);
				if(!$swfUrl)
				{
					die('no content url in item ['.$swfUrl.']. cannot fetch headers for ['.$swfUrl.']');
				}
				// get only the headers and don't follow the location redirect
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $swfUrl);
				curl_setopt($ch, CURLOPT_USERAGENT, "curl/7.11.1");
				curl_setopt($ch, CURLOPT_HEADER, 1);
				curl_setopt($ch, CURLOPT_NOBODY, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
				$headers = curl_exec($ch);
				
				// get the location header
				$found1 = preg_match('/Location: (.*?)$/ms', $headers, $swfUrlResult);
				$swfUrl = urldecode($swfUrlResult[1]);
				$found2 = preg_match('/mediaURL=(.*?)&/ms', $swfUrl, $flvResult);
				if(!$found2)
				{
					// sometimes the format in metacafe response is not the same and they return some kind of JSON
					$found2 = preg_match('/mediaURL\"\:\"(.*?)\"/ms', $swfUrl, $flvResult);
					// this is another match for MediaURL in metacafe response
					$found2_alt = preg_match('/mediaURL":"(.*?)"/ms', $swfUrl, $flvResult);
					if($found2_alt && !$found2) $found2 = $found2_alt;
					if(!$found2)
					{
						die('could not process metacafe output: '.print_r($swfUrl, true));
					}
				}
				$flv = stripslashes(urldecode($flvResult[1]));
				$flv = urldecode($flv);
				$found3 = preg_match('/gdaKey=(.*?)&/ms', $swfUrl, $keyResult);
				// in another response of MetaCafe, the parameter is called key and not gdaKey
				if(!$found3)
				{
					$found3 = preg_match('/key":"(.*?)"/ms', $swfUrl, $keyResult);
				}
				$flv = $flv . "?__gda__=" . (isset($keyResult[1]) ? $keyResult[1] : '');
				
				$flv = str_replace(' ', '%20', $flv);
				// replacing square brackets so curl will not break the redirect
				$flv = str_replace('[', '%5B', $flv);
				$flv = str_replace(']', '%5D', $flv);
				$this->redirect($flv);
			}
		}
		else
		{
			$error = true;
		}

		if ($error)
			die("File not found");
	}
	
	private static function getXmlNodeValue($element, $nodeName, $attrName = null, $ns = null)
	{
		if ($ns)
			$titleNodes = $element->getElementsByTagNameNS($ns, $nodeName);
		else
			$titleNodes = $element->getElementsByTagName($nodeName);
		
		if ($titleNodes->length)
		{
			$node = $titleNodes->item(0);
			
			return $attrName ? $node->getAttribute($attrName) : $node->nodeValue;
		}
		return "";
	}
}
