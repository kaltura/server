<?php
define ( "EVENT_LOG_SEPARATOR" , "," );
define ( "EVENT_LOG_LINE_SEPARATOR" , "\n" );


/* 
each api_event will hold the following data in this order:
api_client_version,
datetime
session_id
service
action
ps_version
is_multi_request
ks
ks_type
partner_id
uid
entry_id
ui_conf_id
widget_id
flavor_id
invoke_duration
dispatch_duration
serialize_duration
total_duration
result
all_params
exception
*/
class kApiEvent 
{
	private $arr;
	public function kApiEvent (  )
	{

	}
	
	
	public function log ()
	{
		$line = 
		$this->api_client_version . EVENT_LOG_SEPARATOR .
		$this->datetime . EVENT_LOG_SEPARATOR .
		$this->session_id . EVENT_LOG_SEPARATOR .
		$this->service . EVENT_LOG_SEPARATOR .
		$this->action . EVENT_LOG_SEPARATOR .
		$this->ps_version . EVENT_LOG_SEPARATOR .
		$this->is_multi_request . EVENT_LOG_SEPARATOR .
		$this->ks . EVENT_LOG_SEPARATOR .
		$this->ks_type . EVENT_LOG_SEPARATOR .
		$this->partner_id . EVENT_LOG_SEPARATOR .
		$this->uid . EVENT_LOG_SEPARATOR .
		$this->entry_id . EVENT_LOG_SEPARATOR .
		$this->ui_conf_id . EVENT_LOG_SEPARATOR .
		$this->widget_id . EVENT_LOG_SEPARATOR .
		$this->flavor_id . EVENT_LOG_SEPARATOR .
		(int)(1000*$this->invoke_duration) . EVENT_LOG_SEPARATOR .
		(int)(1000*$this->dispatch_duration) . EVENT_LOG_SEPARATOR .
		(int)(1000*$this->serialize_duration) . EVENT_LOG_SEPARATOR .
		(int)(1000*$this->total_duration) . EVENT_LOG_SEPARATOR .
		$this->result . EVENT_LOG_SEPARATOR .
		$this->all_params . EVENT_LOG_SEPARATOR .
		str_replace ( array ( "," , "\n" ) , array ( " ", " " ) , $this->exception ) . EVENT_LOG_LINE_SEPARATOR;
//		print_r ( $this );
		print $line;
	}

}

$open_events = array();


function storeApiEvent ( $session_id , $api_event )
{
	global 	$open_events;
	$open_events[$session_id] = $api_event;
	
//	print "storeApiEvent: " . print_r ( $api_event , true );
}

function getApiEvent ( $session_id )
{
	global 	$open_events;
	return @$open_events[$session_id];	
}

function unsetApiEvent ( $session_id )
{
	global 	$open_events;
	unset ($open_events[$session_id]);	
}


$stderr = fopen("php://stderr", "w");

$i = 0;

$file_name = @$argv[1];
$desired_mode = @$argv[2];

if ($file_name)
{
	fprintf($stderr, "\nfilename: $file_name\n");
	$f = @fopen($file_name, "r");
	if (!$f)
	{
		fprintf(stderr, "no file or file not found [$file_name]\n");
		die;
	}
}
else
	$f = fopen("php://stdin", "w");


if ( ! $desired_mode ) $desired_mode = 3 ; //all modes 	

$total_lines = 0;
$ps3_lines = 0;
$ignored_lines = 0;
$partial_events = 0;


// be sure to split the multi requests to several api calls

$start = microtime(true);
while(!feof($f))
{

	++$i;

	if ($i % 100 == 0)
		fprintf($stderr, "$i\r");

	$total_lines++;
		
	$s = fgets($f);
	
	// optimize - skip irrelevant lines
	if ( strpos ( $s , "sfCreole" ) ) continue;  // creole
	if ( strpos ( $s , "#" ) === 0  ) continue;  // stack trace
	if ( strpos ( $s , ">-----" )   ) continue;  // begin API
	if ( strpos ( $s , "API-start" )   ) continue;  // begin API
	if ( strpos ( $s , "Invoke start" )   ) continue;  // Invoke start
	if ( strpos ( $s , "Propel" )   ) continue;  // Propel
	if ( strpos ( $s , "Serialize start" )   ) continue;  // Serialize start
	
	
	
	// there are several lines to read in order to fill in the api_event data.
	// event api_event is identified by it's session_id and is stored in the open_events array.
	// once flushed to disk - the api_event is removed from the array.
	

	// 2009-11-30 07:52:11 [1784322224] [API] [KalturaDispatcher->dispatch] DEBUG: Dispatching service [playlist], action [list] with params Array
	if ( preg_match ( "/([\d\- \:]*) \[([\d]*)].*service \[(.*)\], action \[(.*)\]/" , $s , $matches ) )
	{
		$multi_request = 0;
		// read the following lines for the 
		$api_e = getApiEvent ( $matches[2] );
		if ( $api_e )
		{
			// must be a multi
			$api_e->is_multi_request = 1;
			// this is the case of a multi request - second request or later 
			$api_e->log();	
			// the next will be overriden
			$multi_request = 1;
		}
		
		$api_e = new kApiEvent();
		$api_e->datetime = $matches[1];
		$api_e->session_id=$matches[2];
		$api_e->service=$matches[3];
		$api_e->action=$matches[4];
		$api_e->ps_version="ps3";
		$api_e->is_multi_request = $multi_request;
		$arr_str = "";
		// extract data from following array
		$action_arr = array();
		while ( $s = fgets($f) )
		{
			$arr_str .= $s;

			if ( preg_match ( "/\[(.*)] => (.*)/" , $s , $m ))
			{
				$action_arr[$m[1]] = $m[2];
				continue;
			}
			
			if ( preg_match ( "/^\)/" , $s ) )
			{
				// complete the array and break
			 	$api_e->ks = @$action_arr["ks"];

			 	$str = base64_decode( $api_e->ks , true ) ; // encode this string
				@list ( $hash , $real_str) = @explode ( "|" , $str , 2 );
				
				$a = ""; // just a dummy to store stuff in 
				@list ( $api_e->partner_id , $a , $a , $api_e->ks_type , $a , $api_e->uid , $a) =
					@explode ( ";" , $real_str );
					
				$api_e->ks_type = 0 + $api_e->ks_type;

				// set the entry_id
				if (isset ( $action_arr["entryId"]))
				{
					$api_e->entry_id = $action_arr["entryId"];
				}
				elseif (isset ( $action_arr["mixId"]))
				{
					$api_e->entry_id = $action_arr["mixId"];
				}
				elseif (isset ( $action_arr["widget:entryId"]))
				{
					$api_e->entry_id = $action_arr["widget:entryId"];
				}
				
				// set the widget_id
				if ( isset ( $action_arr["widgetId" ] ) )
				{
					$api_e->widget_id = $action_arr["widgetId" ];
				}
				elseif ( isset ($action_arr["widget:sourceWidgetId" ]) )
				{
					$api_e->widget_id = $action_arr["widget:sourceWidgetId" ];
				}
				
				// set the ui_conf_id
				if (isset ( $action_arr["uiConfId"]))
				{
					$api_e->ui_conf_id = $action_arr["uiConfId"];
				}
				elseif (isset ( $action_arr["widget:uiConfId"]))
				{
					$api_e->ui_conf_id = $action_arr["widget:uiConfId"];
				}				
				
				break;
			}
		}
		
		storeApiEvent ( $matches[2] , $api_e );
		continue;
	}
	
	
	// collect invoke_duration
	// 2009-11-29 04:23:31 [59843975] [API] [KalturaDispatcher->dispatch] DEBUG: Invoke took - 0.012482881546021 seconds
	if ( preg_match ( "/([\d\- \:]*) \[([\d]*)].*Invoke took - ([\d\.]*)/" , $s , $matches ) )
	{
		$api_e = getApiEvent ( $matches[2] );
		if ( ! $api_e ) continue; // this is actuall an error - it should happen very few times - maybe at the beginning of a day
		$api_e->invoke_duration = $matches[3];
		storeApiEvent ( $matches[2] , $api_e );
		continue;
	}
	
	
	// collect dispatch_duration
	// 2009-11-29 04:23:31 [59843975] [API] [KalturaDispatcher->dispatch] DEBUG: Disptach took - 0.05302095413208 seconds
	if ( preg_match ( "/([\d\- \:]*) \[([\d]*)].*Disptach took - ([\d\.]*)/" , $s , $matches ) )
	{
		$api_e = getApiEvent ( $matches[2] );
		if ( ! $api_e ) continue; // this is actuall an error - it should happen very few times - maybe at the beginning of a day
		$api_e->dispatch_duration = $matches[3];
		storeApiEvent ( $matches[2] , $api_e );
		continue;
	}
	
	
	// collect serialize_duration
	// 2009-11-29 04:23:13 [1051747326] [API] [KalturaFrontController->serializeResponse] DEBUG: Serialize took - 0.0041739940643311
	if ( preg_match ( "/([\d\- \:]*) \[([\d]*)].*Serialize took - ([\d\.]*)/" , $s , $matches ) )
	{
		$api_e = getApiEvent ( $matches[2] );
		if ( ! $api_e ) continue; // this is actuall an error - it should happen very few times - maybe at the beginning of a day
		$api_e->serialize_duration = $matches[3];
		storeApiEvent ( $matches[2] , $api_e );
		continue;
	}
	
	
	// 2009-11-29 04:24:16 [1522924140] [API] [global] INFO: API-end [0.067569971084595]
	if ( preg_match ( "/([\d\- \:]*) \[([\d]*)].*API-end \[(.*)\]/" , $s , $matches ) )
	{
		$api_e = getApiEvent ( $matches[2] );
		if ( ! $api_e ) continue; // this is actuall an error - it should happen very few times - maybe at the beginning of a day
		$api_e->total_duration = $matches[3];
		// update the api_event and unset
		$api_e->log();

		
		unsetApiEvent ( $matches[2] );
		
		continue;
	}
	
	// collect exceptions
	// 2009-11-29 04:24:16 [520451400] [API] [KalturaFrontController->getExceptionObject] ERR: exception 'KalturaAPIException' with message 'Entry id "zt6jfpo0f4" not found' in /opt/k
	if ( preg_match ( "/([\d\- \:]*) \[([\d]*)].* ERR: exception (.*)/ms" , $s , $matches ) )
	{
		$api_e = getApiEvent ( $matches[2] );
		if ( ! $api_e ) continue; // this is actuall an error - it should happen very few times - maybe at the beginning of a day
		$api_e->exception = $matches[3];
		// update the api_event and unset
		
		continue;
	}

	
	// 2009-11-29 04:24:16 [520451400] [API] [global] DEBUG: <------------------------------------- api_v3 -------------------------------------
	if ( preg_match ( "/([\d\- \:]*) \[([\d]*)].*<-------/" , $s , $matches ) )
	{
		$api_e = getApiEvent ( $matches[2] );
		if ( ! $api_e ) continue; 
		
		// unset
		unsetApiEvent ( $matches[2] );
		
		continue;
	}	
	
}

$partial_events = count($open_events);
// 
foreach ( $open_events as $ev )
{
	$ev->result = "???"; // didn't have an ending line
	$ev->log();	
}


fprintf($stderr, PHP_EOL . "total_lines [$total_lines] ps3_lines [$ps3_lines] ignored_lines [$ignored_lines] partial_events [$partial_events]" . PHP_EOL );

$end= microtime(true);

fprintf($stderr, "Total time [" . ($end - $start) . "]" );

fclose($stderr);
fclose($f);


