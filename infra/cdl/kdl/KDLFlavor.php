<?php
//include_once("KDLCommon.php");
//include_once("KDLMediaDataSet.php");

/* ===========================
 * KDLFlavor
 */

class KDLFlavor extends KDLMediaDataSet {

	const RedundantFlagBit = 1;
	const BitrateNonComplyFlagBit = 2;
	const MissingContentNonComplyFlagBit = 4;
	const ForceCommandLineFlagBit = 8;

	/* ---------------------
	 * Data
	 */
	
	public	$_flags=0;
	public	$_isTwoPass=false;
	public  $_clipStart=null;
	public 	$_clipDur=null;
				/* 
				 * Contrary to to the 'clipDur' data member, the 'explicitClipDur' can not be 0 or null,
				 * for flavors that have a clip action.
				 * clipDur==0/null means that the clip should end when the source is finised.
				 * Some transcoders require 'explicit' duration (EE3), even in those cases.
				 * For those cases I have set this data member.
				 * Although the 'clipDur' can be changed to act in this way, I prefered to not to touch
				 * the origina logic (that works for ffmpeg,mec, on2, vlc), but rather set this new data member
				 */
	public	$_explicitClipDur=null; 
				/*
				 * To clip a file, we have to seek to required position.
				 * There are fast and slow seek method.
				 * Sveral fromats (mpeg2, theora), don't support fast seeks 
				 */
	public 	$_fastSeekTo = true;
	 
	public	$_transcoders = array();

		/* --------------------------
		 * following fields are for flavorOutputParams
		 * to be moved to the KDLWrap
		 */
	public 	$_id = null;
	public 	$_type = 1;
	public  $_tags=null;
	public  $_name=null;
	public	$_engineVersion=0;
	
	public	$_cdlObject = null; /* To avoid duplicating of fields that are only used for transfer 
									to flavorOutputParams objects, the original CDL object
									is saved on the KDLFlavor object. The required fields are 
									copied in the KDLWrap 
								*/ 
	/* --------------------------- */
	
	/* ----------------------
	 * Cont/Dtor
	 */
	public function __construct() {
		parent::__construct();
	}
	public function __destruct() {
		unset($this);
	}
	public function __clone() {
		if(!is_null($this->_container)) $this->_container = clone $this->_container;
		if(!is_null($this->_video)) $this->_video = clone $this->_video;
		if(!is_null($this->_audio)) $this->_audio = clone $this->_audio;
		if(!is_null($this->_cdlObject)) $this->_cdlObject = clone $this->_cdlObject;
	}
	
	/* ----------------------
	 * ProcessRedundancy
	 */
	public function ProcessRedundancy(KDLFlavor $prevFlavor){
		$rv = true;
		/*
		 * If no video => keep the flavor
		 */
		if($this->_video!=null && $prevFlavor->_video!=null) {
			/*
			 * The previous flavor should be atleast FlavorBitrateRedundencyFactor
			 * away, else - remove the current flavor.
			 */
			$redundRatio = $this->_video->_bitRate/$prevFlavor->_video->_bitRate;
			if($redundRatio>1) $redundRatio = 1/$redundRatio;
			if($redundRatio>KDLConstants::FlavorBitrateRedundencyFactor) {
				$this->_flags = $this->_flags | KDLFlavor::RedundantFlagBit;

				$this->_warnings[KDLConstants::VideoIndex][]= //"Redundant bitrate";
				KDLWarnings::ToString(KDLWarnings::RedundantBitrate);
			}
			else
			$rv = false;
		}

		if($this->_audio!=null && $prevFlavor->_audio!=null) {
			if($this->_audio->_bitRate==$prevFlavor->_audio->_bitRate) {
				$this->_flags = $this->_flags | KDLFlavor::RedundantFlagBit;

				$this->_warnings[KDLConstants::AudioIndex][]= //"Redundant bitrate";
				KDLWarnings::ToString(KDLWarnings::RedundantBitrate);
			}
			else
			$rv = false;
		}

		return $rv;
	}

	/* ---------------------------
	 * ValidateFlavor
	 */
	public function ValidateFlavor()
	{
		return parent::Initialize();
	}

	/* ---------------------------
	 * ToString
	 */
	public function ToString(){
		$rvStr = "flag($this->_flags)";
		if($this->_clipStart) {
			$rvStr .= ",clpStr($this->_clipStart)";
		}
		if($this->_clipDur) {
			$rvStr .= ",clpDur($this->_clipDur)";
		}
		$rvStr .= ",".parent::ToString();
		if(count($this->_errors)){
			$rvStr = $rvStr.",ERRS(".KDLUtils::arrayToString($this->_errors).")";
		}
		if(count($this->_warnings)){
			$rvStr = $rvStr.",WRNS(".KDLUtils::arrayToString($this->_warnings).")";
		}
		if(count($this->_transcoders)){
			$rvStr = $rvStr.",TRNS(".KDLUtils::arrayToString($this->_transcoders).")";
		}
		return $rvStr;
	}

	/* ---------------------------
	 * GenerateTarget
	 */
	public function GenerateTarget(KDLMediaDataSet $source) {
		if($source==null || !$source->IsDataSet() || $this->_flags&self::ForceCommandLineFlagBit) {
			KalturaLog::log("FORCE ". $this->_flags);
			$target = clone $this;
			if($target->_video && ($target->_video->_gop===null || $target->_video->_gop==0))
			$target->_video->_gop = KDLConstants::DefaultGOP;
				
			$target->_warnings[KDLConstants::ContainerIndex][] =
				KDLWarnings::ToString(KDLWarnings::ForceCommandline);
		}
		else {
			$target = $this->generateTargetFlavor($source);
			if($target->_video=="" && $target->_audio=="" && $target->_image==""){
				// "Invalid File - No media content";
				$target->_errors[KDLConstants::ContainerIndex][] = KDLErrors::ToString(KDLErrors::NoValidMediaStream);
			}

			if($target->validateTranscoders($source, $target->_transcoders)==false){
				// "No valid transcoder";
				$target->_errors[KDLConstants::ContainerIndex][] = KDLErrors::ToString(KDLErrors::NoValidTranscoders);
			}
//kLog::log("==>\n".print_r($target->_transcoders,true));
		}
		$this->generateCommandLines($target, $target->_transcoders);

		return $target;
	}

	/* ---------------------------
	 * generateCommandLines
	 */
	private function generateCommandLines(KDLFlavor $target, $transcoders){
		foreach($transcoders as $key=>$trPrmObj) {

			if(is_array($trPrmObj)){
				$this->generateOperationSetCommandLines($target, $trPrmObj);
			}
			else{
				$transcoders[$key] = $trPrmObj->GenerateCommandAndConfig($this, $target);
			}
		}
	}
	
	/* ---------------------------
	 * generateOperationSetCommandLines
	 */
	private function generateOperationSetCommandLines(KDLFlavor $target, $transcoders){
KalturaLog::log("==>\n");
		
		$cnt = count($transcoders);
		$i=1;
		foreach($transcoders as $key=>$trPrmObj) {
			$auxTrg = new KDLFlavor();
			$auxTrg = clone $target;
			$transcoders[$key] = $trPrmObj->GenerateCommandAndConfig($this, $auxTrg);
			$i++;
			continue;
		}
	}
		
	/* ---------------------------
	 * ValidateProduct
	 */
	public function ValidateProduct(KDLMediaDataSet $source, KDLFlavor $product)
	{
		KalturaLog::log( ".SRC-->".$source->ToString());
		KalturaLog::log( ".TRG-->".$this->ToString());
		KalturaLog::log( ".PRD-->".$product->ToString());
		
		$rv = $product->ValidateFlavor();

		if($source){
			$srcVid = $source->_video;
			$srcAud = $source->_audio;
			$srcCont = $source->_container;
		}
		else {
			$srcVid = null;
			$srcAud = null;
			$srcCont =null;
		}
		
		/*
		 * ARF (webex) sources don't have proper mediaInfo - thus can not validate the product, skip it
		 * 
		 * - The second portion of the 'if condition' is a workaround to handle invalidly passed inter-src 
		 * asset both as a source and as a product. 
		 * It is 'strstr' rather than 'strcmp', because call to 'product->ValidateFlavor' might add warnings to the ToString
		 */
//		if(isset($srcCont) && $srcCont->GetIdOrFormat()=='arf') {
		if((isset($srcCont) && $srcCont->GetIdOrFormat()=='arf') || strstr($product->ToString(),$source->ToString())!=false) {
			KalturaLog::log("ARF (webex) sources don't have proper mediaInfo - thus can not validate the product");
			return true;
		}
				
		/*
		 * WVM (DRM Widevine) sources don't have proper mediaInfo - thus can not validate the product, skip it
		 */
		if(isset($this->_container) && $this->_container->GetIdOrFormat()=='wvm') {
			KalturaLog::log("WVM (DRM Widevine) sources don't have proper mediaInfo - thus can not validate the product");
			return true;
		}
		
		/*
		 * Evaluate source duration, to be used to check the product duration validity 
		 */
$plannedDur = 0;
		{
			if($this->_clipDur && $this->_clipDur>0){
				$plannedDur = $this->_clipDur;
				$vDur = $plannedDur;
				$aDur = $plannedDur;
				$cDur = $plannedDur;

			}
			else {
				$vDur = isset($srcVid)? $srcVid->_duration: 0;
				$aDur = isset($srcAud)? $srcAud->_duration: 0;
				$cDur = isset($srcCont)? $srcCont->_duration: 0;
				$plannedDur = max(max($aDur, $vDur),$cDur);
			}
		}

		if($this->_video!==null) {
			if($product->_video===null){
				$product->_errors[KDLConstants::VideoIndex][] = KDLErrors::ToString(KDLErrors::MissingMediaStream);
				$rv=false;
			}
			else {
				$prdVid = $product->_video;
				$trgVid = $this->_video;

				if($plannedDur>0){
					if($prdVid->_duration<$plannedDur*KDLSanityLimits::MinDurationFactor 
					|| $prdVid->_duration>$plannedDur*KDLSanityLimits::MaxDurationFactor) 
					{
						//This check was added to filter out files that have no duration set on their metadata and are of type ogg or ogv to avoid failure on product validation (SUP 546)
						if($aDur==0 && in_array(strtolower($this->_container->GetIdOrFormat()), array("ogg", "ogv")))
						{
							//Do Nothing
						}
						else 
						{
							$product->_errors[KDLConstants::VideoIndex][] = // Invalid product duration
								KDLErrors::ToString(KDLErrors::InvalidDuration, $prdVid->_duration/1000, $plannedDur/1000);
							$rv=false;
						}
					}
					else if($prdVid->_duration<$plannedDur*KDLConstants::ProductDurationFactor) {
						$product->_warnings[KDLConstants::VideoIndex][] =
						KDLWarnings::ToString(KDLWarnings::ProductShortDuration, $prdVid->_duration, $plannedDur);
					}
				}
				
				if(isset($srcVid) && $prdVid->_bitRate<$trgVid->_bitRate*KDLConstants::ProductBitrateFactor) {
					$product->_warnings[KDLConstants::VideoIndex][] = // "Product bitrate too low - ".$prdVid->_bitRate."kbps, required - ".$trgVid->_bitRate."kbps.";
					KDLWarnings::ToString(KDLWarnings::ProductLowBitrate, $prdVid->_bitRate, $srcVid->_bitRate);
				}
			}
		}

		if($this->_audio!==null) {
			if($product->_audio===null){
				$product->_errors[KDLConstants::AudioIndex][] = KDLErrors::ToString(KDLErrors::MissingMediaStream);
				$rv=false;
			}
			else {
				$prdAud = $product->_audio;
				$trgAud = $this->_audio;
				
				if($plannedDur){ 
					if($prdAud->_duration<$plannedDur*KDLSanityLimits::MinDurationFactor 
					|| $prdAud->_duration>$plannedDur*KDLSanityLimits::MaxDurationFactor) {
						$product->_errors[KDLConstants::AudioIndex][] = // Invalid product duration 
						KDLErrors::ToString(KDLErrors::InvalidDuration, $prdAud->_duration/1000, $plannedDur/1000);
						$rv=false;
					}
					else if($prdAud->_duration<$plannedDur*KDLConstants::ProductDurationFactor) {
						$product->_warnings[KDLConstants::AudioIndex][] = // "Product duration too short - ".($prdAud->_duration/1000)."sec, required - ".($srcAud->_duration/1000)."sec.";
						KDLWarnings::ToString(KDLWarnings::ProductShortDuration, $prdAud->_duration, $plannedDur);
					}
				}
				if(isset($srcAud) && $prdAud->_bitRate<$trgAud->_bitRate*KDLConstants::ProductBitrateFactor) {
					$product->_warnings[KDLConstants::AudioIndex][] = // "Product bitrate too low - ".$prdAud->_bitRate."kbps, required - ".$trgAud->_bitRate."kbps.";
					KDLWarnings::ToString(KDLWarnings::ProductLowBitrate, $prdAud->_bitRate, $srcAud->_bitRate);
				}
			}
		}

		if($product->_video===null && $product->_audio===null) {
			// "Invalid File - No media content.";
			$product->_errors[KDLConstants::ContainerIndex][] = KDLErrors::ToString(KDLErrors::NoValidMediaStream);
		}
		KalturaLog::log( ".PRD-->".$product->ToString());

		return $rv;
	}

	/* ------------------------------
	 * IsValid
	 */
	public function IsValid()
	{
		return (count($this->_errors)==0);
	}

	/* ------------------------------
	 * IsRedundant
	 */
	public function IsRedundant()
	{
		return ($this->_flags & KDLFlavor::RedundantFlagBit);
	}

	/* ------------------------------
	 * IsComply
	 */
	public function IsNonComply()
	{
		return ( ($this->_flags & KDLFlavor::BitrateNonComplyFlagBit)
		||($this->_flags & KDLFlavor::MissingContentNonComplyFlagBit));
	}

	/* ------------------------------
	 * IsInArray
	 */
	public function IsInArray(array $arr)
	{
		foreach($arr as $member) {
			if($this->_id==$member->_id) {
				return $member;
			}
		}
		return null;
	}

	/* ---------------------------
	 * EvaluateFileExt
	 */
	public function EvaluateFileExt()
	{
		if($this->_container != null)
		{
			return $this->_container->_id;
		}
		else
		{
			return null;
		}
		
		switch($this->_container->_id){
			case "flv":
			case "avi":
			case "mp4":
			case "mov":
			case "3gp":
			case "ogg":
			case "ogv":
				return $this->_container->_id;
			default:
				return "flv";
		}
	}

	/* ---------------------------
	 * generateTarget
	 */
	private function generateTargetFlavor(KDLMediaDataSet $source) {
		$target = clone $this;
		if($this->_name!=null)
			$target->_name = $this->_name;
		if($this->_container!=""){
			$target->_container = clone $this->_container;
		}

		$sourceDur=0;
			/*
			 * Evaluate source duration
			 */
		if($source) {
			if($source->_video && $source->_video->_duration>0) {
				$sourceDur=$source->_video->_duration;
			}
			else if($source->_audio && $source->_audio->_duration>0) {
				$sourceDur=$source->_audio->_duration;
			}
			else if($source->_container && $source->_container->_duration>0) {
				$sourceDur=$source->_container->_duration;
			}
		}
			/*
			 * Evaluate cliping setting (if any) according to source dur
			 */
		if($sourceDur>0 && $sourceDur<$target->_clipStart+$target->_clipDur) {
			// Mantis 15712 case
			// zeroing the clipDur causes duration validation issues when cliping the end of the file.
//			$target->_clipDur=0;			
			$target->_explicitClipDur=$sourceDur-$target->_clipStart;
		}
		else if(isset($target->_clipStart) && (!isset($target->_clipDur) || $target->_clipDur==0)){
			$target->_explicitClipDur=$sourceDur-$target->_clipStart;
		}
		else
			$target->_explicitClipDur = $target->_clipDur;
			
			/*
			 * mpeg2 and theora video formats does not allow reliable 'fastSeekTo' (used on clipping)
			 */
		if($source->_video && $source->_video->IsFormatOf(array("mpeg video","theora"))){
			$target->_fastSeekTo = false;
		}
		else {
			$target->_fastSeekTo = true;
		}
		
		if($target->_container->_id==KDLContainerTarget::COPY){
			$target->_container->_id=self::EvaluateCopyContainer($source->_container);
		}
		
		$target->_container->_duration = $sourceDur;
		$target->_video = null;
		if($this->_video!="") {
			if($source->_video!="" && ($target->_container && !($target->_container->_id==KDLContainerTarget::MP3 || $target->_container->_id==KDLContainerTarget::WMA))){
				/*
				 * Evaluate flavor frame-size
				 */
				$target->_video = $this->evaluateTargetVideo($source->_video);
				if($target->_video->_bitRate<$this->_video->_bitRate*KDLConstants::FlavorBitrateCompliantFactor) {
					$target->_flags = $this->_flags | self::BitrateNonComplyFlagBit;
					$target->_warnings[KDLConstants::VideoIndex][] = // "The target flavor bitrate {".$target->_video->_bitRate."} does not comply with the requested bitrate (".$this->_video->_bitRate.").";
						KDLWarnings::ToString(KDLWarnings::TargetBitrateNotComply, $target->_video->_bitRate, $this->_video->_bitRate);
				}
			}
			else {
				if($target->_container && $target->_container->_id==KDLContainerTarget::ISMV) {
					$target->_warnings[KDLConstants::ContainerIndex][] = // "The target flavor bitrate {".$target->_video->_bitRate."} does not comply with the requested bitrate (".$this->_video->_bitRate.").";
						KDLWarnings::ToString(KDLWarnings::ChangingFormt, $target->_container->_id, KDLContainerTarget::WMA);
					$target->_container->_id=KDLContainerTarget::WMA;
				}
			}
		}

		$target->_audio = null;
		if($this->_audio!=""){
			if($source->_audio!=""){
				$target->_audio = $this->evaluateTargetAudio($source->_audio, $target);
			}
		}
		
		/*
		 * Handle multi-stream cases
		 *
		$target->_multiStream=self::evaluateMultiStream($source->_multiStream, $this->_multiStream);
		*/
		return $target;
	}

	/* ---------------------------
	 * EvaluateCopyContainer
	 */
	public static function EvaluateCopyContainer(KDLContainerData $source)
	{
		$format = KDLContainerTarget::MP4;
//'mp3', 'flash video', 'mpeg audio', 'quicktime', 'mpeg-4','matroska','mpeg video', 'mpeg-ps',
//'mpeg-ts','ogg','wave','webm','windows media','avi','bdav','dv','jpeg','png','mxf','realmedia','shockwave','aiff'
		switch($source->_format){
			case 'mpeg-4':
				$format = KDLContainerTarget::MP4;
				break;
			case 'flash video':
				$format = KDLContainerTarget::FLV;
				break;
			case 'mpeg audio':
			case 'mp3':
				$format = KDLContainerTarget::MP3;
				break;			
			case 'quicktime':
				$format = KDLContainerTarget::MOV;
				break;
			case 'matroska':
				$format = KDLContainerTarget::MKV;
				break;
			case 'mpeg video':
			case 'mpeg-ps':
			case 'mxf':
				$format = KDLContainerTarget::MPEG;
				break;
			case 'mpeg-ts':
			case 'bdav':
				$format = KDLContainerTarget::MPEGTS;
				break;
			case 'ogg':
				$format = KDLContainerTarget::OGG;
				break;
			case 'wave':
				$format = KDLContainerTarget::WAV;
				break;
			case 'webm':
				$format = KDLContainerTarget::WEBM;
				break;
			case 'windows media':
				$format = KDLContainerTarget::WMV;
				break;
			case 'avi':
			case 'dv':
			case 'realmedia':
			default:
				$format = KDLContainerTarget::AVI;
				break;
		}
		
		return $format;
	}
	
	/* ---------------------------
	 * evaluateTargetVideo
	 */
	public function evaluateTargetVideo(KDLVideoData $source)
	{
		$targetVid = clone $this->_video;
		$flavorVid = $this->_video;
		$sourceVid = $source;

		if($this->_video->_id=="") {
			switch($this->_container->_id){
				case KDLContainerTarget::FLV:
					$targetVid->_id = KDLVideoTarget::FLV;
					break;
				case KDLContainerTarget::AVI:
					$targetVid->_id = KDLVideoTarget::H264;
					break;
				case KDLContainerTarget::MP4:
					$targetVid->_id = KDLVideoTarget::H264;
					break;
				case KDLContainerTarget::MOV:
					$targetVid->_id = KDLVideoTarget::H264;
					break;
				case KDLContainerTarget::_3GP:
					$targetVid->_id = KDLVideoTarget::H264;
					break;
				case KDLContainerTarget::OGG:
				case KDLContainerTarget::OGV:
					$targetVid->_id = KDLVideoTarget::THEORA;
					break;
				case KDLContainerTarget::WMV:
					$targetVid->_id = KDLVideoTarget::WMV2;
					break;
				case KDLContainerTarget::ISMV:
					$targetVid->_id = KDLVideoTarget::WVC1A;
					break;
				case KDLContainerTarget::WEBM:
					$targetVid->_id = KDLVideoTarget::VP8;
					break;
				case KDLContainerTarget::MPEG:
				case KDLContainerTarget::MPEGTS:
				case KDLContainerTarget::APPLEHTTP:
					$targetVid->_id = KDLVideoTarget::H264;
					break;
			}
		}
		
		/*
		 * Evaluate flavor frame-size
		 */
 		$this->evaluateTargetVideoFramesize($sourceVid, $targetVid);

 		/*
		 * Following code is a hack to overcome ffmpeg/x264 AR disorder 
		 * that happens with several hdv source formats
		 */
		$srcVcodec = $source->GetIdOrFormat();
		if(isset($srcVcodec) && in_array($srcVcodec, array("dvh3", "dvhp", "hdv1","hdv2" ,"hdv3", "hdv6"))
//		&& isset($targetVid->_id) && in_array($targetVid->_id, array("h264", "h264b", "h264m","h264h" ))
		&& !($targetVid->_width==0 || $targetVid->_height==0)) {
			$targetVid->_dar = round($targetVid->_width/$targetVid->_height,4);
		}
		
		/*
		 * Evaluate flavor bitrate
		 */
		$this->evaluateTargetVideoBitrate($sourceVid, $targetVid);
		
		/*
		 * Frame Rate - If the flavor fps is zero, evaluate it from the source and
		 * the constants theshold.
		 */
		$this->evaluateTargetVideoFramerate($sourceVid, $targetVid);
				
		/*
		 * GOP - if gop not set, set it to 2sec according to the required frame rate,
		 * otherwise if gop param is in sec (_isGopInSec) ==> calculate form framerate,
		 * If framerate not set - DefaultGOP(60)
		 */
		if($flavorVid->_gop===null || $flavorVid->_gop==0) {
			if(isset($targetVid->_frameRate)){
				$targetVid->_gop = round(KDLConstants::DefaultGOPinSec*$targetVid->_frameRate);
			}
			else {
				$targetVid->_gop = KDLConstants::DefaultGOP;
			}
		}
		else if(isset($flavorVid->_isGopInSec) && $flavorVid->_isGopInSec>0) {
			if(isset($targetVid->_frameRate)){
				$targetVid->_gop = round($targetVid->_gop*$targetVid->_frameRate);
			}
			else {
				$targetVid->_gop = KDLConstants::DefaultGOP;
			}
		}

		$targetVid->_rotation = $sourceVid->_rotation;
		$targetVid->_scanType = $sourceVid->_scanType;
		
		return $targetVid;
	}
	
	/* ---------------------------
	 * evaluateTargetVideoFramesize
	 */
	private static function evaluateTargetVideoFramesize(KDLVideoData $source, KDLVideoData $target) 
	{
		$shrinkToSource = $target->_isShrinkFramesizeToSource;
		
		$widSrc = $source->_width;
		$hgtSrc = $source->_height;
		if($widSrc==0 || $hgtSrc==0)
			return;
		$darSrcFrame = $widSrc/$hgtSrc;
		/*
		 * DAR adjustment
		 */
		if($source->_dar!="" && $source->_dar>0){
			$darSrc = $source->_dar;
			$diff = abs(1-$darSrc/$darSrcFrame);
			if($diff>0.1) {
				$widSrc = $darSrc*$hgtSrc;
				$darSrcFrame = $darSrc;
			}
		}

		/*
		 * Evaluate target frame size, from the source frame size 
		 * and from the predefined target frame size
		 */

			/*
			 * Both target width and height are not set ==> use the source frame size 
			 */
		if(($target->_width==0 || $target->_width=="") && ($target->_height==0 || $target->_height=="")){
			$target->_height = $hgtSrc;
			$target->_width  = $widSrc;
		}
			/*
			 * The target width was net set ==> 
			 * evaluate it from the height while keeping source the aspect ratio 
			 */
		else if($target->_width==0 || $target->_width==""){
			$target->_width = $target->_height*$darSrcFrame;
			if($shrinkToSource && $target->_width>$widSrc) {
				$target->_height = $hgtSrc;
				$target->_width  = $widSrc;
			}
		}
			/*
			 * The target height was net set ==> 
			 * evaluate it from the width while keeping source the aspect ratio 
			 */
		else if($target->_height==0 || $target->_height==""){
			$target->_height = $target->_width/$darSrcFrame;
			if($shrinkToSource && $target->_height>$hgtSrc) {
				$target->_height = $hgtSrc;
				$target->_width  = $widSrc;
			}
		}
			/*
			 * Dual dimension 'keep aspect ratio' mode:
			 * Scale down the source to match inside the flavor params 
			 * predefined frame size while keeping source the aspect ratio
			 */
		else if(isset($target->_arProcessingMode) && $target->_arProcessingMode>0){
			$darTrgFrame = $target->_width/$target->_height;
				/*
				 * AR Mode - Match the both dims frame size & preserve AR mode 
				 */
			if($target->_arProcessingMode==1){
				/*
				 * The target AR is wider than the source
				 */
				if($darTrgFrame>$darSrcFrame){
					$target->_width = $target->_height*$darSrcFrame;
					if($shrinkToSource && $target->_width>$widSrc) {
						$target->_height = $hgtSrc;
						$target->_width  = $widSrc;
					}
				}
				/*
				 * The target AR is narrower than the source
				 */
				else {
					$target->_height = $target->_width/$darSrcFrame;
					if($shrinkToSource && $target->_height>$hgtSrc) {
						$target->_height = $hgtSrc;
						$target->_width  = $widSrc;
					}
				}
			}
				/*
				 * AR Mode - letterboxing
				 */
			else if($target->_arProcessingMode==2){
				/*
				 * The target AR is wider than the source
				 */
				if($shrinkToSource){
					if($darTrgFrame>$darSrcFrame){
						if($target->_height>$hgtSrc) $target->_height = $hgtSrc;
						$target->_width = $target->_height*$darTrgFrame;
					}
					/*
					 * The target AR is narrower than the source
					 */
					else {
						if($target->_width>$widSrc) $target->_width  = $widSrc;
						$target->_height = $target->_width/$darTrgFrame;
					}
				}
			}
		}
			/*
			 * Fixed target frame size
			 */
		else if($shrinkToSource) {
			if($target->_width>$source->_width) {
				$target->_width=$source->_width;
			}
			if($target->_height>$source->_height) {
				$target->_height=$source->_height;
			}
		}

		$target->_height = round($target->_height);
		$target->_width  = round($target->_width);
		
		/*
		 * For anamorphic pixels - set the width to match the required PAR 
		 * and adjsut the target DAR.
		 */
		if(isset($target->_anamorphic) && $target->_anamorphic!=0){
			$dar = $target->_width/$target->_height;
			if(abs($dar-$target->_anamorphic)>0.2) {
				$w=$target->_height*$target->_anamorphic;
				$w = round($w);
				$target->_dar = $dar;
				$target->_width = $w;
			}
		}
		
		/*
		 * x16 - make sure both hgt/wid comply to x16
		 * - if the frame size is an 'industry-standard', skip x16 constraint 
		 */
		if((isset($target->_forceMult16) && $target->_forceMult16 == 0)
		|| (($target->_width == 640 || $target->_width == 480) && $target->_height == 360) || ($target->_width == 1920 && $target->_height == 1080)){
			;
		}
		else {
			$target->_height = $target->_height -($target->_height%16);
			$target->_width  = $target->_width  -($target->_width%16);
		}
	}

	/* ---------------------------
	 * evaluateTargetVideoBitrate
	 * If flavor BR is higher than the source - keep the source BR
	 */
	private static function evaluateTargetVideoBitrate(KDLVideoData $source, KDLVideoData $target) 
	{
		if($target->_isShrinkBitrateToSource!=1) {
			return $target->_bitRate;
		}
		$brSrcNorm = KDLVideoBitrateNormalize::NormalizeSourceToTarget($source->_id, $source->_bitRate, $target->_id);
		
		if($target->_bitRate>$brSrcNorm){
			$target->_bitRate = $brSrcNorm;
		}
		return $target->_bitRate = round($target->_bitRate, 0);
	}

	/* ---------------------------
	 * evaluateTargetVideoFramerate
	 */
	private static function evaluateTargetVideoFramerate(KDLVideoData $source, KDLVideoData $target) 
	{
		/*
		 * Frame Rate - If the flavor fps is zero, evaluate it from the source and
		 * the constants theshold.
		 */
		if($target->_frameRate==0) {
			$target->_frameRate = $source->_frameRate;
			if(isset($target->_maxFrameRate) && $target->_maxFrameRate>0)
				$maxFR = $target->_maxFrameRate;
			else
				$maxFR = KDLConstants::MaxFramerate;
			if($target->_frameRate>$maxFR) {
				$target->_warnings[KDLConstants::VideoIndex][] =
					KDLWarnings::ToString(KDLWarnings::TruncatingFramerate, $maxFR, $target->_frameRate);
				$target->_frameRate=$target->_frameRate==50?25:$maxFR;
			}
			// For webcam/h263 - if FR==0, set FR=24
			else if($target->_frameRate==0 && $source->IsFormatOf(array("h.263","h263","sorenson spark","vp6")) ){
				$target->_frameRate=24;
			}
			
			/*
			 * For frame rates to comply w/HLS (relevant for low br 110 and 200 kfps)
			 * 
			 * 110 - either 10 (for ~30fps) or 8 (for 24/25fps)
			 * 200 - for 24 to 30fps, take half of the original targetFR
			 * otherwise - keep the targetFR (the targetFR<24)
			 */
			if($target->_isFrameRateForLowBrAppleHls){
				if($target->_bitRate<=110) {
					$target->_frameRate = $target->_frameRate>=29.97? 10: 8;
				}
				else if($target->_bitRate<=200 && round($target->_frameRate)>=24) {
					$target->_frameRate = round($target->_frameRate/2,2);
				}
			}
		}

		/*
		 * MPEG2 constraint - target fps should be at least 20
		 */
		if($target->_id==KDLVideoTarget::MPEG2){
			$target->_frameRate = max(20,$target->_frameRate);
		}
		
		//Frame rate smaller than 1 causes Memory Fatal Error so in this case set it to 1
		//Changed the setting to force the frame rate from 1 to 5 since we noticed in some cases this causes mp4 h264 assets to played unusually like in a fast forward mode    
		if( $target->_frameRate > 0 &&  $target->_frameRate < 1)
			$target->_frameRate=5;
		
		return $target->_frameRate;
	}

	/* ---------------------------
	 * evaluateTargetAudio
	 */
	public function evaluateTargetAudio(KDLAudioData $source, KDLMediaDataSet $target)
	{
		$targetAud = clone $this->_audio;
		if($targetAud->_id=="" || $targetAud->_id==null) {
			if($target->_container!=null) {
				switch($target->_container->_id){
					case KDLContainerTarget::MP4:
					case KDLContainerTarget::_3GP:
						$targetAud->_id=KDLAudioTarget::AAC;
						break;
					case KDLContainerTarget::MP3:
						$targetAud->_id=KDLAudioTarget::MP3;
						break;
					case KDLContainerTarget::OGG:
					case KDLContainerTarget::OGV:
						$targetAud->_id=KDLAudioTarget::VORBIS;
						break;
					case KDLContainerTarget::FLV:
						$targetAud->_id=KDLAudioTarget::MP3;
						break;
					case KDLContainerTarget::WMV:
					case KDLContainerTarget::WMA:
					case KDLContainerTarget::ISMV:
						$targetAud->_id=KDLAudioTarget::WMA;
						break;
					case KDLContainerTarget::WEBM:
					case KDLContainerTarget::MPEGTS:
					case KDLContainerTarget::APPLEHTTP:
						$targetAud->_id=KDLAudioTarget::AAC;
						break;
				};
			}
			else if($target->_video!=null) {
				switch($target->_video->_id){
					case KDLVideoTarget::H264:
					case KDLVideoTarget::H264B:
					case KDLVideoTarget::H264M:
					case KDLVideoTarget::H264H:
						$targetAud->_id=KDLAudioTarget::AAC;
						break;
					case KDLVideoTarget::THEORA:
						$targetAud->_id=KDLAudioTarget::VORBIS;
						break;
					default:
						$targetAud->_id=KDLAudioTarget::MP3;
						break;
				}
			}
			else {
				$targetAud->_id=KDLAudioTarget::MP3;
			}
		}
		elseif ($target->_container->_id==KDLContainerTarget::MP3) {
			$targetAud->_id=KDLAudioTarget::MP3;
		}

				/* -------------
				 * Adjust target bit depth/resolution if it is set in the source
				 */
		if(!(isset($targetAud->_resolution) && $targetAud->_resolution>0) 
		&& isset($source->_resolution)){
			$targetAud->_resolution=$source->_resolution;
		}
				/* ---------------
				 * Channels (ch):
				 * - AMRNB: ch 1
				 * - MP3: if not defined - set 2, else keep the definition
				 * - else (ch defined): make it minimum between the source ch cnt 
				 * and the required ch cnt
				 */
		if ($targetAud->_id==KDLAudioTarget::AMRNB){
			$targetAud->_channels=1;
		}
		else if($targetAud->_channels==0 
		&& !($targetAud->_id==KDLAudioTarget::AAC || $targetAud->_id==KDLAudioTarget::PCM || $targetAud->_id==KDLAudioTarget::MPEG2)){
			$targetAud->_channels=KDLConstants::DefaultAudioChannels;
		}
		else {
			$targetAud->_channels=min($targetAud->_channels, $source->_channels);
		}

				/* ----------------
				 * Normalize sample rate - 
				 * - FLV/MP3: on auto get sr from the source. Follow the spec valid values - 11025,22050,44100.
				 * - AMRNB - br <=12.2, sr <= 8000
				 * - AAC or MP3/non flv: on auto use source sr (if available). Truncate to valid range(11025-48000) 
				 */
		if(($target->_container!=null && $target->_container->_id==KDLContainerTarget::FLV) 
		 && $targetAud->_id==KDLAudioTarget::MP3) {
			if($targetAud->_sampleRate==0 && $source->_sampleRate && $source->_sampleRate>0){
				$targetAud->_sampleRate=$source->_sampleRate;
			}
			$trgSr = $targetAud->_sampleRate;
			if($targetAud->_sampleRate>44100)
				$trgSr=44100;
			if($targetAud->_sampleRate<44100)
				$trgSr=22050;
			if($targetAud->_sampleRate<22050)
				$trgSr=11025;
			$targetAud->_sampleRate = $trgSr;
		}
		else if($targetAud->_id==KDLAudioTarget::AMRNB) { 
			if ($targetAud->_sampleRate==0 || $targetAud->_sampleRate>8000)
				$targetAud->_sampleRate=8000;
			if ($targetAud->_bitRate==0 || $targetAud->_bitRate>12.2)
				$targetAud->_bitRate=12.2;
		}
		else {
			if($targetAud->_sampleRate==0){
				/*
				 * AAC targets should get default 44.1, rather than source SR
				 */
				if($source->_sampleRate>0 && $targetAud->_id!=KDLAudioTarget::AAC) {
					$targetAud->_sampleRate=max(KDLConstants::MinAudioSampleRate,min(KDLConstants::MaxAudioSampleRate,$source->_sampleRate));
				}
				else {
					$targetAud->_sampleRate=KDLConstants::DefaultAudioSampleRate;
				}
			}
			else {
				$targetAud->_sampleRate=max(KDLConstants::MinAudioSampleRate,min(KDLConstants::MaxAudioSampleRate,$targetAud->_sampleRate));
			}
		}
		
			/*
			 * For following cases the audio should be resampled with ffmpeg 'aresample' filter
			 * - Nellimoser audio source
			 * - Low sample-rate audio (<16000hz)
			 * - target other than OGG/Vorbis
			 */
		if(!$target->_container->IsFormatOf(array(KDLContainerTarget::OGG,KDLContainerTarget::OGV))
		&& ($source->IsFormatOf(array('nellymoser'))||($source->_sampleRate && $source->_sampleRate>0 && $source->_sampleRate<16000))) {
			$targetAud->_useResampleFilter = true;
		}

		return $targetAud;
	}

	/* ---------------------------
	 * evaluateMultiStream
	 * 
	 */
	private static function evaluateMultiStream($source, $flavor) 
	{
/*
Supprted options
1.
{"audio":{"mapping":[["all"]]}}


2.
{"audio":{"mapping":[["all",1]]}}


3.
{"audio":{"mapping":[[7,0],[8,1]]}}

 */
		if(is_null($source) || is_null($flavor)){
			return null;
		}
		
			/*
			 * No multiple audio source streams ==> nothing to porcess
			 */
		if(!array_key_exists(KDLConstants::AudioIndex, $source)){
			return null;
		}
			/*
			 * No multiple audio mappings in the target ==> default porcessing
			 */
		$audioFieldName = KDLConstants::AudioIndex;
		if(!array_key_exists(KDLConstants::AudioIndex, $flavor) 
		|| is_null($flavor->$audioFieldName->mapping)
		|| !is_array($flavor->$audioFieldName->mapping)
		|| count($flavor->$audioFieldName->mapping)==0 ){
			return null;
		}
		$mapping=$flavor->$audioFieldName->mapping;

		$target = new stdClass();
		$target->$audioFieldName = new stdClass();
		foreach($mapping as $map){
			if($map[0]=='all') {
					/*
					 * When 'count($map)==1' (only the source member is assigned)
					 * - take ALL source streams and convert each of them separatly into an ouput multi-stream
					 * - else - take ALL source streams and MERGE them into ONE output stream
					 */ 
				foreach($source->$audioFieldName as $i=>$aud) {
					$target->$audioFieldName->mapping[($aud->streamId)] = count($map)==1? $aud->streamId: $map[1];
				}
			}
			else {
				$target->$audioFieldName->mapping[$map[0]] = count($map)==1? $map[0]: $map[1];				
			}
		}

					/*
					 * Currently there is no video mapping functionality. 
					 * Therefore if there is audio mapping and there are video source stream, 
					 * default video stream is added.  
					 */
		if(array_key_exists(KDLConstants::VideoIndex, $source)){
			$videoFieldName = KDLConstants::VideoIndex;
			$target->$videoFieldName = new stdClass();
			$sourceVideoStreams = $source->$videoFieldName;
			$target->$videoFieldName->mapping[$sourceVideoStreams[0]->streamId] = 0;
		}

		return $target;
	}

	/* ---------------------------
	 * validateTranscoders
	 * - Remove the engines that in the blacklist for that codec/format/etc
	 */
	private function validateTranscoders(KDLMediaDataSet $source, &$transcoders, $inSet=false)
	{
KalturaLog::log("==>\n");
		$cnt = count($transcoders);
		$i = 0;
		foreach($transcoders as $key=>$trPrm) {
			if(is_array($trPrm)){
				$cnt = count($trPrm);
				$this->validateTranscoders($source, $trPrm, true);
				if($cnt>count($trPrm)){
					unset($transcoders[$key]);
					$this->_warnings[KDLConstants::ContainerIndex][] = 
						KDLWarnings::ToString(KDLWarnings::RemovingMultilineTranscoding);
				}
			}
			else {
				if(is_null($transcoders[$key]->_engine)){
					$this->_warnings[KDLConstants::ContainerIndex][] = //"The transcoder (".$key.") can not process the (".$sourcePart->_id."/".$sourcePart->_format. ").";
								KDLWarnings::ToString(KDLWarnings::MissingTranscoderEngine, $transcoders[$key]->_id);
					unset($transcoders[$key]);
				}
				else {
					if($inSet){		
						KalturaLog::log(": inSet,cnt:$cnt,i:$i");
						if($i>0){
							$transcoders[$key]->_engine->set_sourceBlacklist(null);
						}
						if($i<$cnt-1){
							$transcoders[$key]->_engine->set_targetBlackList(null);
						}
					}
					$rv=$transcoders[$key]->_engine->CheckConstraints($source, $this, $this->_errors, $this->_warnings);
					if($rv==true){
						unset($transcoders[$key]);
					}
				}
			}
			$i++;
		}
		if(count($transcoders)){
			return true;
		}
		return false;
	}
	
	/* ---------------------------
	 * Blacklist processing
	 */
	private static function checkBlackList($blackList, $transcoder, $mediaSet)
	{
		if(array_key_exists($transcoder, $blackList)) {
			foreach ($blackList[$transcoder] as $keyPart => $subBlackList){
				$sourcePart = null;
				switch($keyPart){
				case KDLConstants::ContainerIndex;
					$sourcePart = $mediaSet->_container;
					break;
				case KDLConstants::VideoIndex;
					$sourcePart = $mediaSet->_video;
					break;
				case KDLConstants::AudioIndex;
					$sourcePart = $mediaSet->_audio;
					break;
				default:
					continue;
				}
				if($sourcePart && is_array($subBlackList)
				&& (in_array($sourcePart->_id, $subBlackList)
				|| in_array($sourcePart->_format, $subBlackList))) {
					return $sourcePart;
				}
			}
		}
		return null;
	}
}

#if 0
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
/* ===========================
 * KDLFlavor2Tags
 */
class KDLFlavor2Tags {
	static $ItunesFormats = array("mpeg-4","mpeg audio", "aiff", "wave");
	static $FlashFormats = array("flash video", "flv", "f4v","flash","flashvideo");
	static $FlashPlayableFormats = array("mpeg-4","mpeg audio");
	static $H264Synonyms = array("avc","avc1","h264","h.264");
	static $MP4ContainerSynonyms = array("mpeg-4", "mp4");

	/* ---------------------------
	 * ToTags
	 */

	public static function ToTags(KDLMediaDataSet $source, $tagsToCheck=null)
	{
		//		$aaa=KDLFlavor2Tags::$ItunesFormats;
		$tagsIn = array();
		$tagsOut = array();
		$flavor=null;
		if(is_array($tagsToCheck)) {
			if($tagsToCheck[0] instanceof KDLFlavor) {
				foreach($tagsToCheck as $tagToCheck) {
					$tagsOut = $tagsOut + KDLFlavor2Tags::ToTags($source, $tagToCheck);
					return $tagsOut;
				}
			}
			else {
				$tagsIn = $tagsToCheck;
			}
		}
		else if($tagsToCheck instanceof KDLFlavor) {
			$flavor = $tagsToCheck;
			if(is_array($tagsToCheck->_tags))
			$tagsIn = $tagsToCheck->_tags;
			else
			$tagsIn[0] = $tagsToCheck->_tags;
		}
		else {
			$tagsIn[0] = $tagsToCheck;
		}

		foreach($tagsIn as $tag) {
			switch($tag){
				case "web":
					if($source->_container->IsFormatOf(KDLFlavor2Tags::$FlashFormats))
					$tagsOut[] = $tag;
					else if(KDLFlavor2Tags::isMp4($source))
					$tagsOut[] = $tag;
					else if(KDLFlavor2Tags::isMpegAudio($source))
					$tagsOut[] = $tag;
					/*
					 else {
						if($source->_container->IsFormatOf(KDLFlavor2Tags::$FlashPlayableFormats)) {
						$audFormats = array("mpeg audio");
						if(($source->_video && $source->_video->IsFormatOf(KDLFlavor2Tags::$H264Synonyms))
						|| ($source->_audio && $source->_audio->IsFormatOf($audFormats))){
						$tagsOut[] = $tag;
						}
						}
						}
						*/
					break;
				case "itunes":
					if($source->_container->_id=="qt"
					|| $source->_container->IsFormatOf(KDLFlavor2Tags::$ItunesFormats))
					$tagsOut[] = $tag;
					break;
				case "mbr":
					if($flavor!=null && KDLFlavor2Tags::isMbr($source, $flavor))
					$tagsOut[] = $tag;
					break;
				default:
					break;
			}
		}

		return $tagsOut;
	}

	/* ---------------------------
	 * isMbr
	 */
	private static function isMbr(KDLMediaDataSet $source, KDLFlavor $flavor)
	{
		if($source->_container->IsFormatOf(KDLFlavor2Tags::$FlashFormats)
		&& $flavor->_container->IsFormatOf(KDLFlavor2Tags::$FlashFormats)) {
			;
		}
		else
		if(KDLFlavor2Tags::isMp4($source) && KDLFlavor2Tags::isMp4($flavor)) {
			;
		}
		else {
			return false;
		}
		/*

		if(!(($source->_container->IsFormatOf(KDLFlavor2Tags::$FlashFormats) && $flavor->_container->IsFormatOf(KDLFlavor2Tags::$FlashFormats))
		|| ($source->_container->IsFormatOf(array("mpeg-4")) && $source->_video->IsFormatOf(KDLFlavor2Tags::$H264Synonyms))
		) ) )
		return false;
		*/
		return true;
		return false;
	}

	/* ---------------------------
	 * isMp4
	 */
	private static function isMp4(KDLMediaDataSet $media, $doVideoCheck=true)
	{
		if($media->_container->IsFormatOf(KDLFlavor2Tags::$MP4ContainerSynonyms)
		&&($media->_video==null || $media->_video->IsFormatOf(KDLFlavor2Tags::$H264Synonyms))
		&&($media->_audio==null || $media->_audio->IsFormatOf(array("mpeg audio", "mp3","aac")))
		){
			return true;
		}
		return false;
	}

	/* ---------------------------
	 * isMpegAudio
	 */
	private static function isMpegAudio(KDLMediaDataSet $media)
	{
		if($media->_container->IsFormatOf(array("mpeg audio", "mp3"))
		&& $media->_video!=null
		&& $media->_video->IsFormatOf(array("mpeg audio", "mp3"))){
			return true;
		}
		return false;
	}

}
#endif

?>