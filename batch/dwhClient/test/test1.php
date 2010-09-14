<?php

/**
 * 
 * @package Scheduler
 * @subpackage Debug
 */

require_once ( dirname(__FILE__) ."/../KDwhClient.class.php");
require_once ( dirname(__FILE__) ."/../KBatchEvent.class.php");

KDwhClient::setFileName( "/var/log/dwh/dwh_batch_events" );

$param = $argv[1];
$loop_size = $argv[2];

$session_id = null;
for ( $i = 0 ; $i < $loop_size ; $i++ )
{
	if ( $i % 10 == 0 )
		$session_id = md5 ( "abc" . time() );
	$event = new KBatchEvent ();
	$event->batch_client_version = "123|{$param}";
	$event->batch_event_time = time();
	$event->batch_event_type_id = rand ( 1, 10 );
	$event->batch_name = "import";
	$event->batch_id = $i % 8;
	$event->batch_session_id = $session_id;
	$event->entry_id = rand ( 1000 , 10000 );
	$event->host_name = "host {$param}";
	$event->location_id = "1";
	$event->partner_id = rand ( 1, 10 );
	$event->section_id = "1";
	
	KDwhClient::send( $event );
	
	usleep( 200 );
}

?>