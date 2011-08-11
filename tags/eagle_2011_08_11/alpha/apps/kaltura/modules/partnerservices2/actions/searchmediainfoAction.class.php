<?php
/**
 * @package api
 * @subpackage ps2
 */
class searchmediainfoAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "searchMediaInfo",
				"desc" => "",
				"in" => array (
					"mandatory" => array (
						"media_type" => array ("type" => "enum,entry,ENTRY_MEDIA_TYPE", "desc" => ""),
						"media_source" => array ("type" => "enum,entry,ENTRY_MEDIA_SOURCE", "desc" => ""),
						"media_id" => array ("type" => "string", "desc" => ""),
						),
					"optional" => array (
						),
					),
				"out" => array (
					"media" => array ("type" => "array", "desc" => "")
					),
				"errors" => array (
					APIErrors::ADULT_CONTENT,
					APIErrors::CANNOT_IMPORT_ONE_OR_MORE_MEDIA_FILES,
					APIErrors::SEARCH_UNSUPPORTED_MEDIA_TYPE,
					APIErrors::SEARCH_UNSUPPORTED_MEDIA_SOURCE
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

		$this->getMediaInfoForPrefix ( "media" );
		
		$i=1;
		while ( $res = $this->getMediaInfoForPrefix ( "media$i" ) )
		{
			++$i;
		}
	}
	
	private function getMediaInfoForPrefix ( $prefix )
	{
		$media_id = $this->getP ( $prefix . "_id" );
		
		if ( ! $media_id )
		{
			return false;
		}
		
		$mediaType = $this->getPM ( $prefix . "_type" , entry::ENTRY_MEDIA_TYPE_VIDEO );
		$mediaSource = $this->getPM ( $prefix . "_source" );
		
//		echo "[$prefix]: $media_id, $mediaType, $mediaSource<br>";
		
		// TODO - get this if we need t for flickr
		$kuserId = null;
		
		$media_source_provider = myMediaSourceFactory::getMediaSource ( $mediaSource );
	//	echo ( "$media_source\n" );
		if ( $media_source_provider )
		{
			$res = $media_source_provider->getMediaInfo ( $mediaType , $media_id )  ;
			if ( $res !== null )
			{
				$this->addMsg( $prefix , $res );
				
				// lets check if there were errors so we could add an error message
				if(@$res["status"] == "error") 
				{
					if (strpos(@$res["message"], "Adult content") !== false)
					{
						$this->addError(APIErrors::ADULT_CONTENT);				
					}
					else
					{
						$this->addError(APIErrors::CANNOT_IMPORT_ONE_OR_MORE_MEDIA_FILES);
					}
				}
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

		return true;
	}
}
?>