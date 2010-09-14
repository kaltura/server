<?php
require_once ( "defPartnerservices2Action.class.php");

class searchfromurlAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "searchFromUrl",
				"desc" => "",
				"in" => array (
					"mandatory" => array ( 
						"url" => array ("type" => "string", "desc" => ""),
						"media_type" => array ("type" => "enum,entry,ENTRY_MEDIA_TYPE", "desc" => "")
						),
					"optional" => array (
						)
					),
				"out" => array (
					"search" => array ("type" => "array", "desc" => "")
					),
				"errors" => array (
					APIErrors::SEARCH_UNSUPPORTED_MEDIA_SOURCE_FOR_URL ,
				)
			); 
	}
	
	// TODO - remove so this service will validate the session
	protected function ticketType ()
	{
		return self::REQUIED_TICKET_NONE;
	}
	
	// check to see if already exists in the system = ask to fetch the puser & the kuser
	// don't ask for  KUSER_DATA_KUSER_DATA - because then we won't tell the difference between a missing kuser and a missing puser_kuser
	public function needKuserFromPuser ( )
	{
		return self::KUSER_DATA_NO_KUSER;
	}

	/**
		the puser might not be a kuser in the system
	 */
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		// set this boolean so the text will be escaped n the XML result
		self::$escape_text = true;
		
		$url = $this->getPM ( 'url' );
		$mediaType = $this->getPM ( 'media_type' );
/*		$searchText = $this->getP ( 'search' );
		$page = $this->getP ( 'page' , 1 );
		$pageSize = $this->getP ( 'page_size' , 10 );
		$authData = $this->getP ( 'auth_data' );
	*/
	
		list ( $media_source_provider ,$obj_id ) = myMediaSourceFactory::getMediaSourceAndObjectDataByUrl( $mediaType , $url );
		
	//	echo ( "$media_source\n" );
		if ( $media_source_provider )
		{
			//$res = call_user_func (  array ( $media_source , "searchMedia" ) , array ( $media_type , $searchText, $page, $pageSize, $kuserId ) ) ;
			$res = $media_source_provider->getMediaInfo ( $mediaType , $obj_id )  ;
			$this->addMsg( "search" , $res );
		}		
		else
		{
			//$this->addError( -301 , "unknown media source for url  $url" );
			$this->addError( APIErrors::SEARCH_UNSUPPORTED_MEDIA_SOURCE_FOR_URL , $url);
		}
	}
}
?>