<?php
/**
 * @package api
 * @subpackage ps2
 */
class searchAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "search",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"media_type" => array ("type" => "enum,entry,ENTRY_MEDIA_TYPE", "desc" => ""), 
						"media_source" => array ("type" => "enum,entry,ENTRY_MEDIA_SOURCE", "desc" => ""),
						"search" => array ("type" => "string", "desc" => ""),
						),
					"optional" => array (
						"auth_data" => array ("type" => "string", "desc" => ""),
						"page" => array ("type" => "integer", "default" => 1, "desc" => ""),
						"page_size" => array ("type" => "integer", "default" => 10, "desc" => "")
						)
					),
				"out" => array (
					"search" => array ("type" => "array", "desc" => "")
					),
				"errors" => array (
					APIErrors::SEARCH_UNSUPPORTED_MEDIA_TYPE ,
					APIErrors::SEARCH_UNSUPPORTED_MEDIA_SOURCE ,
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
		self::$escape_text = true;
		
		$mediaType = $this->getPM ( 'media_type' , entry::ENTRY_MEDIA_TYPE_VIDEO );
		$mediaSource = $this->getPM ( 'media_source' );
		$searchText = $this->getP ( 'search' );
		$page = $this->getP ( 'page' , 1 );
		$pageSize = $this->getP ( 'page_size' , 10 );
		$authData = $this->getP ( 'auth_data' );
		$extraData = $this->getP ( 'extra_data' );

		// for media_commons - dont' escape the XML.
		// The Urls will be damaged
		// TODO  - think of a good way to avoid this issue
		if ( $mediaSource == entry::ENTRY_MEDIA_SOURCE_MEDIA_COMMONS )
			self::$escape_text = false;
		
			
		// TODO - get this if we need t for flickr
		$kuserId = null;
		
		$media_source_provider = myMediaSourceFactory::getMediaSource ( $mediaSource );
	//	echo ( "$media_source\n" );
		if ( $media_source_provider )
		{
			$media_source_provider->setUserDetails ( $partner_id , $subp_id , $puser_id  );
			
			//$res = call_user_func (  array ( $media_source , "searchMedia" ) , array ( $media_type , $searchText, $page, $pageSize, $kuserId ) ) ;
			// magic word - '$partner_id' to replace the parameter dynamically
			$extraData = str_replace( '$partner_id' , $partner_id , $extraData );
			$res = $media_source_provider->searchMedia ( $mediaType , $searchText, $page, $pageSize, $authData , $extraData )  ;
			if ( $res )
			{
				$this->addMsg( "search" , $res );
			}
			else
			{
				$this->addError( APIErrors::SEARCH_UNSUPPORTED_MEDIA_TYPE, $mediaType);
			}
		}		
		else
		{
			$this->addError( APIErrors::SEARCH_UNSUPPORTED_MEDIA_SOURCE, $media_source);
		}
	}
}
?>