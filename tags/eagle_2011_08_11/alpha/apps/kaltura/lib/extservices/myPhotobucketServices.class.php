<?php
class myPhotobucketServices extends myBaseMediaSource implements IMediaSource
{
	protected $supported_media_types =  5;  
	protected $source_name = "PhotoBucket";
//	protected $auth_method = self::AUTH_METHOD_NONE;
	protected $search_in_user = false; 
	protected $logo = "http://www.kaltura.com/images/wizard/logo_phototbucket.png";
	protected $id = entry::ENTRY_MEDIA_SOURCE_PHOTOBUCKET;
		
	private static $NEED_MEDIA_INFO = "0";
	
	private static $API_KEY = "149826145";
	private static $API_SECRET = "99e5bf0d7a80d057becb6e4c9f8e109a";
	private static $BASE_URL = "http://api.photobucket.com/";
	
	public function searchMedia( $media_type , $searchText, $page, $pageSize, $authData = null, $extraData = null)
	{
		if( $media_type == entry::ENTRY_MEDIA_TYPE_VIDEO )
		{
			$result = self::search("video", $searchText, $page, $pageSize);
			return self::parse($result);
		}
		elseif( $media_type == entry::ENTRY_MEDIA_TYPE_IMAGE )
		{
			$result = self::search("image", $searchText, $page, $pageSize);
			return self::parse($result);
		}
		else
		{
			// this provider does not supply media type $media_type
		}		
		return null;		
	}	
	
	public function getMediaInfo( $media_type ,$objectId) 
	{
		if( $media_type == entry::ENTRY_MEDIA_TYPE_VIDEO || $media_type == entry::ENTRY_MEDIA_TYPE_IMAGE )
		{
			return self::getObjectInfo( $objectId );
		}
		else
		{
			// this provider does not supply media type $media_type
		}		
		return null;		
	}
	
	public function getAuthData( $kuserId, $userName, $password, $token)
	{
		return array('status' => 'ok', 'message' => '', 'authData' => $userName);
//		return ""; // empty value
	}	


	public static function login($userName, $password)
	{
		//curl -c cookies.txt -D headers.txt -o login.txt -d "action=login&redir=&username=USERNAME&password=PASSWORD&login=Login" http://photobucket.com/login.php
		$postData = array("action" => "login", "redir", "username" => $userName, "password" => $password, "login" => "Login");
		
		$o="";
		foreach ($postData as $k=>$v)
			$o.= "$k=".utf8_encode($v)."&";
		
		$postData = substr($o,0,-1);
		
		$loginUrl = "http://photobucket.com/login.php";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $loginUrl);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	
		$header = curl_exec($ch);
		curl_close($ch);
		
		if (preg_match('/Set-Cookie:.*?(PHPSESSID.*?);/', $header, $sessionCookieMatch))
		{
			$sessionCookie = $sessionCookieMatch[1];
			return $sessionCookie;
		}
		else
		{
			return "";
		}
	}
	
	private static function search($searchType, $searchText, $page, $pageSize)
	{
		$searchUrl = self::$BASE_URL . "search/" . self::urlencodeRFC3986($searchText) . "/" .$searchType;
		$params = array(
			"perpage"=> $pageSize,
			"offset" => ($page - 1) * $pageSize,
			"secondaryperpage" => 0
		);

		$params = self::signParams("GET", $searchUrl, $params);
		
		$searchUrl .= "?";
		foreach($params as $key => $val) {
			$searchUrl .= ($key . "=" . $val . "&"); 
		}
		$searchUrl = substr($searchUrl, 0, strlen($searchUrl) - 1);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $searchUrl);
		curl_setopt($ch, CURLOPT_NOBODY, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	
		$content = curl_exec($ch);
		curl_close($ch);
		$result = unserialize($content);

		return $result;
	}

	private static function parse($result)
	{
		$objects = array();
		$message = '';

		$media = @$result["content"]["result"]["primary"]["media"];
		if (count($media) > 0) 
		{
			foreach($media as $item) 
			{
				// photobucket support something that looks like a roughcuts, which we cannot import
				// so we only return videos which are flv 
				$url = "";
				if (preg_match('/file=([^&]*)/', $item["url"], $out)) // video flv
					$url = $out[1];
				else if (preg_match('/\.(jpg|jpeg|png|bmp)$/', $item["url"], $out)) // image (ends with image extension
					$url = $item["url"];
				else 
					continue;
					
				$objects[] = array(
					"id" => urldecode($item["url"]),
					"title" => $item["title"],
					"thumb" => $item["thumb"], 
					"description" => $item["description"],
					"credit" => $item["_attribs"]["username"],
					"source_link" => $item["browseurl"],
					"url" => $url
				);
			}
			$status = "ok";
		}
		else
		{
			$status = "error";
		}
		
		return array('status' => $status, 'message' => $message, 'objects' => $objects, "needMediaInfo" => self::$NEED_MEDIA_INFO);
	}


	private  static function getObjectInfo($objectId)
	{
		$status = 'ok';
		$message = '';
		$objectInfo = null;
		
		$objectInfo = array('id' => $objectId, 'url' => $objectId);
		
		return array('status' => $status, 'message' => $message, 'objectInfo' => $objectInfo);
	}
	
	private static function signParams($httpMethod, $url, $extraParams = null) {
		$baseParams = array(
			"format" => "phpserialize",
			"oauth_consumer_key" => self::$API_KEY,
			"oauth_nonce" => md5(rand()),
			"oauth_timestamp" => time(),
			"oauth_version" => "1.0",
			"oauth_signature_method" => "HMAC-SHA1",
		);
		
		// merge
		if (!@$extraParams)
			$extraParams = array();
		$paramsMerged = array_merge($baseParams, $extraParams);
		
		// sort
		ksort($paramsMerged);
		
		// url encode
		$params = array();
		foreach($paramsMerged as $key => $val) {
			$params[self::urlencodeRFC3986($key)] = self::urlencodeRFC3986UTF8($val);
		}
		
		// params to string
		$paramsStr = "";
		foreach($params as $key => $val) {
			$paramsStr .= ($key . "=" . $val . "&");
		}
		$paramsStr = substr($paramsStr, 0, strlen($paramsStr) - 1);
		
		// build the base string
		$baseString = self::urlencodeRFC3986($httpMethod) . "&" . self::urlencodeRFC3986($url) . "&" . self::urlencodeRFC3986UTF8($paramsStr);
		
		$sig = base64_encode(hash_hmac("sha1", $baseString, (self::$API_SECRET . "&"), true));
		$sig = self::urlencodeRFC3986($sig);
		$params["oauth_signature"] = $sig;
		
		return $params;
	}
	
	/*
	 * taken from OAuth-0.1.1 @ http://code.google.com/p/photobucket-api-php5/ 
     * @param $string
     * @return string
	 */
    public static function urlencodeRFC3986($string) {
        return str_replace('%7E', '~', rawurlencode($string));
    }
    
    /*
     * taken from OAuth-0.1.1 @ http://code.google.com/p/photobucket-api-php5/ 
     * @param $string
     * @return string
     */
    public static function urlencodeRFC3986UTF8($string) {
        return self::urlencodeRFC3986(utf8_encode($string));
    }
}
?>