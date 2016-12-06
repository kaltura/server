<?php
/**
 * @package Core
 * @subpackage ExternalServices
 */
class myFileUploadService extends myBaseMediaSource implements IMediaSource
{
	const MAX_FILES = 100;
	
	private static $NEED_MEDIA_INFO = "0";
		
	protected $source_type ="upload";
	protected $supported_media_types = 7 ; // bitwise for all 3 
	protected $source_name = "File Upload";
//	protected $auth_method = self::AUTH_METHOD_NONE;
	protected $search_in_user = false; 
	protected $logo = ""; //"http://ccmixter.org/mixter-files/ccdj.gif";
	protected $module_url = "UploadView.swf";
	protected $id = entry::ENTRY_MEDIA_SOURCE_FILE;

	// empty implementation for search  methods
	public function getMediaInfo( $media_type ,$objectId) {} 

	public function searchMedia( $media_type , $searchText, $page, $pageSize, $authData = null, $extraData = null) {}
	
	public function getAuthData( $kuserId, $userName, $password, $token) {}
	
	public function getConfigCustomData() 
	{
		return array("maxFiles" => self::MAX_FILES );
	}

	public static function getExtensionByContentType($url)
	{
		$curlWrapper = new KCurlWrapper();
		$curlHeaderResponse = $curlWrapper->getHeader($url, false);
		$curlWrapper->close();

		$headerContentType = $curlHeaderResponse && isset($curlHeaderResponse->headers["content-type"]) ? strtolower($curlHeaderResponse->headers["content-type"]) : null;
		$contentTypes = kConf::get("video_curl_content_type", 'base', array());

		if($headerContentType && isset($contentTypes[$headerContentType]))
		{
			$ext = $contentTypes[$headerContentType];
			KalturaLog::debug("extension - $ext");
			return $ext;
		}

		return null;
	}

	static public function getMediaTypeFromFileExt ( $ext )
	{
		// notice that video is checked first since it has precedence over audio (both may have the same ext.)
		$ext = strtolower($ext);
	    if (in_array($ext, kConf::get("video_file_ext")))
	    {
			return entry::ENTRY_MEDIA_TYPE_VIDEO;
	    }
		elseif (in_array($ext, kConf::get("image_file_ext")))
		{
			return entry::ENTRY_MEDIA_TYPE_IMAGE;
		}
		elseif (in_array($ext, kConf::get("audio_file_ext")))
		{
			return entry::ENTRY_MEDIA_TYPE_AUDIO;
		}
		
		return entry::ENTRY_MEDIA_TYPE_AUTOMATIC;
	}
	
	protected function getFileExt ( $type )
	{
		if ( $type == self::SUPPORT_MEDIA_TYPE_VIDEO )
		{
			return kConf::get("video_file_ext");
		}
		if ( $type == self::SUPPORT_MEDIA_TYPE_IMAGE )
		{
			return kConf::get("image_file_ext");
		}
		if( $type == self::SUPPORT_MEDIA_TYPE_AUDIO )
		{
			return kConf::get("audio_file_ext");
		}
	}
}
