<?php
class myWebcamRecordService extends myBaseMediaSource implements IMediaSource
{
	private static $NEED_MEDIA_INFO = "0";
	
	protected $source_type ="webcam";
	protected $supported_media_types = 1 ; // SUPPORT_MEDIA_TYPE_VIDEO =1
	protected $source_name = "Webcam";
	protected $search_in_user = false; 
	protected $logo = ""; //"http://ccmixter.org/mixter-files/ccdj.gif";
	protected $module_url = "WebcamView.swf";
	protected $id = entry::ENTRY_MEDIA_SOURCE_WEBCAM;

	// empty implementation for search  methods
	public function getMediaInfo( $media_type ,$objectId) {} 

	public function searchMedia( $media_type , $searchText, $page, $pageSize, $authData = null, $extraData = null) {}
	
	public function getAuthData( $kuserId, $userName, $password, $token) {}
	
	public function getConfigCustomData()
	{
		return array("serverUrl" => requestUtils::getStreamingServerUrl());
	}
}

?>