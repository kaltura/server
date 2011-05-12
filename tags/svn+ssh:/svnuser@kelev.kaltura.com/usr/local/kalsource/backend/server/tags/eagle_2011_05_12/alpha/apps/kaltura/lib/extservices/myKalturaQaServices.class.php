<?php
//define('MODULES' , SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR);
//require_once(MODULES.'search/actions/entryFilter.class.php');

class myKalturaQaServices  extends myBaseMediaSource  implements IMediaSource
{
	public function getMediaInfo( $media_type ,$objectId) 
	{
		
	}
	
	
	/**
		return array('status' => $status, 'message' => $message, 'objects' => $objects);
			objects - array of
					'thumb' 
					'title'  
					'description' 
					'id' - unique id to be passed to getMediaInfo 
	*/
	public function searchMedia( $media_type , $searchText, $page, $pageSize, $authData = null, $extraData= null )
	{
		$status = "ok";
		$message = '';
		$objects = array();
		@list ( $searchTerm , $requestedPageSize , $numResults , $repeatOldResults ) = explode ( "," ,$searchText);
		
		if ( $requestedPageSize > 0 ) $pageSize = $requestedPageSize;
		
		$already_returned = ($page-1) * $pageSize;
		$num_of_entries_to_return =  min ( $pageSize , $numResults - $already_returned );
		$i = 0;
		for ( $i=0 ; $i < $num_of_entries_to_return ; ++$i )
		{
			$host_num = 1+  $i % 7;
			$host = requestUtils::getHost();
			$url = "$host/qa/images.php?id=";
			
			$id =  ($page -1)* 	$pageSize + $i;
			if ( $repeatOldResults > 0 )
				$id = $id % $repeatOldResults ;
			
			$id = 1+ $id ; // do we want it to be 0-based or 1-based ??
		
			$playback = ( $i % 2 ? "none" : "" ) ;
			
			$object = array ( "id" => "id-{$id}" ,
				"thumb" => $url . $id , 
				"tags" => "tags-{$id}, $searchTerm" ,
				"title" => "title-{$id}" , 
				"description" => "description-{$id}" , 
				"flash_playback_type" => $playback );
			$objects[] = $object;
		}
		return array('status' => $status, 'message' => $message, 'objects' => $objects);		
	}
	
	
	/**
	*/
	public function getAuthData( $kuserId, $userName, $password, $token)
	{
		
	}
	
}
?>