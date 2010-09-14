<?php
//define('MODULES' , SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR);
//require_once(MODULES.'search/actions/entryFilter.class.php');

class myKalturaPartnerServices extends myKalturaServices implements IMediaSource
{
	const AUTH_SALT = "myKalturaPartnerServices:gogog123";
	const AUTH_INTERVAL = 3600;
	
	protected $id = entry::ENTRY_MEDIA_SOURCE_KALTURA_PARTNER;
	
	private static $NEED_MEDIA_INFO = "0";
	
	public function __construct()
	{
		parent::__construct();
		self::$s_clazz = get_class();
	}
	
	// assume the extraData is the partner_id to be searched 
	protected function getEntryFilter ( $extraData )
	{
		$entry_filter = new entryFilter ();
		// This is the old way to search within a partner - allow both
		$entry_filter->setByName ( "_eq_partner_id" , $extraData );

		// this is the better way -
		$entry_filter->setPartnerSearchScope( $extraData );
		return $entry_filter;
	}
}
?>