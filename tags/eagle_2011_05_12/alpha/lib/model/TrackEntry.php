<?php

/**
 * Subclass for representing a row from the 'track_entry' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class TrackEntry extends BaseTrackEntry
{
	const TRACK_ENTRY_EVENT_TYPE_UPLOADED_FILE = 1;
	const TRACK_ENTRY_EVENT_TYPE_WEBCAM_COMPLETED = 2;
	const TRACK_ENTRY_EVENT_TYPE_IMPORT_STARTED = 3;
	const TRACK_ENTRY_EVENT_TYPE_ADD_ENTRY = 4;
	const TRACK_ENTRY_EVENT_TYPE_UPDATE_ENTRY = 5;
	const TRACK_ENTRY_EVENT_TYPE_DELETED_ENTRY = 6;
	
	public static function addTrackEntry ( TrackEntry $te )
	{
		// can be switched of once we decide this is not needed
		if ( true )
		{
			if ( ! $te->getKs() ) $te->setKs ( kCurrentContext::$ks );
			if ( ! $te->getPartnerId() ) $te->setPartnerId( kCurrentContext::$partner_id );
			if ( ! $te->getPsVersion() ) $te->setPsVersion( kCurrentContext::$ps_vesion );
			if ( ! $te->getHostName() ) $te->setHostName( kCurrentContext::$host );
			if ( ! $te->getUid() ) $te->setUid(  kCurrentContext::$uid );
			if ( ! $te->getUserIp() ) $te->setUserIp( kCurrentContext::$user_ip );
			$te->setContext( kCurrentContext::$client_version . "|" .  kCurrentContext::$client_lang . "|" . kCurrentContext::$service . "|" . kCurrentContext::$action );
			$te->save();
		}
	}
}
