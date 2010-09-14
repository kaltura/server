#!/usr/bin/php
<?php
/*
  -i, --in=input file                                     infile
  -o, --out=output file                                   outfile
  -w, --width=output width                                width
  -h, --height=output height                              height
  -k, --kffreq=keyframe frequency                         kffreq
  -b, --bitrate=bitrate                                   bitrate
  -r, --framerate=framerate                               framerate
  -a, --audiorate=audiorate                               64,80,96,112,128,160,192,224,256,288,320
  -c, --codec=[VP6/H264]                                  codec
  --FE2_VP6_RC_MODE=vp6 rc mode                           CBR_1PASS = 0,
                                                          VBR_1PASS = 1,
                                                          CBR_2PASS = 2,
                                                          VBR_2PASS = 3
  --FE2_CUT_START_SEC=starting second                     starting second
  --FE2_CUT_STOP_SEC=stop second (-1 = duration)          stop second (-1 =
                                                          duration)
  --FE2_CROP_TOP=crop top                                 crop top
  --FE2_CROP_BOTTOM=crop bottom                           crop bottom
  --FE2_CROP_LEFT=crop left                               crop left
  --FE2_CROP_RIGHT=crop right                             crop right
*/

$infile 		= get_arg("i", "in");
$outfile 		= get_arg("o", "out");
$width 			= get_arg("w", "width");
$height 		= get_arg("h", "height");
$kffreq 		= get_arg("k", "kffreq");
$bitrate 		= get_arg("b", "bitrate");
$framerate  	= get_arg("r", "framerate");
$audiorate 		= get_arg("a", "audiorate");
$codec 			= get_arg("c", "codec");
$rc_mode 		= get_arg("FE2_VP6_RC_MODE");
$start_sec		= get_arg("FE2_CUT_START_SEC");
$stop_sec		= get_arg("FE2_CUT_STOP_SEC");
$crop_top		= get_arg("FE2_CROP_TOP");
$crop_bottom	= get_arg("FE2_CROP_BOTTOM");
$crop_left		= get_arg("FE2_CROP_LEFT");
$crop_right		= get_arg("FE2_CROP_RIGHT");



if (!$infile || !$outfile)
{
	die("infile (-i) and outfile (-o) are mandatory");
}

if ((strpos($infile, "http://") === null) 	|| 
	(strpos($infile, "https://") === null) 	|| 
	(strpos($infile, "ftp://") === null) 	|| 
	(strpos($infile, "sftp://") === null))
{
	die("infile (-i) must be a valid uri");	
}

if (!$codec)
{
	$codec = "VP6";
}

$add_media_xml = get_template_request();
set_credentials($add_media_xml);
set_request_param($add_media_xml, "ACTION", "AddMedia");
set_request_param($add_media_xml, "SOURCE", $infile);
set_request_param($add_media_xml, "TURBO", "yes");

if (strtoupper($codec) == "VP6")
{
	set_request_param($add_media_xml, "OUTPUT", "flv");
	set_request_param($add_media_xml, "VIDEO_CODEC", "VP6");
}
	
if (strtoupper($codec) == "H264")
{
	set_request_param($add_media_xml, "OUTPUT", "mp4");
	set_request_param($add_media_xml, "VIDEO_CODEC", "libx264");
}

if ($bitrate)
	set_request_param($add_media_xml, "BITRATE", $bitrate);

$size = null;
if ($width && $height)
	$size = $width."x".$height;
	
if ($width && !$height)
	$size = $width."x0";
	
if (!$width && $height)
	$size = "0x".$height;
	
if ($size)
	set_request_param($add_media_xml, "SIZE", $size);

if ($kffreq)
	set_request_param($add_media_xml, "KEYFRAME", $kffreq);

if ($framerate)
	set_request_param($add_media_xml, "FRAMERATE", $framerate);
	
if ($audiorate)
	set_request_param($add_media_xml, "AUDIO_BITRATE", $audiorate."k");
	
if ($crop_top)
	set_request_param($add_media_xml, "CROP_TOP", $crop_top);
	
if ($crop_bottom)
	set_request_param($add_media_xml, "CROP_BOTTOM", $crop_bottom);
	
if ($crop_left)
	set_request_param($add_media_xml, "CROP_LEFT", $crop_left);
	
if ($crop_right)
	set_request_param($add_media_xml, "CROP_RIGHT", $crop_right);

if ($rc_mode == "0")
{
	set_request_param($add_media_xml, "CBR", "yes");
	set_request_param($add_media_xml, "TWO_PASS", "no");
}

if ($rc_mode == "1")
{
	set_request_param($add_media_xml, "CBR", "no");
	set_request_param($add_media_xml, "TWO_PASS", "no");
}

if ($rc_mode == "2")
{
	set_request_param($add_media_xml, "CBR", "yes");
	set_request_param($add_media_xml, "TWO_PASS", "yes");
}

if ($rc_mode == "3")
{
	set_request_param($add_media_xml, "CBR", "no");
	set_request_param($add_media_xml, "TWO_PASS", "yes");
}

if ($start_sec)
	set_request_param($add_media_xml, "START", $start_sec);

if ($stop_sec)
	set_request_param($add_media_xml, "DURATION", $stop_sec - $start_sec);

clean_request($add_media_xml);
$add_media_response = send_request($add_media_xml);
	
preg_match("/\<mediaid\>(\w*)\<\/mediaid\>/i", $add_media_response, $media_id);
$media_id = isset($media_id[1]) ? $media_id[1] : null;
if (!$media_id)
{
	die ("media id was not returned!");
}

echo ("sleep for 10 seconds\n");
sleep(10);

$not_ready = true;
while($not_ready)
{
	$retries = 0;
	echo ("checking media id [$media_id]\n");
	$get_status_xml = get_template_request();
	set_credentials($get_status_xml);
	set_request_param($get_status_xml, "ACTION", "GetStatus");
	set_request_param($get_status_xml, "MEDIAID", $media_id);
	clean_request($get_status_xml);
	$get_status_response = send_request($get_status_xml);
	preg_match("/\<status\>([\w\s]*)\<\/status\>/", $get_status_response, $status);
	$status = (isset($status[1]) ? $status[1] : null); 
	if (!$status)
	{
		echo "status not found (tried [$retries] times)\n";
		$retries++;
		if ($retries > 10)
			die("too much retries with invalid response");
	}
	else
	{
		echo ("status for media id [$media_id] is [$status]\n");
		
		switch(strtolower($status))
		{
			case "finished":
				handle_status_finished($media_id, $get_status_response);
				break;
			case "error":
				handle_status_error($media_id, $get_status_response);
				break;
		}
	}
	echo ("sleep for 10 seconds\n");
	echo ("\n");
	sleep(10);
}

die;

// HELPERS

function handle_status_finished($media_id, $get_status_response)
{
	global $outfile;
	preg_match("/\<s3_destination\>(.*)\<\/s3_destination\>/", $get_status_response, $s3_destination);
	$s3_destination = (isset($s3_destination[1]) ? $s3_destination[1] : null);
	echo ("s3_destination [$s3_destination]\n");
	echo ("downloading file to [$outfile]\n");
	$cmd = 'curl -L -o"'.$outfile.'" "'.$s3_destination.'"';
	echo ("executing [$cmd]\n");
	passthru($cmd);
	die("done!");
}

function handle_status_error($media_id, $get_status_response)
{
	echo("error occured, exiting with status code 1\n");
	exit(1); 
}

function get_arg()
{
	global $args,$argv;
	if (!$args)
		$args = parse_args($argv);

	$func_args = func_get_args();
	foreach($func_args as $func_arg)
	{
		if (isset($args[$func_arg]))
			return $args[$func_arg];
	}
}

function parse_args($argv){
	array_shift($argv);
	
	// replace all "-k abc" wth "-k=abc"
	$new_args = array();
	for($i =0; $i < count($argv); $i++)
	{
		if (strlen($argv[$i]) == 2 && (substr($argv[$i], 0, 1) == "-"))
		{
			if (isset($argv[$i+1]) && substr($argv[$i+1], 0, 1) != "-")
			{
				$new_args[] = $argv[$i]."=".$argv[$i+1];
				$i++; // skip the next
				continue;
			}
		}
		$new_args[] = $argv[$i];
	}
	
	$argv = $new_args;
	
	$out = array();
	foreach ($argv as $arg){
		if (substr($arg,0,2) == '--'){
			$eqPos = strpos($arg,'=');
			if ($eqPos === false){
				$key = substr($arg,2);
				$out[$key] = isset($out[$key]) ? $out[$key] : true;
			} else {
				$key = substr($arg,2,$eqPos-2);
				$out[$key] = substr($arg,$eqPos+1);
			}
		} else if (substr($arg,0,1) == '-'){
			if (substr($arg,2,1) == '='){
				$key = substr($arg,1,1);
				$out[$key] = substr($arg,3);
			} else {
				$chars = str_split(substr($arg,1));
				foreach ($chars as $char){
					$key = $char;
					$out[$key] = isset($out[$key]) ? $out[$key] : true;
				}
			}
		} else {
			$out[] = $arg;
		}
	}
	return $out;
}

function set_request_param(&$xml, $name, $value)
{
	$xml = str_replace("%".$name."%", $value, $xml);
}

function set_credentials(&$xml)
{
	set_request_param($xml, "USERID", 1598);
	set_request_param($xml, "USERKEY", "0a33145cad3142419f3f94b50aac57db");
}

function clean_request(&$xml)
{
	preg_match_all("/\s*<\w*>%[A-Z\_]*%<\/\w*>/", $xml, $output);
	foreach($output[0] as $line)
	{
		$xml = str_replace($line, "", $xml);
	}
}

function send_request($xml)
{
	echo ("sending request:\n");
	echo ($xml);
	echo ("\n");
	
	$url = "http://manage.encoding.com/index.php";
	$fields = array(
		"xml" => $xml
	); 
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$result = curl_exec($ch);
	$error = curl_error($ch);
	curl_close($ch);
	
	if ($error)
		echo "curl error: $error\n";
	
	echo ("response:\n");
	echo ("\n");
	echo $result;
	
	return $result;
}

function get_template_request()
{
	return "<?xml version=\"1.0\"?>
<query>
    <!-- Main fields -->
    <userid>%USERID%</userid>
    <userkey>%USERKEY%</userkey>
    <action>%ACTION%</action>
    <mediaid>%MEDIAID%</mediaid>
    <source>%SOURCE%</source>
    <notify>%NOTIFY%</notify>

    <format>
        <!-- Format fields -->
        <output>%OUTPUT%</output>
        <video_codec>%VIDEO_CODEC%</video_codec>
        <audio_codec>%AUDIO_CODEC%</audio_codec>
        <bitrate>%BITRATE%</bitrate>
        <framerate>%FRAMERATE%</framerate>
        <audio_bitrate>%AUDIO_BITRATE%</audio_bitrate>
        <audio_sample_rate>%AUDIO_SAMPLE_RATE%</audio_sample_rate>
        <audio_volume>%AUDIO_VOLUME%</audio_volume>      
        <size>%SIZE%</size>
        <two_pass>%TWO_PASS%</two_pass>
        <cbr>%CBR%</cbr>
        <crop_left>%CROP_LEFT%</crop_left>
        <crop_top>%CROP_TOP%</crop_top>
        <crop_right>%CROP_RIGHT%</crop_right>
        <crop_bottom>%CROP_BOTTOM%</crop_bottom>
        <thumb_time>%THUMB_TIME%</thumb_time>
        <thumb_size>%THUMB_SIZE%</thumb_size>
        <add_meta>%ADD_META%</add_meta>
       
        <rc_init_occupancy>%RC_INIT_OCCUPANCY%</rc_init_occupancy>
        <minrate>%MINRATE%</minrate>
        <maxrate>%MAXRATE%</maxrate>
        <bufsize>%BUFSIZE%</bufsize>

        <keyframe>%KEYFRAME%</keyframe>
        <start>%START%</start>
        <duration>%DURATION%</duration>

        <!-- Destination fields -->
        <destination>%DESTINATION%</destination>
        <thumb_destination>%THUMB_DESTINATION%</thumb_destination>
               
        <!-- Turbo Encoding switch (OPTIONAL) -->              
        <turbo>%TURBO%</turbo>
    </format>
</query>";

}