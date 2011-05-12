<?php

list($range_from, $range_to, $range_length) = requestUtils::handleRangeRequest($total_length);


if ($total_length < 1000) // (actually $total_length is probably 13 or 143 - header + empty metadata tag) probably a bad flv maybe only the header - dont cache
	requestUtils::sendCdnHeaders("flv", $range_length, 0);
else
	requestUtils::sendCdnHeaders("flv", $range_length);
	
header('Content-Disposition: attachment; filename="video.flv"');

if ( $flv_wrapper == null )
	die;
	
$chunk_size =  1*1024*1024;

$uri = $_SERVER["REQUEST_URI"];
$startTime = microtime(true);
KalturaLog::info( "flvclipperSuccess: start dump range:$range_length, total:$total_length, $startTime, $chunk_size, $from_byte, $to_byte, $audio_only, $dump_from_byte, $range_from, $range_to, $cuepoint_time, $cuepoint_pos, $uri");

$flv_wrapper->dump($chunk_size, $from_byte, $to_byte, $audio_only, $dump_from_byte, $range_from, $range_to, $cuepoint_time, $cuepoint_pos);

$endTime = microtime(true);
$diffTime = $endTime - $startTime;
KalturaLog::info( "flvclipperSuccess: end dump range:$range_length, total:$total_length, $diffTime, $endTime, $chunk_size, $from_byte, $to_byte, $audio_only, $dump_from_byte, $range_from, $range_to, $cuepoint_time, $cuepoint_pos, $uri");

die; // prevent symfony from sending its headers and filling our logs with warnings
