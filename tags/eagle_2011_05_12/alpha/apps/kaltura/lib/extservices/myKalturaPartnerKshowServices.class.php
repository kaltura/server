<?php
//define('MODULES' , SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR);
//require_once(MODULES.'search/actions/entryFilter.class.php');

class myKalturaPartnerKshowServices extends myKalturaKshowServices implements IMediaSource
{
	const AUTH_SALT = "myKalturaPartnerKshowServices:gogog123";
	const AUTH_INTERVAL = 3600;
	
	protected $id = entry::ENTRY_MEDIA_SOURCE_KALTURA_PARTNER_KSHOW;
	
	private static $NEED_MEDIA_INFO = "0";
	
	// assume the extraData is the partner_id to be searched 
	protected function getKshowFilter ( $extraData )
	{
		$filter = new kshowFilter ();
		// This is the old way to search within a partner
//		$entry_filter->setByName ( "_eq_partner_id" , $extraData );

		// this is the better way -
		$filter->setPartnerSearchScope( $extraData );
		return $filter;
	}
}
?>