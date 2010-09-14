<?php
define ( "EVENT_LOG_SEPARATOR" , "," );
define ( "EVENT_LOG_LINE_SEPARATOR" , "\n" );

class kArgs 
{
	private $prefix = "";
	private $arr;
	public function kArgs ( array $arr )
	{
		$this->arr = $arr;
	}
	
	public function setPrefix ( $prefix )
	{
		$this->prefix = $prefix;
	}
	
	public function __get( $prop )
	{
		if ( $this->prefix )
		{
			if ( isset ($this->arr[$this->prefix.$prop] ) )
				return $this->arr[$this->prefix.$prop];
		}
		
		if ( isset ($this->arr[$prop] ) )
			return $this->arr[$prop];

		return null;
	}
}

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
$ps2_lines = 0;
$ignored_lines = 0;
$partial_events = 0;

// because the year is missing from the logs, needs to be set manually
$default_year = "2009";

// be sure to split the multi requests to several api calls

$start = microtime(true);
while(!feof($f))
{

	++$i;

	if ($i % 100 == 0)
		fprintf($stderr, "$i\r");

	$total_lines++;
		
	$s = fgets($f);
	
	if ( strpos ( $s , "partnerservices2" ) < 1 &&
		 strpos ( $s , "HTTP" ) < 1 ) continue;
	
	// there are several lines to read in order to fill in the api_event data.
	// event api_event is identified by it's session_id and is stored in the open_events array.
	// once flushed to disk - the api_event is removed from the array.
	

	// 125965956955.03, Dec 01 04:26:09 symfony [info] {sfRequest} request parameters array (  'entry_id' => '-1',  'kshow_id' => '292688',  'kdata' => 'YTo0OntzOjQ6ImJhc2UiO3M6MzQ6Imh0dHA6Ly93aWtpZWR1Y2F0b3Iub3JnL2luZGV4LnBocC8iO3M6MzoiYWRkIjtzOjQ5OiJTcGVjaWFsOkthbHR1cmFDb250cmlidXRpb25XaXphcmQ|02a3Nob3dfaWQ9MjkyNjg4IjtzOjQ6ImVkaXQiO3M6NDI6IlNwZWNpYWw6S2FsdHVyYVZpZGVvRWRpdG9yP2tzaG93X2lkPTI5MjY4OCI7czo1OiJzaGFyZSI7czo0NzoiaHR0cDovL3dpa2llZHVjYXRvci5vcmcvTGVzc29uXzg6X0Zvb2RfU3BvaWxhZ2UiO30=',  'referer' => 'http://wikieducator.org/Lesson_8:_Food_Spoilage',  'widget_type' => '3',  'module' => 'keditorservices',  'action' => 'getEntryInfo',)
	// 2009-11-30 07:52:11
	if ( preg_match ( "/([\d\.]*), ([a-zA-Z\d\- \:]*) symfony.*request parameters (.*)/" , $s , $matches ) )
	{
		$session_id = $matches[1] ;
		$raw_date = $matches[2];
		$t = strtotime( $raw_date );
		$datetime = date ( "Y-m-d H:i:s" , $t );
		
		$multi_request = 0;
		$service_index = 1;
		
		$action_arr_str = $matches[3];
		eval ( '$action_arr = ' . $action_arr_str . ";" );

		if ( @$action_arr["myaction"] == "multirequest" )
		{
			$multi_request=1;
		}
		
		$kargs = new kArgs( $action_arr );
		
		while ( true )
		{
			if ( $multi_request )
			{
				if ( isset ( $action_arr["request{$service_index}_service"] ) )
				{
					$kargs->setPrefix( "request{$service_index}_" ); 	
				}
				else
				{
					// no more calls on this multi
					break;
				}
			}
			
			$api_e = new kApiEvent();
			$api_e->datetime = $datetime;
			$api_e->session_id=$session_id;
			$api_e->ps_version="ps2";
			$api_e->is_multi_request = $multi_request ? $service_index : 0 ;
			$arr_str = "";

			// extract data from following array
			
			$api_e->service=$kargs->module;
			if ( $multi_request )
			{
				$api_e->action=$kargs->service;
			}
			else
			{
				$api_e->action=$kargs->myaction;
			}
	
			// complete the array and break
		 	$api_e->ks = $kargs->ks;
	
		 	$str = base64_decode( $api_e->ks , true ) ; // encode this string
			@list ( $hash , $real_str) = @explode ( "|" , $str , 2 );
					
			$a = ""; // just a dummy to store stuff in 
			@list ( $api_e->partner_id , $a , $a , $api_e->ks_type , $a , $api_e->uid , $a) =
					@explode ( ";" , $real_str );
						
			$api_e->ks_type = 0 + $api_e->ks_type;
	
			if ( ! $api_e->partner_id )
			{
				$api_e->partner_id = $kargs->partner_id;	
			}
			
			if ( ! $api_e->uid )
			{
				$api_e->uid = $kargs->uid;	
			}
			
			// set the entry_id
			if (isset ( $kargs->entry_id))
			{
				$api_e->entry_id = $kargs->entry_id;
			}
			
			// set the widget_id
			if ( isset ( $kargs->widget_id ) )
			{
				$api_e->widget_id = $kargs->widget_id;
			}
			
			// set the ui_conf_id
			if (isset ( $kargs->uiconf_id))
			{
				$api_e->ui_conf_id = $kargs->uiconf_id;
			}
			elseif (isset ( $kargs->ui_conf_id))
			{
				$api_e->ui_conf_id = $kargs->ui_conf_id;
			}	
				
			if ( $multi_request )
			{
				// if this is not the first index - write to log
				if ( $service_index == 1)
					storeApiEvent ( $session_id , $api_e ); // the first request will be stored and then handled in the status closure
				else
				 	$api_e->log();  // all the rest can be written directly to the log 
				 	
				// inc the service_index
				$service_index++;
			}
			else
			{
				// no need to loop
				storeApiEvent ( $session_id , $api_e );
				break;
			}
		}
		$ps2_lines++;
		continue;
	}

	// status - closuer
	// 125965956955.03, Dec 01 04:26:09 symfony [info] {sfResponse} send status "HTTP/1.0 200 OK"
	if ( preg_match ( "/([\d\.]*), ([a-zA-Z\d\- \:]*) symfony.*status \"HTTP\/[\d\.]* ([\d]*)/s" , $s , $matches ) )
	{
		$session_id = $matches[1] ;

		$api_e = getApiEvent ( $session_id );
		
		if ( ! $api_e ) 
		{
			continue; // this is actuall an error - it should happen very few times - maybe at the beginning of a day
		}
		$api_e->result = $matches[3];
		// update the api_event and unset
		
		$api_e->log();
		
		unsetApiEvent ( $session_id );
		
		continue;
	}

	$ignored_lines ++;
}

$partial_events = count($open_events);
// 
foreach ( $open_events as $ev )
{
	$ev->result = "???"; // didn't have an ending line
	$ev->log();	
	
	
}

fprintf($stderr, PHP_EOL . "total_lines [$total_lines] ps2_lines [$ps2_lines] ignored_lines [$ignored_lines] partial_events [$partial_events]" . PHP_EOL );

$end= microtime(true);

fprintf($stderr, "Total time [" . ($end - $start) . "]" );

fclose($stderr);
fclose($f);


