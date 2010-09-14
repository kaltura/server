<?php

class myStreamSession
{
	public $sessionID;
	public $sessionStartDate;
	public $sessionEndDate;
	public $sessionType;
	public $sessionDirection = "play";
	public $partnerID;
	public $connectBytes;
	public $disConnectBytes = 0;
	public $csconnectBytes = 0;
	public $csdisConnectBytes;
	public $playBytes;
	public $stopBytes;
	public $ipAddress;
	public $kalturaStreamID;
}

$fields = array(
	"x-event" => 0,
	"x-category" => 1,
	"date" => 2,
	"time" => 3,
	"tz" => 4,
	"x-ctx" => 5,
	"s-ip" => 6,
	"x-pid" => 7,
	"x-cpu-load" => 8,
	"x-mem-load" => 9,
	"x-adaptor" => 10,
	"x-vhost" => 11,
	"x-app" => 12,
	"x-appinst" => 13,
	"x-duration" => 14,
	"x-status" => 15,
	"c-ip" => 16,
	"c-proto" => 17,
	"s-uri" => 18,
	"cs-uri-stem" => 19,
	"cs-uri-query" => 20,
	"c-referrer" => 21, // can sometimes find partnerID from referrer, if referred from KDP...
	"c-user-agent" => 22,
	"c-client-id" => 23,
	"cs-bytes" => 24,
	"sc-bytes" => 25,
	"x-sname" => 26,
	"x-sname-query" => 27,
	"x-file-name" => 28,
	"x-file-ext" => 29,
	"x-file-size" => 30,
	"x-file-length" => 31,
	"x-spos" => 32,
	"cs-stream-bytes" => 33,
	"sc-stream-bytes" => 34,
	"x-sc-qos-bytes" => 35,
	"x-trans-sname" => 36,
	"x-trans-sname-query" => 37,
	"x-trans-file-ext" => 38
	"x-comment" => 39,
);

$summary = array();
$i = 0;

$errhandle = null;
if (@$argv[1])
{
	$errhandle = fopen($argv[1], "w");
}
$sessions = array();
$partner_activity = array();

$left_data = file_get_contents("leftovers.data");
if(!empty($left_data))
{
    $sessions = unserialize($left_data);
}
$stderr = fopen("C:/development/Kaltura/fms-live/akamai_77659.fl_S.201003210000-2400-0.out", "w");
$handle = fopen("C:/development/Kaltura/fms-live/akamai_77659.fl_S.201003220000-2400-0.log", "r");
if ($handle) {
    while (!feof($handle)) {
        $line = rtrim(fgets($handle), "\n");
	$i++;
	if ($i % 10000 == 0) fprintf($stderr, "$i\r");
	
	$line_parts = explode("\t", $line);
	if(count($line_parts) != 40)
		continue;
	
	$session_id = $line_parts[$fields['c-client-id']];
	if(key_exists($session_id, $sessions))
	{
		$session_obj = $sessions[$session_id];
	}
	else
	{
		$session_obj = new myStreamSession();
		$session_obj->sessionID = $session_id;
	}
	
	$functionName = str_replace('-', '_', $line_parts[$fields['x-event']]);
	if(function_exists($functionName)) // skip irrelevant events
	{
		$functionName($session_obj, $line_parts);
	}

	$sessions[$session_id] = $session_obj;
    }
    fclose($handle);
    
    print(count($sessions));
    $remove_sessions = array();
    foreach($sessions as $session_id => $session)
    {
      if ($session->disConnectBytes != 0 || $session->csdisConnectBytes != 0)
      {
        $sub_activity = ($session->sessionDirection == 'broadcast')? 702: 703;
		$serverToClienAmount = $session->disConnectBytes - $session->connectBytes;
		$clientToServerAmount = $session->csdisConnectBytes - $session->csconnectBytes;
		if($session->sessionDirection == 'play')
		{
			$type = 'play';
			$uplink = 0;
			$downlink = floor($serverToClienAmount/1024);
		}
		else
		{
			$type = 'broadcast';
			$uplink = floor($clientToServerAmount/1024);
			$downlink = 0;
		}
        
        echo 'insert into sessions(activity, sub_activity, activity_statr_date,activity_end_date,partner_id, uplink, downlink, ip, session) '.
					"values(7, $sub_activity, '$session->sessionStartDate', '$session->sessionEndDate', $uplink, $downlink, '$session->ipAddress', '$session->kalturaStreamID');".PHP_EOL;
        $remove_sessions []=$session_id;
      }
//	aggregate_partner_activity($session);
    }
    
    foreach($remove_sessions as $session_id)
    {
        unset($sessions[$session_id]);
    }
}

 file_put_contents("leftovers.data", serialize($sessions));
//var_dump($partner_activity);
//output_sql_commands();

if ($errhandle)
	fclose($errhandle);
exit();

function add_activity_for_partner($partner_id, $activity_date, $amount, $type)
{
	global $partner_activity;
	if(!isset($partner_activity[$partner_id]))
		$partner_activity[$partner_id] = array();
	if(!isset($partner_activity[$partner_id][$type]))
		$partner_activity[$partner_id][$type] = array();
	if(!isset($partner_activity[$partner_id][$type][$activity_date]))
		$partner_activity[$partner_id][$type][$activity_date] = 0;
	
	$partner_activity[$partner_id][$type][$activity_date] += $amount;
}

function output_sql_commands() {
	global $partner_activity;
	foreach($partner_activity as $partnerId => $session_types)
	{
		if(!$partnerId) continue;
		foreach($session_types as $type => $activities)
		{
			foreach($activities as $activity_date => $amount)
			{
				$sub_activity = ($type == 'ondemand')? 701: (($type == 'live-bc')? 702: 703);
				$amount = floor($amount/1024); // change amount to KB
				echo 'insert into partner_activity(activity, sub_activity, activity_date, partner_id, amount) '.
					"values(7, $sub_activity, '$activity_date', $partnerId, $amount);".PHP_EOL;
			}
		}
	}
}

function aggregate_partner_activity(myStreamSession $session) {
	global $sessions,$partner_activity;
	switch($session->sessionType)
	{
		case 'ondemand':
/*			$amount = $session->disConnectBytes - $session->connectBytes;
			if(!is_numeric($session->partnerID)) var_dump($session);
			add_activity_for_partner($session->partnerID, $session->sessionDate, $amount, $session->sessionType);*/
			break;
		case 'live':
			$serverToClienAmount = $session->disConnectBytes - $session->connectBytes;
			$clientToServerAmount = $session->csdisConnectBytes - $session->csconnectBytes;
			if($serverToClienAmount > $clientToServerAmount)
			{
				$type = 'live-play';
				$amount = $serverToClienAmount; // assume 'live play session'
			}
			else
			{
				$type = 'live-bc';
				$amount = $clientToServerAmount;
			}
//			add_activity_for_partner($session->partnerID, $session->sessionDate, $amount, $type);
			break;
		default:
			echo '# no session type for session '.$session->sessionID.PHP_EOL;
	}
	// unset($sessions[$session->sessionID]);
}

function auth_play(&$sess, $line_parts) {
	global $fields;
	if(!$sess->kalturaStreamID && $line_parts[$fields['x-app']] == 'live')
		$sess->kalturaStreamID = $line_parts[$fields['x-sname']];
};
function connect(&$sess, $line_parts) {
	global $fields;
	$sess->connectBytes = $line_parts[$fields['sc-bytes']];
	$sess->csconnectBytes = $line_parts[$fields['cs-bytes']];
	$sess->sessionStartDate = $line_parts[$fields['date']] . ' ' . $line_parts[$fields['time']];
	$sess->ipAddress = $line_parts[$fields['c-ip']];
	if(!$sess->sessionType && $line_parts[$fields['x-app']] != '-')
		$sess->sessionType = $line_parts[$fields['x-app']];
};
function connect_pending(&$sess, $line_parts) {
	global $fields;
	if(!$sess->sessionType && $line_parts[$fields['x-app']] != '-')
		$sess->sessionType = $line_parts[$fields['x-app']];
};
function disconnect(&$sess, $line_parts) {
	global $fields;
	$sess->disConnectBytes = $line_parts[$fields['sc-bytes']];
	$sess->csdisConnectBytes = $line_parts[$fields['cs-bytes']];
	$sess->sessionEndDate = $line_parts[$fields['date']] . ' ' . $line_parts[$fields['time']];	
};
function play(&$sess, $line_parts) {
	global $fields;
	if($line_parts[$fields['x-app']] == 'live')
	{
		$sess->kalturaStreamID = $line_parts[$fields['x-ctx']];
	}
/*	else
	{
		$partner_ids = getPartnerDetailsFromUrl($line_parts[$fields['x-ctx']]);
		$sess->partnerID = $partner_ids["pid"];
	}*/
	$sess->playBytes = $line_parts[$fields['sc-stream-bytes']];
	if(!$sess->sessionType && $line_parts[$fields['x-app']] != '-')
		$sess->sessionType = $line_parts[$fields['x-app']];
};
function publish(&$sess, $line_parts) {
	global $fields;
	if ($line_parts[$fields['x-app']] == 'live')
	{
	    $sess->sessionDirection = 'broadcast';
    	if(!$sess->kalturaStreamID)
	    	$sess->kalturaStreamID = $line_parts[$fields['x-sname']];
	}
};
function stop(&$sess, $line_parts) {
	global $fields;
	$sess->stopBytes = $line_parts[$fields['sc-stream-bytes']];
};
function logerror($msg)
{
	global $errhandle;
	if ($errhandle)
		fprintf($errhandle, "%s", $msg);
}

function parseDate($text)
{
	return;
}

function getPartnerDetailsFromUrl($url)
{
	$result = array();
	if (preg_match("/\/p\/([0-9]+)\//", $url, $result) || preg_match("/\/p\/([0-9]+)\/sp\/([0-9]+)\//", $url, $result))
	{
		return array (
			"pid" => $result[1],
			"subpid" => @$result[2]
		);
	}
	else
	{
		logerror("ERROR: partner details not found in url [" . $url . "]\n");
		return null;
	}
}

/** unused functions / events
function play_continue(&$sess, $line_parts) {
	global $fields;
	
};
function pause(&$sess, $line_parts) {
	global $fields;
	
};
function client_pause(&$sess, $line_parts) {
	global $fields;
	
};
function client_unpause(&$sess, $line_parts) {
	global $fields;
	
};
function seek(&$sess, $line_parts) {
	global $fields;
	
};
function unpause(&$sess, $line_parts) {
	global $fields;
	
};
function connect_continue(&$sess, $line_parts) {
	global $fields;
	
};
*/