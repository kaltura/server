<?php
require_once ( "defPartnerservices2Action.class.php");

class searchauthdataAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "searchAuthData",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"media_source" => array ("type" => "integer", "desc" => ""),
						"username" => array ("type" => "string", "desc" => ""),
						"password" => array ("type" => "string", "desc" => "")
						),
					"optional" => array (
						)
					),
				"out" => array (
					"authdata" => array ("type" => "string", "desc" => "")
					),
				"errors" => array (
					APIErrors::SEARCH_UNSUPPORTED_MEDIA_SOURCE ,
				)
			); 
	}
	
	// TODO - remove so this service will validate the session
	protected function ticketType ()
	{
		return self::REQUIED_TICKET_NONE;
		//return self::REQUIED_TICKET_REGULAR;
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
		
		$mediaSource = $this->getP ( 'media_source' );
		$username = $this->getP ( 'username' );
		$password = $this->getP ( 'password' );
		
		// TODO - get this if we need t for flickr
		$kuserId = null;
		
		$media_source_provider = myMediaSourceFactory::getMediaSource ( $mediaSource );
	//	echo ( "$media_source\n" );
		if ( $media_source_provider )
		{
			//$res = call_user_func (  array ( $media_source , "searchMedia" ) , array ( $media_type , $searchText, $page, $pageSize, $kuserId ) ) ;
			$res = $media_source_provider->getAuthData($kuserId, $username, $password, "");//$this->ks->toSecureString());
			$this->addMsg( "authdata" , $res );
		}		
		else
		{
			$this->addError( APIErrors::SEARCH_UNSUPPORTED_MEDIA_SOURCE, $media_source);
		}
	}
}
?>