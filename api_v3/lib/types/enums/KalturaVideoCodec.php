<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaVideoCodec extends KalturaStringEnum
{
	const NONE = "";
	
	const VP6 = "vp6";
	const H263 = "h263";
	const H264 = "h264";
	const H264B = "h264b";
	const H264M = "h264m";
	const H264H = "h264h";
	const FLV = "flv";
	const MPEG4 = "mpeg4";
	const THEORA = "theora";
	const WMV2 = "wmv2";
	const WMV3 = "wmv3";
	const WVC1A = "wvc1a";
	const VP8 = "vp8";
	const MPEG2 = "mpeg2";
	const APCO = "apco";	// 36mbps,	profile:0
	const APCS = "apcs";	// 75mbps,	profile:1
	const APCN = "apcn";	// 112mbps,	profile:2
	const APCH = "apch";	// 185mbps,	profile:3
	const DNXHD= "dnxhd";
	const DV = "dv";
	const VP9 = "vp9";
	const COPY = "copy";
}