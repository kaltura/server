<?php
interface IMediaSource
{
	/**
		return array('status' => $status, 'message' => $message, 'objectInfo' => $objectInfo);
	*/
	public function getMediaInfo( $media_type ,$objectId) ;
	
	
	/**
		return array('status' => $status, 'message' => $message, 'objects' => $objects);
			objects - array of
					'thumb' 
					'title'  
					'description' 
					'id' - unique id to be passed to getMediaInfo 
	*/
	public function searchMedia( $media_type , $searchText, $page, $pageSize, $authData = null, $extraData= null );
	
	
	/**
	*/
	public function getAuthData( $kuserId, $userName, $password, $token);
	
	
	public function getSearchConfig ( );
	
	public function getConfigCustomData();
}
?>