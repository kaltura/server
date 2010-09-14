<?php

/**
 * Stats Service
 *
 * @service stats
 * @package api
 * @subpackage services
 */
class StatsService extends KalturaBaseService 
{
	const SEPARATOR = ",";
	
	/**
	 * Will write to the event log a single line representing the event
	 * 
	 * 
 	* 
 client version - will help interprete the line structure. different client versions might have slightly different data/data formats in the line
event_id - number is the row number in yuval's excel
datetime - same format as MySql's datetime - can change and should reflect the time zone
session id - can be some big random number or guid
partner id
entry id
unique viewer
widget id
ui_conf id
uid - the puser id as set by the ppartner
current point - in milliseconds
duration - milliseconds
user ip
process duration - in milliseconds
control id
seek
new point
referrer
	
	 * KalturaStatsEvent $event
	 * 
	 * @action collect
	 */
	
	// TODO - should move to a lighter php script that is not part of the API - it is unnecessarily  heavy	
	function collectAction( KalturaStatsEvent $event )
	{
		$evenLogFullPath = kConf::get ( "event_log_file_path" );
		
		// if no file path - do nothing
		if ( ! $evenLogFullPath ) return;
		
		$http_referrer = isset ( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : "";
		
		$users_timestamp = $event->eventTimestamp;
		
		$eventLine = 
			$event->clientVer . self::SEPARATOR 
			. $event->eventType  . self::SEPARATOR
			. date ( "Y-m-d H:i:s" , time() ) . self::SEPARATOR   // use server time
			. $event->sessionId  . self::SEPARATOR
			. $event->partnerId  . self::SEPARATOR
			. $event->entryId  . self::SEPARATOR
			. $event->uniqueViewer  . self::SEPARATOR
			. $event->widgetId  . self::SEPARATOR
			. $event->uiconfId  . self::SEPARATOR
			. $event->userId  . self::SEPARATOR
			. $event->currentPoint  . self::SEPARATOR
			. $event->duration  . self::SEPARATOR
			. requestUtils::getRemoteAddress()  . self::SEPARATOR
			. $event->processDuration  . self::SEPARATOR
			. $event->controlId  . self::SEPARATOR
			. $event->seek  . self::SEPARATOR
			. $event->newPoint  . self::SEPARATOR
			. ( $event->referrer ? $event->referrer : "" )	. self::SEPARATOR	// duw to the way flash sends the referrer - allow it to override
			. $users_timestamp . self::SEPARATOR
			. PHP_EOL 
		;
		
		try
		{
			$res = $this->writeToFile ( $evenLogFullPath , $eventLine);
			if ( ! $res )
				KalturaLog::err( "Error while trying to write event to log. Event:\n". $eventLine );
        }
        catch ( Exception $ex )
        {
        	KalturaLog::err( "Error while trying to write event to log. Event:\n". $eventLine );	
        }
		return true;
	}

	/**
	 * 
	 * Will collect the kmcEvent sent form the KMC client
	 * // this will actually be an empty function because all events will be sent using GET and will anyway be logged in the apache log
	 * 
	 * @action kmcCollect
	 * 
	 * @param KalturaStatsKmcEvent $kmcEvent
	 */
	public function kmcCollectAction( KalturaStatsKmcEvent $kmcEvent )
	{
		
	}
	
	
	function writeToFile ( $evenLogFullPath , $eventLine )
	{
		// write line to log
		$stream = @fopen( $evenLogFullPath , 'a', false) ;
		$res = fwrite($stream, $eventLine);
		if ( ! $res )
		{
			// sleep a little and try again... 
			usleep ( 50 + rand ( 0,50 ));
			$res = fwrite($stream, $eventLine);
		}
		if (is_resource($stream) ) {
            fclose($stream);
		}
		
		return $res;
	}
	
	/**
	 * @action reportKceError
	 * @param KalturaCEError $kalturaCEError 
	 * @return KalturaCEError
	 */
	function reportKceErrorAction( KalturaCEError $kalturaCEError )
	{
		$_kalturaCEError = $kalturaCEError->toKceInstallationError();
		if (($this->getPartnerId() && !$_kalturaCEError->partnerId) ||
		    ($this->getPartnerId && $this->getPartnerId != $_kalturaCEError->partnerId))
		{
			$_kalturaCEError->setPartnerId ( $this->getPartnerId() );
		}
		$_kalturaCEError->save();
		
		$kalturaCEError = new KalturaCEError(); // start from blank
		$kalturaCEError->fromKceInstallationError( $_kalturaCEError );
		
		return $kalturaCEError;
	}
}