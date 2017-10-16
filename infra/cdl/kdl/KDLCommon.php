<?php
	class KDLSanityLimits {
		const MinDuration = 100;
		const MaxDuration = 360000000;	// 100h
		const MinBitrate = 10;			// 10Kbps
		const MaxBitrate = 400000;		// 400MBps
		const MinFileSize = 1;
		const MaxFileSize = 40000000000;		// 40GB
		const MinDimension = 10;
		const MaxDimension = 5000;		// 
	
		const MinFramerate = 2;
		const MaxFramerate = 90;		// 
		const MinDAR = 0.5;
		const MaxDAR = 2.5;		// 
		const MinDurationFactor = 0.5;
		const MaxDurationFactor = 1.5;
	}

	class KDLVideoBitrateNormalize {
					/*
					 * Ratios that represent quaility/efficency diffrences beween various 
					 * codecs/codec groups per given bitrate. 
					 * For example - H264 assumed to be twice more efficient than H263, and so on. 
					 */
		const BitrateH263Factor = 1.0;
		const BitrateVP6Factor = 1.5;
		const BitrateH264Factor = 2.0;
		const BitrateScreencastFactor = 6.0;
		const BitrateH265Factor = 3.0;
		const BitrateOthersRatio = 1.3;
		
					/*
					 * Following the codec-vs-codec normalization, this factor gives additional 20%
					 * to the bitarate to cope with the built-in transcoding quality reduction  
					 */
		const TranscodingFactor = 1.2;	
		
		static $BitrateFactorCategory1 = array(KDLVideoTarget::H263,KDLVideoTarget::FLV, "h263", "h.263", "s263", "flv1", "theora");
		static $BitrateFactorCategory2 = array(KDLVideoTarget::VP6, "vp6", "vp6e", "vp6s", "flv4");
		static $BitrateFactorCategory3 = array(KDLVideoTarget::H264, KDLVideoTarget::H264B, 
											   KDLVideoTarget::H264M,KDLVideoTarget::H264H,
											   KDLVideoTarget::WMV3, KDLVideoTarget::WVC1A,
											   KDLVideoTarget::VP8,
											   "h264", "h.264", "x264", "avc1", "wvc1",
											   "avc", "wmv3", "wmva", "rv40", "realvideo4", "rv30", "realvideo3");
		static $BitrateFactorCategory4 = array("g2m3", "g2m4", "gotomeeting3", "gotomeeting4", "gotomeeting", 
												"tsc2", "tscc", "techsmith", "mss1", "mss2");
		static $BitrateFactorCategory5 = array(KDLVideoTarget::H265, "hevc", "hev1", "h.evc", "h.265", "v_mpegh/iso/hevc", 
											   KDLVideoTarget::VP9, "v_vp9");
		
		public static function NormalizeSourceToTarget($sourceCodec, $sourceBitrate, $targetCodec, $transcodingFactor=self::TranscodingFactor)
		{
			$ratioTrg = self::BitrateVP6Factor;
			if(in_array($targetCodec, self::$BitrateFactorCategory1))
				$ratioTrg = self::BitrateH263Factor;
			else if(in_array($targetCodec, self::$BitrateFactorCategory2))
				$ratioTrg = self::BitrateVP6Factor;
			else if(in_array($targetCodec, self::$BitrateFactorCategory3))
				$ratioTrg = self::BitrateH264Factor;
			else if(in_array($targetCodec, self::$BitrateFactorCategory5))
				$ratioTrg = self::BitrateH265Factor;

			$ratioSrc = self::BitrateOthersRatio;
			if(in_array($sourceCodec, self::$BitrateFactorCategory1))
				$ratioSrc = self::BitrateH263Factor;
			else if(in_array($sourceCodec, self::$BitrateFactorCategory2))
				$ratioSrc = self::BitrateVP6Factor;
			else if(in_array($sourceCodec, self::$BitrateFactorCategory3))
				$ratioSrc = self::BitrateH264Factor;
			else if(in_array($sourceCodec, self::$BitrateFactorCategory4))
				$ratioSrc = self::BitrateScreencastFactor;
			else if(in_array($sourceCodec, self::$BitrateFactorCategory5))
				$ratioSrc = self::BitrateH265Factor;
	
			$brSrcNorm = $sourceBitrate*($ratioSrc/$ratioTrg)*$transcodingFactor;
			return round($brSrcNorm, 0);								   		}
		}
	
	class KDLConstants {
				/* FlavorBitrateRedundencyFactor - 
				 * The ratio between the current and prev flavors 
				 * should be at most of that value (curr/prev=ratio).
				 * Higher ratio means that the current flavor is redundant 
				 */
		const FlavorBitrateRedundencyFactor = 0.75;

		const FlavorBitrateComplianceFactor = 0.80;
		const FlavorFrameSizeComplianceFactor = 0.80;

		const ProductDurationFactor = 0.95;
		const ProductBitrateFactor = 0.7;
		const LowBitrateThresHold = 200;		// Video clips below that value will get special quantization/quality issue
		
		const IsmvMinimalFlavorRatio = 1.02; 	// Minimal BR difference between ISMV collection flavors. EE4 constraint
		const IsmvPeakBitrateRatio   = 1.3;
				/*
				 * TranscodersSourceBlackList
				 */
		static $TranscodersSourceBlackList = array(
			KDLTranscoders::ON2 => array(
				KDLConstants::VideoIndex=>array("icod","intermediate codec","apcn","apch"),
			),
			KDLTranscoders::FFMPEG => array(
//				KDLConstants::ContainerIndex=>array("ogg", "ogv"),
//				KDLConstants::VideoIndex=>array("iv41","iv50"), //"icod","intermediate codec"),
//				KDLConstants::AudioIndex=>array("vorbis"),
			),
			KDLTranscoders::FFMPEG_AUX => array(
//				KDLConstants::ContainerIndex=>array("ogg", "ogv"),
				KDLConstants::VideoIndex=>array("iv41","iv50","icod","intermediate codec"),
//				KDLConstants::AudioIndex=>array("vorbis"),
			),
			KDLTranscoders::MENCODER => array(
				KDLConstants::VideoIndex=>array("icod","intermediate codec","apcn","apch"),
				KDLConstants::AudioIndex=>array("a"), // MS Speach/Voice codec
			),
		);
				/*
				 * TranscodersTargetBlackList
				 */
		static $TranscodersTargetBlackList = array(
			KDLTranscoders::ON2 => array(
				KDLConstants::ContainerIndex=>array(KDLContainerTarget::WMV, KDLContainerTarget::ISMV, KDLContainerTarget::ISMA),
				KDLConstants::VideoIndex=>array("wvc1", KDLVideoTarget::WMV2,KDLVideoTarget::WMV3)),
			KDLTranscoders::EE3 => array(
				KDLConstants::ContainerIndex=>array(KDLContainerTarget::FLV, KDLContainerTarget::MP4)),
			KDLTranscoders::FFMPEG => array(),
//				KDLConstants::ContainerIndex=>array(KDLContainerTarget::ISMV)),
			KDLTranscoders::FFMPEG_AUX => array(
				KDLConstants::ContainerIndex=>array(KDLContainerTarget::ISMV, KDLContainerTarget::ISMA)),
			KDLTranscoders::ENCODING_COM => array(
				KDLConstants::ContainerIndex=>array(KDLContainerTarget::ISMV, KDLContainerTarget::ISMA)),
			KDLTranscoders::MENCODER => array(
				KDLConstants::ContainerIndex=>array(KDLContainerTarget::ISMV, KDLContainerTarget::ISMA)),
		);
		
		const MaxFramerate = 30.0;
		const DefaultGOP = 60;
		const DefaultGOPinSec = 2;
		const DefaultAudioSampleRate = 44100;
		const MinAudioSampleRate = 11025;
		const MaxAudioSampleRate = 48000;
		const DefaultAudioChannels = 2;
		
		const ContainerIndex = "container";
		const VideoIndex = "video";
		const AudioIndex = "audio";
		const ImageIndex = "image";
		const PdfIndex = 'pdf';
		const SwfIndex = 'swf';
		
	}

	class KDLTranscoders {
		const KALTURA 		= "kaltura.com";
		const FFMPEG 		= "ffmpeg";
		const MENCODER 		= "mencoder";
		const ON2 			= "cli_encode";
		const ENCODING_COM 	= "encoding.com";
		const FFMPEG_AUX 	= "ffmpeg-aux";
		const EE3			= "ee3";
		const EXPRESSION_ENCODER = "expression_encoder";
		const FFMPEG_VP8 	= "ffmpeg-vp8";
		const QUICK_TIME_PLAYER_TOOLS = "quick_time_player_tools";
		const QT_FASTSTART  = "qt-faststart";
		const AVIDEMUX  = "avidemux";
		const PDF2SWF = "pdf2swf";
		const PDF_CREATOR = "pdf_creator";
	};

	class KDLVideoAspectRatio {
		const AR_LOW = "LOW";
		const AR_5_4 = "5:4";
		const AR_4_3 = "4:3";
		const AR_3_2 = "3:2";
		const AR_8_5 = "8:5";
		const AR_1024_600 = "8:5";
		const AR_16_9 = "16:9";
		const AR_185 = "1.85:1";
		const AR_2048_1080 = "2048:1080";
		const AR_235 = "2.35:1";
		const AR_237 = "2.37:1";
		const AR_239 = "2.39:1";
		const AR_255 = "2.55:1";
		const AR_HIGH = "HIGH";
		
		public static function ConvertFrameSize($wid, $hgt)
		{
		$arr=array(
			1280/1024*1000=>KDLVideoAspectRatio::AR_5_4,
			1280/960*1000=>KDLVideoAspectRatio::AR_4_3,1392./1040*1000=>KDLVideoAspectRatio::AR_4_3,
			1280/854*1000=>KDLVideoAspectRatio::AR_3_2,1152/768*1000=>KDLVideoAspectRatio::AR_3_2,1280./848*1000=>KDLVideoAspectRatio::AR_3_2,
			320/200*1000=>KDLVideoAspectRatio::AR_8_5,1440./896*1000=>KDLVideoAspectRatio::AR_8_5,
			1680./1040*1000=>KDLVideoAspectRatio::AR_8_5,320./192*1000=>KDLVideoAspectRatio::AR_8_5,
			1024/600*1000=>KDLVideoAspectRatio::AR_1024_600,1024/592*1000=>KDLVideoAspectRatio::AR_1024_600,
			1360/768*1000=>KDLVideoAspectRatio::AR_16_9,848/480*1000=>KDLVideoAspectRatio::AR_16_9,
			1920/1080*1000=>KDLVideoAspectRatio::AR_16_9,1366/768*1000=>KDLVideoAspectRatio::AR_16_9,
			854/480*1000=>KDLVideoAspectRatio::AR_16_9,1920/1072*1000=>KDLVideoAspectRatio::AR_16_9,
			1850=>KDLVideoAspectRatio::AR_185,
			2048/1080*1000=>KDLVideoAspectRatio::AR_2048_1080,2048/1072*1000=>KDLVideoAspectRatio::AR_2048_1080,
			2350=>KDLVideoAspectRatio::AR_235,
			2370=>KDLVideoAspectRatio::AR_237,
			2390=>KDLVideoAspectRatio::AR_239,
			2550=>KDLVideoAspectRatio::AR_255,
		);
		$ratio = (int)($wid*1000/$hgt);
//print_r($arr);	
		$res="0x0";
		if(array_key_exists($ratio, $arr))
		{
			$res = $arr[$ratio];
		}
		else {
			$prev=key($arr);
			$first=$prev;
			foreach($arr as $key=>$val) {
				if($key>$ratio){
					break;
				}
				$prev=$key;
			}
			end($arr);
			$last = key(($arr));
			if($first*0.9>$ratio) {
				$res = KDLVideoAspectRatio::AR_LOW;
			}
			else if($last*1.1<$ratio) {
				$res = KDLVideoAspectRatio::AR_HIGH;
			}
			else if(abs($ratio-$prev)<abs($ratio-$key)){
				$res=$arr[$prev];
			}
			else {
				$res=$arr[$key];
			}
		}
//echo "\n,<br>key=".(int)($wid*1000/$hgt).",val=$res ";
		return $res;
		}	
	};

	class KDLCmdlinePlaceholders {
		const InFileName	= "__inFileName__";
		const OutFileName	= "__outFileName__";
		const OutDir		= "__outDir__";
		const ConfigFileName= "__configFileName__";
		const BinaryName	= "__binaryName__";
		const ForceKeyframes= "__forceKeyframes__";
		const WaterMarkFileName = "__waterMarkFileName__";
		const WaterMarkWidth = "__waterMarkWidth__";
		const WaterMarkHeight = "__waterMarkHeight__";
		const SubTitlesFileName = "__subTitlesFileName__";
	};
	
	class KDLContainerTarget {
		const FLV = "flv";
		const MP4 = "mp4";
		const MOV = "mov";
		const _3GP = "3gp";
		const AVI = "avi";
		const MP3 = "mp3";
		const OGG = "ogg";
		const OGV = "ogv";
		const WMV = "wmv";
		const WMA = "wma";
		const ISMV = "ismv";
		const ISMA = "isma";
		const MKV = "mkv";
		const WEBM = "webm";
		const MPEG = "mpeg";
		const MPEGTS = "mpegts";
		const M2TS = "m2ts";
		const APPLEHTTP = "applehttp";
		const WAV = "wav";
		const HLS = "hls";
		const M4V = "m4v";
		const MXF = "mxf";
		const COPY = "copy";
	};

	class KDLVideoTarget {
		const FLV  = "flv";
		const H263 = "h263";
		const VP6  = "vp6";
		const H264 = "h264";
		const H264B = "h264b";
		const H264M = "h264m";
		const H264H = "h264h";
		const MPEG4= "mpeg4";
		const WVC1A= "wvc1a";
		const WMV2 = "wmv2";
		const WMV3 = "wmv3";
		const THEORA = "theora";
		const VP8 = "vp8";
		const MPEG2= "mpeg2";
//	It has rate-control for 4 profiles: 'apch' - 185mbps, 'apcn' - 112mbps, 'apcs' - 75mbps, 'apco' - 36mbps.
//	The profiles are triggered from ffmpeg command line via '-profile' option ( 0 - apco, 1 - apcs, 2 - apcn, 3 - apch), the default profile is apch.
		const APCO = "apco";	// 36mbps,	profile:0, 'acpo' (Proxy)
		const APCS = "apcs";	// 75mbps,	profile:1, 'apcs' (LT),
		const APCN = "apcn";	// 112mbps,	profile:2, 'apcn' (SD)
		const APCH = "apch";	// 185mbps,	profile:3, 'apch' (HQ)
		const DNXHD= "dnxhd";
		const DV = "dv";
		const VP9 = "vp9";
		const H265 = "h265";
		const COPY = "copy";
	}

	class KDLAudioTarget {
		const MP3 = "mp3";
		const AAC = "aac";
		const AACHE = "aache";
		const WMA = "wma";
		const WMAPRO = "wmapro";
		const VORBIS = "vorbis";
		const AMRNB = "amrnb";
		const MPEG2= "mpeg2";
		const AC3= "ac3";
		const EAC3= "eac3";
		const PCM= "pcm";
		const COPY = "copy";
	};
	
	/**
	 * KDLAudioLayouts
	 * 
	 *
	 */
	class KDLAudioLayouts {
		const FL = "fl";
		const FR = "fr";
		const FC = "fc";
		const LFE = "lfe";
		const BL = "bl";
		const BR = "br";
		const BC = "bc";
		const SL = "sl";
		const SR = "sr";
		const DR = "dr";
		const DL = "dl";
		const DOWNMIX = "downmix";
		const MONO = "mono";
		const STEREO = "stereo";
		
		static $layouts = array(
			self::FL => array("(fl)"),
			self::FR => array("(fr)"),
			self::FC => array(),
			self::LFE => array("(lfe)"),
			self::BL => array("(bl)"),
			self::BR => array("(br)"),			
			self::DOWNMIX => array("downmix"),
			self::MONO =>array("mono"),	
			self::STEREO =>array("stereo"),
			"4"   => array(self::FL, self::FR, self::FC, self::BC),
			"4.0" => array(self::FL, self::FR, self::FC, self::BC),
			"4.1" => array(self::FL, self::FR, self::FC, self::LFE, self::BC),
			"5"   => array(self::FL, self::FR, self::FC, self::BL, self::BR),
			"5.0" => array(self::FL, self::FR, self::FC, self::BL, self::BR),
			"5.1" => array(self::FL, self::FR, self::FC, self::LFE, self::BL, self::BR),	
			"6"   => array(self::FL, self::FR, self::FC, self::BC, self::SL, self::SR),
			"6.0" => array(self::FL, self::FR, self::FC, self::BC, self::SL, self::SR),
			"6.1" => array(self::FL, self::FR, self::FC, self::LFE, self::BC, self::SL, self::SR),
			"7"   => array(self::FL, self::FR, self::FC, self::BL, self::BR, self::SL, self::SR),  
			"7.0" => array(self::FL, self::FR, self::FC, self::BL, self::BR, self::SL, self::SR),  
			"7.1" => array(self::FL, self::FR, self::FC, self::LFE, self::BL, self::BR, self::SL, self::SR),
		);
		
		static $surroundlayoutTypes = array(self::FL, self::FR, self::FC, self::LFE, self::BL, self::BR, self::MONO);
		
		/**
		 * 
		 * @param unknown_type $layout
		 * @return NULL|Ambigous <multitype:, multitype:string >
		 */
		public static function Detect($layout) {
			if(!isset($layout))
				return null;
			foreach(self::$layouts as $l=>$names){
				if(count($names)==0){
					continue;
				}
				foreach($names as $name){
					if(!isset($name) || strlen($name)==0){
						continue;
					}
					if(stristr($layout,$name)!=false){
						return $l;
					}
				}
			}
			return null;
		}
		
		/**
		 * 
		 * @param unknown_type $audioStreams
		 * @param unknown_type $layoutTypes
		 * @return multitype:unknown
		 */
		public static function matchLayouts($audioStreams, $layoutTypes = null)
		{
			if(!isset($layoutTypes)){
				$layoutTypes = array(self::FL, self::FR, self::FC,self::LFE, self::BL, self::BR, self::MONO);
			}
			else if(!is_array($layoutTypes))
				$layoutTypes = array($layoutTypes);
			$rv = array();
			$matchedLayouts = array();
			foreach ($audioStreams as $stream){
				// Make sure that the tested stream layout is unique (if not in matchedLayouts)
				if(isset($stream->audioChannelLayout) && !in_array($stream->audioChannelLayout, $matchedLayouts)){ 
					if(in_array($stream->audioChannelLayout, $layoutTypes)){
						$rv[] = $stream;
						$matchedLayouts[] = $stream->audioChannelLayout;
					}
					else if($stream->audioChannelLayout==self::MONO && in_array(self::FC, $layoutTypes) ){
						$rv[] = $stream;					
						$matchedLayouts[] = $stream->audioChannelLayout;
					}
				}
			}
			return $rv;
		}
		
		/**
		 * 
		 * @param unknown_type $layout
		 * @return number
		 */
		public static function getLayoutChannels($layout)
		{
			$n1;$n2;
			$n = sscanf($layout,"%d.%d",$n1, $n2);
			switch($n){
				case 1:
					return $n1;
				case 2:
					return ($n1+$n2);
				default:
					return 0;
			}
		}
	}
	
		/*
		 * Defines the heuristics to be used to define whether the resultant flavor-outut 
		 * 'comply' or 'not comply' with the requirements
		 * - Bitrate (default) - assets with bitrates higher than the source (+factor/threshold), will not be generated
		 * - FrameSize - assets with frame size that are larger than the source, will not be generated 
		 */
	class KDLOptimizationPolicy {
		const BitrateFlagBit = 1;
		const FrameSizeFlagBit = 2;
	}
	
	class KDLErrors {
		const SanityInvalidFileSize = 1000;
		const SanityInvalidFrameDim = 1001;
		const NoValidTranscoders = 1102;
		const MissingMediaStream = 1103;
		const NoValidMediaStream = 1104;
		const InvalidDuration = 1105;
		const PackageMovOnly = 1106;
		const DnxhdUnsupportedParams = 1107;
		const InvalidRequest = 1108;
		const InvalidFlavorParamConfiguration = 1109;
		const Encryption = 1110;
		const Other = 1500;
		
		public static function ToString($err, $param1=null, $param2=null){
			$str = null;
			switch($err){
				case self::SanityInvalidFileSize:
					if($param1!=="")
						$str = $err.",".$param1."#Invalid file size(".$param1."kb).";
					else
						$str = $err."#Invalid file size.";
					break;
				case self::SanityInvalidFrameDim:
					if($param1!=="")
						$str = $err.",".$param1."#Invalid frame dimensions (".$param1."px)";
					else
						$str = $err."#Invalid frame dimension.";
					break;
				case self::NoValidTranscoders:
					$str = $err."#No valid transcoders.";
					break;
				case self::MissingMediaStream:
					$str = $err."#Missing media stream.";
					break;
				case self::NoValidMediaStream:
					$str = $err."#Invalid File - No media content.";
					break;
				case self::InvalidDuration:
					$str = $err."#Product invalid duration - product($param1 sec), source($param2 sec).";
					break;
				case self::PackageMovOnly:
					$str = $err."#Video codec ($param1) can be packaged only in MOV format.";
					break;
				case self::DnxhdUnsupportedParams:
					$str = $err."#Following params set is not supported by DNXHD video codec ($param1).";
					break;
				case self::InvalidRequest:
					$str = $err."#Invalid request ($param1).";
					break;
				case self::InvalidFlavorParamConfiguration:
					$str = "#Invalid configured flavor param - Empty container";
					break;
				case self::Other:
				default:
					$str = $err."#Unknown.";
					break;
			}
			return $str;
		}
	}

	class KDLWarnings {
		const SanityInvalidDuration = 2000;
		const SanityInvalidBitrate = 2001;
		const SanityInvalidFarmerate = 2002;
		const SanityInvalidDAR = 2003;
		const ProductShortDuration = 2104;
		const ProductLowBitrate = 2105;
		const RedundantBitrate = 2106;
		const TargetBitrateNotComply = 2107;
		const TruncatingFramerate = 2108;
		const TranscoderFormat = 2109;
		const TranscoderDAR_PAR = 2110;
		const SetDefaultBitrate = 2111;
		const TranscoderLimitation = 2112;
		const MissingMediaStream = 2113;
		const ForceCommandline=2114;
		const ZeroedFrameDim=2115;
		const ChangingFormt=2116;
		const RemovingMultilineTranscoding=2117;
		const MissingTranscoderEngine=2118;
		const RealMediaMissingContent=2119;
		const TargetFrameSizeNotComply = 2120;
		const Other = 2500;
		
		public static function ToString($err, $param1=null, $param2=null){
			$str = null;
			switch($err){
				case self::SanityInvalidDuration:
					if($param1!=="")
						$str = $err.",".$param1."#Invalid duration(".$param1."msec).";
					else
						$str = $err."#Invalid duration.";
					break;
				case self::SanityInvalidBitrate:
					if($param1!=="")
						$str = $err.",".$param1."#Invalid bitrate(".$param1."kbs).";
					else
						$str = $err."#Invalid bitrate.";
					break;
				case self::SanityInvalidFarmerate:
					if($param1!=="")
						$str = $err.",".$param1."#Invalid framerate(".$param1."fps).";
					else
						$str = $err."#Invalid framerate.";
					break;
				case self::SanityInvalidDAR:
					if($param1!=="")
						$str = $err.",".$param1."#Invalid DAR(".$param1.").";
					else
						$str = $err."#Invalid DAR.";
					break;
				case self::ProductShortDuration:
					$str = $err.",".$param1.",".$param2."#Product duration too short - ".($param1/1000)."sec, required - ".($param2/1000)."sec.";
					break;
				case self::ProductLowBitrate:
					$str = $err.",".$param1.",".$param2."#Product bitrate too low - ".($param1)."kbps, required - ".($param2)."kbps.";
					break;
				case self::RedundantBitrate:
					$str = $err.","."#Redundant bitrate.";
					break;
					case self::TargetBitrateNotComply:
// "The target flavor bitrate {".$target->_video->_bitRate."} does not comply with the requested bitrate (".$this->_video->_bitRate.").";
					$str = "$err,$param1,$param2#The target flavor bitrate ($param1) does not comply with the requested bitrate (".$param2.").";
					break;
				case self::TruncatingFramerate:
					$str = $err.",".$param1.",".$param2."#Truncating FPS to (".round($param1,2).") from evaluated (".round($param2,2).").";
					break;
				case self::TranscoderFormat:
//"The transcoder (".$param1.") can not process the (".$param2. ").";
					$str = $err.",".$param1.",".$param2."#The transcoder (".$param1.") can not process the (".$param2.").";
					break;
				case self::TranscoderDAR_PAR:
//"#The transcoder (".$param1.") does not handle properly DAR<>PAR.";
					$str = $err.",".$param1."#The transcoder (".$param1.") does not handle properly DAR<>PAR.";
					break;
				case self::SetDefaultBitrate:
//"#Invalid bitrate value. Set to defualt ".$param1;
					$str = $err.",".$param1."#Invalid bitrate value. Set to defualt ".$param1;
					break;
				case self::TranscoderLimitation:
					$str = $err.",".$param1."#The transcoder (".$param1.") can not handle this content.";
					break;
				case self::MissingMediaStream:
					$str = $err."#Missing media stream.";
					break;
				case self::ForceCommandline:
					$str = $err."#Force Commandline.";
					break;
				case self::ZeroedFrameDim:
					$str = $err.",".$param1."#Got zeroed frame dim. Changed to default (".$param1.").";
					break;
				case self::ChangingFormt:
					$str = $err.",".$param1.",".$param2."#Changing the format from (".$param1.") to (".$param2.").";
					break;
				case self::RemovingMultilineTranscoding:
					$str = $err."#Removing Multiline Transcoding.";
					break;
				case self::MissingTranscoderEngine:
					$str = "$err,$param1#The transcoding engine ($param1) is missing.";
					break;
				case self::RealMediaMissingContent:
					$str = $err."#The source file is an outdated RM file.No valid media info.";
					break;
				case self::TargetFrameSizeNotComply:
					$str = "$err,$param1,$param2#The requested frame-size ($param1) does not comply with the target frame-size (".$param2.").";
					break;
				case self::Other:
				default:
					$str = $err."#Unknown.";
					break;
			}
			return $str;
		}
				
	}
	
/*
 * MPEG-PS
 * avc1, wmv3,wmva,wvc1,h264,x264
 * BDAV - Blu ray
 * Format    : MPEG Video
 * Format    : Bitmap
 */

?>
