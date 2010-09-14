<?php
abstract class myBaseMediaSource implements IMediaSource 
{
	const AUTH_METHOD_PUBLIC = 1;
	const AUTH_METHOD_USER = 2;
	const AUTH_METHOD_USER_PASS = 3;
	const AUTH_METHOD_EXTERNAL = 4;
	const AUTH_METHOD_USER_NO_PARAMS = 5;
	
	const PUBLIC_SEARCH_FALSE = 0;
	const PUBLIC_SEARCH_TRUE = 1;
	
	const SUPPORT_MEDIA_TYPE_VIDEO =1;
	const SUPPORT_MEDIA_TYPE_AUDIO =2;
	const SUPPORT_MEDIA_TYPE_IMAGE =4;

	const AUDIO_THUMB_URL = "http://www.kaltura.com/images/search/img_sound.png";
	
	protected static $partner_id;
	protected static $subp_id;
	protected static $puser_id;
	
	
	protected $source_type ="search";
	protected $supported_media_types = 1; // bitwise 
	protected $source_name = "";
	protected $auth_method = array ( self::AUTH_METHOD_PUBLIC );
	protected $allow_public_search = self::PUBLIC_SEARCH_TRUE;
	protected $search_in_user = true; 
	protected $logo = "http://www.kaltura.com";
	protected $module_url = "SearchView.swf";
	protected $id = -1;
	
	
	public function setUserDetails (  $partner_id , $subp_id , $puser_id )
	{
		self::$partner_id = $partner_id;
		self::$subp_id = $subp_id;
		self::$puser_id = $puser_id;	
	}
	
	public function getConfigCustomData() 
	{
		return null;
	}
	
	protected function getFileExt ( $type )
	{
		return null;	
	}
	
	public function getSearchConfig ( )
	{
/*
		<service>
			<id>2</id>
			<type>search</type>
			<authMethod>0</authMethod> 
			<name>iTunes</name>
			<media>
				<type>audio</type>
				<logo>http://logo.jpg</logo>\
			</media>
		</service>
*/
		$service = array ( "id" => $this->id ,
				"type" => $this->source_type ,
				"authMethodList" => $this->getAuthMethods ( ) , 
				"moduleUrl" => $this->module_url,
				"name" => $this->source_name ,
				"logo" => $this->logo);
				
		$customData = $this->getConfigCustomData();
		if ($customData)
			$service["customData"] = $customData;
		
		if ( $this->supported_media_types & self::SUPPORT_MEDIA_TYPE_VIDEO )		
		{	
			$this->prepareMediaTypeArray ( $service ,  self::SUPPORT_MEDIA_TYPE_VIDEO , "video" ) ;
		}
		if ( $this->supported_media_types & self::SUPPORT_MEDIA_TYPE_AUDIO )		
		{
			$this->prepareMediaTypeArray ( $service ,  self::SUPPORT_MEDIA_TYPE_AUDIO , "audio" ) ;			
		}
		if ( $this->supported_media_types & self::SUPPORT_MEDIA_TYPE_IMAGE )		
		{
			$this->prepareMediaTypeArray ( $service ,  self::SUPPORT_MEDIA_TYPE_IMAGE , "image" ) ;
		}

		//$prefix = "__" . get_class( $this) . "_" ;
		return $service; 
		
	}
	
	protected function getAuthMethods ()
	{
		$arr = array();
		foreach ( $this->auth_method as $method )
		{
			$arr[ "__{$method}_authMethod" ] = $method ;	
		}
		return $arr;
	}
	
	protected static function getKalturaMediaType ( $service_media_type )
	{
		if( $service_media_type == self::SUPPORT_MEDIA_TYPE_AUDIO )
			return entry::ENTRY_MEDIA_TYPE_AUDIO;
		if( $service_media_type == self::SUPPORT_MEDIA_TYPE_VIDEO )
			return entry::ENTRY_MEDIA_TYPE_VIDEO;
		if( $service_media_type == self::SUPPORT_MEDIA_TYPE_IMAGE )
			return entry::ENTRY_MEDIA_TYPE_IMAGE;
	}

	 
	private function prepareMediaTypeArray ( &$service_arr , $type , $type_str )
	{
			$service_arr ["__{$type_str}_media"] = array ( "type" => $type_str );
			$file_extensions = $this->getFileExt( $type );
			if ( $file_extensions != null )
				$service_arr ["__{$type_str}_media"] ["fileExt"] = $file_extensions ; 	
	}
	
	protected function hitUrl ( $url , $params=null , $user_agent =null)
	{
		try
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			if ( $params )
			{
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			}
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_NOBODY, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			if ( $user_agent )
			{
				curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
			}
			$content = curl_exec($ch);
			return $content;
		}
		catch ( Exception $ex )
		{
			KalturaLog::log ( __METHOD__ . ": error hitting url [$url] with params:" . print_r ( $params ) );
		}	
		curl_close($ch);	
	}
}
?>