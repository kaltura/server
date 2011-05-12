<?php
class myFlickrServices extends myBaseMediaSource implements IMediaSource
{
	protected $supported_media_types = self::SUPPORT_MEDIA_TYPE_IMAGE;  
	protected $source_name = "Flickr";
	protected $auth_method = array ( self::AUTH_METHOD_PUBLIC , self::AUTH_METHOD_EXTERNAL );
	protected $search_in_user = false; 
	protected $logo = "http://www.kaltura.com/images/wizard/logo_flickr.png";
	protected $id = entry::ENTRY_MEDIA_SOURCE_FLICKR;
	
	private static $NEED_MEDIA_INFO = "1";
	
	const API_KEY = 'bc52f6fe3c0c8ee578c0632e5c1e9635';
	const SECRET = '02aacc93d6086633';

	public function getMediaInfo( $media_type ,$objectId) 
	{
		if( $media_type == entry::ENTRY_MEDIA_TYPE_IMAGE )
		{
			return self::getImageInfo( $objectId );
		}
		else
		{
			// this provider does not supply media type $media_type
		}		
		return null;		
	}

	public function getAuthData($kuserId, $userName, $password, $token)
	{
		if (!$token)
			$token = "";
			
		if (!$kuserId)
			$kuserId = 0;
		
		//$kalt_token = $kuserId . ":" . $token;
		$kalt_token = base64_decode(@$_COOKIE['flickr_kalttoken']);
		if (!$kalt_token)
			$kalt_token = $kuserId . ":" . md5(microtime());
		
	 	$flickrToken = flickrTokenPeer::retrieveByPK($kalt_token);
		
		$status = 'ok';
		$message = '';
		$authData = ($flickrToken && $flickrToken->getIsValid()) ? $kalt_token : "";
		setcookie( 'flickr_kalttoken', base64_encode($kalt_token), time() + 86400 , '/' );
		
		$loginUrl = $authData ? "" : self::createLoginUrl();
		
		return array('status' => $status, 'message' => $message, 'authData' => $authData, 'loginUrl' => $loginUrl);
	}
	
	public function searchMedia( $media_type , $searchText, $page, $pageSize, $authData = null, $extraData = null)
	{
		if( $media_type == entry::ENTRY_MEDIA_TYPE_IMAGE )
		{
			return self::searchImages($searchText, $page, $pageSize, $authData ); 		
		}
		else
		{
			// this provider does not supply media type $media_type
		}
		return null;
	}

// --------------------------------------------------------------------------------	
	private static function buildUrl($params, $url)
	{
		$encoded_params = array();
		
		$api_sig = self::SECRET;
			
		$params['api_key'] = self::API_KEY;
		if ($url != "auth")
			$params['format'] = 'php_serial';

		ksort($params);
		foreach ($params as $k => $v){
			$encoded_params[] = urlencode($k).'='.urlencode($v);
			$api_sig .= $k.$v;
		}
		
		//print_r($encoded_params);
			
		#
		# call the API and decode the response
		#
			
		$api_sig = md5($api_sig);
			
		$fullUrl = "http://api.flickr.com/services/$url/?".implode('&', $encoded_params)."&api_sig=$api_sig";
		
		return $fullUrl;
	}
	
	private static function sendRequest($params, $url = "rest")
	{
		$fullUrl = self::buildUrl($params, $url);
		
		$rsp = kFile::downloadUrlToString($fullUrl);
			
		$rsp_obj = unserialize($rsp);
			
		return $rsp_obj;
	}
	
	public static function createLoginUrl()
	{
		$params = array(
			'perms'	=>	'read',
		);
		
		return myResponseUtils::createRedirectUrl(self::buildUrl($params, "auth"));
	}
	
	public static function setKuserToken($kalt_token, $frob)
	{
		$params = array(
			'method'	=> 'flickr.auth.getToken',
			'frob'	=>	$frob
		);
		
		$flickrToken = flickrTokenPeer::retrieveByPK($kalt_token);
		if (!$flickrToken)
		{
			$flickrToken = new flickrToken();
			$flickrToken->setKaltToken($kalt_token);
		}
		
		$flickrToken->setFrob($frob);
		
		$rsp_obj = self::sendRequest($params);
		
		if ($rsp_obj['stat'] == 'ok')
		{
			$flickrToken->setToken($rsp_obj['auth']['token']['_content']);
			$flickrToken->setNsid($rsp_obj['auth']['user']['nsid']);
			$flickrToken->setIsValid(true);
		}
		else
			$flickrToken->setIsValid(false);
		
		$flickrToken->setResponse(serialize($rsp_obj));
		
		$flickrToken->save();
		
		return $rsp_obj;
	}


		
	private static function searchImages($searchText, $page, $pageSize, $authData = null)
	{
		$params = array(
			'method'	=> 'flickr.photos.search',
			'page'		=>	$page,
			'per_page'	=>	$pageSize,
			'text'		=>	$searchText
		);
		
		if ($authData)
		{
			$flickrToken = flickrTokenPeer::retrieveByPK($authData);
			if ($flickrToken)
			{
				$params['user_id'] = 'me';
				$params['auth_token'] = $flickrToken->getToken();
			}
		}
		else
		{
			$params['license'] = '4';
		}

		$images = array();
		$message = '';
		$rsp_obj = self::sendRequest($params);

		if ($rsp_obj['stat'] == 'ok')
		{
			$photo = $rsp_obj['photos']['photo'];
			foreach($photo as $key => $value)
			{
				$thumb = 'http://farm'.$value['farm'].'.static.flickr.com/'.$value['server'].'/'.$value['id'].'_'.$value['secret'].'_t.jpg';
				$title = $value['title'];

				$images[] = array('thumb' => $thumb, 'title' => $title, 'id' => $value['id'] . ':'. $value['secret']);

				//echo $title."\n".$url."\n";
			}
			$status = 'ok';
		}
		else
		{
			//print_r($rsp_obj);
			$status = 'error';
			$message = $rsp_obj['code'].' : '.$rsp_obj['message'];
		}
		
		return array('status' => $status, 'message' => $message, 'objects' => $images , "needMediaInfo" => self::$NEED_MEDIA_INFO );
	}

	

	
	private  static function getImageInfo($objectId)
	{
		list($photoId, $secret) = explode(':', $objectId);
		
		$params = array(
			'method'	=> 'flickr.photos.getinfo',
			'photo_id'	=>	$photoId,
			'secret' => $secret
		);
		
		$message = '';
		$objectInfo = null;
		
		$rsp_obj = self::sendRequest($params);
		if ($rsp_obj['stat'] == 'ok')
		{
			$photo = $rsp_obj['photo'];
			$photo_url = 'http://farm'.$photo['farm'].'.static.flickr.com/'.$photo['server'].'/'.$photo['id'].'_'.$photo['secret'].'.jpg';
			
			//print_r($photo);
			$tags = '';
			foreach($photo['tags']['tag'] as $key => $tag)
				$tags .= $tag['raw'].',';
				
			$source_link = "";
			foreach(@$photo['urls'] as $urls)
			{
				foreach($urls as $url)
				{
					if (@$url['type'] == 'photopage')
					{
						$source_link = @$url['_content'];
						break;
					}
				}
			}
			
			$objectInfo = array('id' => $objectId, 'url' => $photo_url, 'title' => $photo['title']['_content'],
				'tags' => $tags,
				'source_link' => $source_link,
				'license' => $photo['license'], 'credit' => $photo['owner']['realname']);
				
			$status = 'ok';
		}
		else
		{
			$status = 'error';
			$message = $rsp_obj['code'].' : '.$rsp_obj['message'];
		}
		
		return array('status' => $status, 'message' => $message, 'objectInfo' => $objectInfo);
	}
}
?>