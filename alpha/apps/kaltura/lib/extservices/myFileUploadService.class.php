<?php
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
	
	static $video_file_ext = array("flv","asf","qt","mov","mpg","mpeg","avi","wmv","mp4","m4v","3gp","vob","f4v","mkv");
	static $image_file_ext = array("jpg","jpeg","bmp","png","gif","tif","tiff");
	static $audio_file_ext = array("flv","asf","wmv","qt","mov","mpg","avi","mp3","wav","mp4","wma","3gp","vob","amr");
		
	static public function getMediaTypeFromFileExt ( $ext )
	{
		// notice that video is checked first since it has precedence over audio (both may have the same ext.)
		if (in_array($ext, self::$video_file_ext))
			return entry::ENTRY_MEDIA_TYPE_VIDEO;
		elseif (in_array($ext, self::$image_file_ext))
			return entry::ENTRY_MEDIA_TYPE_IMAGE;
		elseif (in_array($ext, self::$audio_file_ext))
			return entry::ENTRY_MEDIA_TYPE_AUDIO;
		
		return entry::ENTRY_MEDIA_TYPE_AUTOMATIC;
	}
	
	protected function getFileExt ( $type )
	{
		if ( $type == self::SUPPORT_MEDIA_TYPE_VIDEO )
			return implode(",", self::$video_file_ext);
		if ( $type == self::SUPPORT_MEDIA_TYPE_IMAGE )
			return implode(",", self::$image_file_ext);
		if( $type == self::SUPPORT_MEDIA_TYPE_AUDIO )
			return implode(",", self::$audio_file_ext);
	}
}

?>