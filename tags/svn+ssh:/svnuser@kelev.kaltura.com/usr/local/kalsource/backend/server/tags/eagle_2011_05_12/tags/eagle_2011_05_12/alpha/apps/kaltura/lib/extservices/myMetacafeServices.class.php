<?php

class myMetacafeServices extends myBaseMediaSource implements IMediaSource
{
	protected $supported_media_types =  self::SUPPORT_MEDIA_TYPE_VIDEO ;  
	protected $source_name = "Metacafe";
	protected $auth_method = array ( self::AUTH_METHOD_PUBLIC );
	protected $search_in_user = false; 
	protected $logo = "http://www.kaltura.com/images/wizard/logo_metacafe.png";
	protected $id = entry::ENTRY_MEDIA_SOURCE_METACAFE;
	
	private static $domain = "http://www.metacafe.com";
		
	private static $NEED_MEDIA_INFO = "0";
		
	const USER_AGENT = "User-Agent: Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)";

	public function getMediaInfo( $media_type ,$objectId) 
	{
		return null;		
	}
		
	public function searchMedia( $media_type , $searchText, $page, $pageSize, $authData = null, $extraData = null)
	{
		if( $media_type == entry::ENTRY_MEDIA_TYPE_VIDEO )
		{
			$result = self::search($searchText, $page, $pageSize);
			return self::parse($result);
		}
		else
		{
			// this provider does not supply media type $media_type
		}		
		return null;		
		
	}

	public function getAuthData( $kuserId, $userName, $password, $token)
	{
		return;
	}
	
// --------------------------------------------------------------------------------
	
	private static function search($searchText, $page, $pageSize)
	{
		if (!$pageSize)
			$pageSize = 100;
			
		$pageSize = min($pageSize, 100);
		
		$startIndex = ($page - 1) * $pageSize;
		$url = self::$domain."/api/videos/?vq=".urlencode($searchText)."&max-results=".$pageSize."&start-index=".$startIndex."&time=all_time";

		$content = kFile::downloadUrlToString($url);
		
		return $content;
	}
	
	private static function parse($content)
	{
		$items = array();
		$message = '';
		
		$doc = new DOMDocument();
		if ($doc->loadXML($content))
		{
			$xpath = new DOMXPath($doc);
			$itemNodes = $xpath->query("//item");

			if (!$itemNodes->length)
			{
				$status = "error";
			}
			else
			{
				foreach($itemNodes as $itemNode)
				{
					$ns = "http://search.yahoo.com/mrss/";
					$id = self::getXmlNodeValue($itemNode, "id");
					$title = self::getXmlNodeValue($itemNode, "title");
					$thumbnail = "http://s.mcstatic.com/thumb/".$id.".jpg";
					$duration = self::getXmlNodeValue($itemNode, "content", "duration", $ns);
					$description = self::getXmlNodeValue($itemNode, "description", null, $ns);
					$description = strip_tags($description);
					$tags = self::getXmlNodeValue($itemNode, "keywords", null, $ns);
					$credit = self::getXmlNodeValue($itemNode, "credit", null, $ns);
					$link = self::getXmlNodeValue($itemNode, "link");
					
					$items[] = array(
							'id' => $id,
							'title' => $title,
							'thumb' => $thumbnail, 
							'description' => $description,
							'tags' => $tags,
							'license' => '', 
							'credit' => $credit,
							'source_link' => $link,
							'url' => "http://".kConf::get("www_host")."/index.php/extservices/metacafeRedirect/itemId/".$id
						);
				}
									
				$status = "ok";
			}
		}
		else
		{
			$status = "error";
		}

		return array('status' => $status, 'message' => $message, 'objects' => $items , "needMediaInfo" => self::$NEED_MEDIA_INFO);
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