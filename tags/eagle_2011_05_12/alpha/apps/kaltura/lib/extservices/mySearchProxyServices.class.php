<?php
class mySearchProxyServices extends myBaseMediaSource implements IMediaSource
{
	protected $supported_media_types =  self::SUPPORT_MEDIA_TYPE_VIDEO ;  
	protected $source_name = "ProxySearch";
	protected $auth_method = array ( self::AUTH_METHOD_PUBLIC );
	protected $search_in_user = false; 
	protected $logo = "http://www.kaltura.com/images/wizard/logo_metacafe.png";
	protected $id = entry::ENTRY_MEDIA_SOURCE_SEARCH_PROXY;
	
	public function getMediaInfo( $media_type ,$objectId) 
	{
		return null;		
	}
		
	public function searchMedia( $media_type , $searchText, $page, $pageSize, $authData = null, $extraData = null)
	{
		$params = array();
		$params["uid"] = "";  
		$params["search"] = $searchText;  
		$params["media_type"] = $media_type;  
		$params["page"] = $page;  
		$params["page_size"] = $pageSize;  
		$params["filter"] = "";

		/*
		 * this is defined in cw ui conf under search provider (for provider id 28)
		 * <tokens>
		 *   <token>
		 *     <name>extra_data</name>
		 *     <value>http://www.partnerdomain.com/kaltura_search_callback.php</value>
		 *   </token>
		 * </tokens> 
		 */
		$searchUrl = $extraData;

		// curl request
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $searchUrl);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, "Kaltura search proxy");

		$result = curl_exec($ch);

		$items = array();
		$message = '';
		
		$doc = new DOMDocument();
		if (strlen($result) > 0 && $doc->loadXML($result))
		{
			$xpath = new DOMXPath($doc);
			$i = 0;
			$itemNodes = $xpath->query("//num_".$i);

			$noResults = true;
			while ($itemNodes && $itemNodes->length > 0)
			{
				$noResults = false;
				$itemNode = $itemNodes->item(0);
				$items[] = array(
					"id" => self::getXmlNodeValue($itemNode, "id"),
					"url" => self::getXmlNodeValue($itemNode, "url"),
					"tags" => self::getXmlNodeValue($itemNode, "tags"),
					"title" => self::getXmlNodeValue($itemNode, "title"),
					"thumb" => self::getXmlNodeValue($itemNode, "thumb"),
					"description" => self::getXmlNodeValue($itemNode, "description"),
					"source_link" => self::getXmlNodeValue($itemNode, "source_link"),
					"credit" => self::getXmlNodeValue($itemNode, "credit"),
					"media_source" => self::getXmlNodeValue($itemNode, "media_source"),
					"flash_playback_type" => self::getXmlNodeValue($itemNode, "flash_playback_type"),
					"license" => self::getXmlNodeValue($itemNode, "license")
				);
				
				$i++;
				$itemNodes = $xpath->query("//num_".$i);
			}
			
			if ($noResults)
				$status = "error";
			else
				$status = "ok";
		}
		else
		{
			$status = "error";
		}

		return array('status' => $status, 'message' => $message, 'objects' => $items , "needMediaInfo" => "0");
	}

	public function getAuthData( $kuserId, $userName, $password, $token)
	{
		return null;
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
;?>