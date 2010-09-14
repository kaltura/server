<?php

/**
 * Subclass for representing a row from the 'bb_forum' table.
 *
 * 
 *
 * @package lib.model
 */ 
class BBForum extends BaseBBForum
{

  public function getFormattedCreatedAt( $format = dateUtils::KALTURA_FORMAT )
  {
		return dateUtils::formatKalturaDate( $this , 'getCreatedAt' , $format );
  }
  
  
}
