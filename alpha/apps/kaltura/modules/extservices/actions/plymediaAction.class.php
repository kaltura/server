<?php

/**
 * extservices actions.
 *
 * @package    kaltura
 * @subpackage extservices
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class plymediaAction extends kalturaAction
{
	public function execute()
	{
		$hdr = "text/xml; charset=utf-8";
		$this->response->setHttpHeader ( "Content-Type" ,  $hdr );
	
		$movie = $this->getP ( "movie" );
		if ( $movie ) 
		{
			//@list ( $obj_type , $id ) = explode ( "_" , $movie );
			// Gonen 28/03/2010: changed code to support both Andromeda style entry ID (0_XXX....) and old style entry ID (chars only)
			$obj_type = @substr($movie, 0, strpos($movie, '_'));
			$id = @str_replace($obj_type.'_', '', $movie);
			
			if ( $obj_type == "entry" )
			{
				return $this->renderText( self::renderEntry ( $id ) );
			}
			else
			{
				$kshow = kshowPeer::retrieveByPK( $id );
				if ( $kshow )
				{
					return $this->renderText( self::renderEntry ( $kshow->getShowEntryId() ) );
				}
			}
		}

		return $this->renderText("OK");
	}
	
	private static function renderEntry ( $entry_id )
	{
		$xml = '<?xml version="1.0" encoding="ISO-8859-1"?>';
		$xml .= "<Video>";
		$entry = entryPeer::retrieveByPK( $entry_id );
		if ( $entry ) 
		{
			$seconds = (int)($entry->getLengthInMsecs()/1000);
			$xml .= "<PartnerId>" . $entry->getPartnerId() . "</PartnerId>" .
				"<Duration>" . $seconds . "</Duration>";
		}
		$xml .= "</Video>";


		return $xml;
	}
}
