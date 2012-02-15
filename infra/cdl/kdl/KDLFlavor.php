<?php
include_once("KDLCommon.php");
include_once("KDLMediaDataSet.php");

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
			if($this->_video->_bitRate/$prevFlavor->_video->_bitRate>KDLConstants::FlavorBitrateRedundencyFactor) {
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
			if($target->_video->_gop===null || $target->_video->_gop==0)
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
//kLog::log(__METHOD__."==>\n".print_r($target->_transcoders,true));
		}
		$this->generateCommandLines($target, $target->_transcoders);

		return $target;
	}

	/* ---------------------------
	 * generateCommandLines
	 */
	private function generateCommandLines(KDLFlavor $target, $transcoders){

		if(isset($target->_video)) {
			if(!property_exists($target->_video,"_h264ForMobile")) {
				if(isset($target->_engineVersion) && $target->_engineVersion==1){
					$target->_video->_h264ForMobile = 1;
				}
				else {
					$target->_video->_h264ForMobile = 0;
				}
			}
		}
		foreach($transcoders as $key=>$trPrmObj) {

			if(is_array($trPrmObj)){
				$this->generateOperationSetCommandLines($target, $trPrmObj);
			}
			else{
//				$transcoders[$key]->_cmd = $trPrmObj->_engine->GenerateCommandLine($this, $target, $trPrmObj->_extra);
//				$transcoders[$key]->_cfg = $trPrmObj->_engine->GenerateConfigData($this, $target);
				$transcoders[$key] = $trPrmObj->GenerateCommandAndConfig($this, $target);
			}
		}
	}
	
	/* ---------------------------
	 * generateOperationSetCommandLines
	 */
	private function generateOperationSetCommandLines(KDLFlavor $target, $transcoders){
KalturaLog::log(__METHOD__."==>\n");
		
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
		KalturaLog::log( ".TRG-->".$this->ToString());
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
					|| $prdVid->_duration>$plannedDur*KDLSanityLimits::MaxDurationFactor) {
						$product->_errors[KDLConstants::VideoIndex][] = // Invalid product duration
							KDLErrors::ToString(KDLErrors::InvalidDuration, $prdVid->_duration/1000, $plannedDur/1000);
						$rv=false;
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
			$target->_clipDur=0;
		}
		
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
			/*
			 	$target->_flags = $this->_flags | self::MissingContentNonComplyFlagBit;
				$target->_warnings[KDLConstants::VideoIndex][] = // "The target flavor bitrate {".$target->_video->_bitRate."} does not comply with the requested bitrate (".$this->_video->_bitRate.").";
				KDLWarnings::ToString(KDLWarnings::MissingMediaStream);
			*/
			}
		}

		$target->_audio = null;
		if($this->_audio!=""){
			if($source->_audio!=""){
				$target->_audio = $this->evaluateTargetAudio($source->_audio, $target);
			}
			/*
			 else {
				$target->_flags = $this->_flags | self::MissingContentNonComplyFlagBit;
				$target->_warnings[KDLConstants::AudioIndex][] = // "The target flavor bitrate {".$target->_video->_bitRate."} does not comply with the requested bitrate (".$this->_video->_bitRate.").";
				KDLWarnings::ToString(KDLWarnings::MissingMediaStream);
				}
				*/
		}
		return $target;
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
		 * If flavor BR is higher than the source - keep the source BR
		 */
		$this->evaluateTargetVideoBitrate($sourceVid, $targetVid);

		/*
		 * If the flavor fps is zero, evaluate it from the source and
		 * the constants theshold.
		 */
		if($flavorVid->_frameRate==0) {
			$targetVid->_frameRate = $sourceVid->_frameRate;
			if($targetVid->_frameRate>KDLConstants::MaxFramerate) {
				$targetVid->_warnings[KDLConstants::VideoIndex][] =
				KDLWarnings::ToString(KDLWarnings::TruncatingFramerate, KDLConstants::MaxFramerate, $targetVid->_frameRate);
				$targetVid->_frameRate=KDLConstants::MaxFramerate;
			}
			// For webcam/h263 - if FR==0, set FR=24
			else if($targetVid->_frameRate==0 && $sourceVid->IsFormatOf(array("h.263")) ){
				$targetVid->_frameRate=24;
			}
		}

		if($flavorVid->_gop===null || $flavorVid->_gop==0) {
			$targetVid->_gop = KDLConstants::DefaultGOP;
		}

		$targetVid->_rotation = $sourceVid->_rotation;
		$targetVid->_scanType = $sourceVid->_scanType;
		
		return $targetVid;
	}

	/* ---------------------------
	 * evaluateTargetVideoFramesize
	 */
	private static function evaluateTargetVideoFramesize(KDLVideoData $source, KDLVideoData $target) {

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
			if($target->_width>$widSrc) {
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
			if($target->_height>$hgtSrc) {
				$target->_height = $hgtSrc;
				$target->_width  = $widSrc;
			}
		}
			/*
			 * Dual dimension 'keep aspect ratio' mode:
			 * Scale down the source to match inside the flavor params 
			 * predefined frame size while keeping source the aspect ratio
			 */
		else if(isset($target->_arProcessingMode) && $target->_arProcessingMode==1){
			$darTrgFrame = $target->_width/$target->_height;
				/*
				 * The target AR is wider than the source
				 */
			if($darTrgFrame>$darSrcFrame){
				$target->_width = $target->_height*$darSrcFrame;
				if($target->_width>$widSrc) {
					$target->_height = $hgtSrc;
					$target->_width  = $widSrc;
				}
			}
				/*
				 * The target AR is narrower than the source
				 */
			else {
				$target->_height = $target->_width/$darSrcFrame;
				if($target->_height>$hgtSrc) {
					$target->_height = $hgtSrc;
					$target->_width  = $widSrc;
				}
			}
		}
			/*
			 * Fixed target frame size
			 */
		else {
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
		 * x16 - make sure both hgt/wid comply to
		 */
		if(!isset($target->_forceMult16) || $target->_forceMult16==1) {
			$target->_height = $target->_height -($target->_height%16);
			$target->_width  = $target->_width  -($target->_width%16);
		}
	}

	/* ---------------------------
	 * evaluateTargetVideoBitrate
	 */
	//bitrate calc should take in account source frame size(heightXwidth), relativly to the flavor/target frame size.
	//therefore the Evaluate frame sze should be called before this func
	private static function evaluateTargetVideoBitrate(KDLVideoData $source, KDLVideoData $target) 
	{
		$ratioFlvr = KDLConstants::BitrateVP6Factor;
		if(in_array($target->_id, KDLConstants::$BitrateFactorCategory1))
			$ratioFlvr = KDLConstants::BitrateH263Factor;
		else if(in_array($target->_id, KDLConstants::$BitrateFactorCategory2))
			$ratioFlvr = KDLConstants::BitrateVP6Factor;
		else if(in_array($target->_id, KDLConstants::$BitrateFactorCategory3))
			$ratioFlvr = KDLConstants::BitrateH264Factor;

		$ratioSrc = KDLConstants::BitrateOthersRatio;
		if(in_array($source->_id, KDLConstants::$BitrateFactorCategory1))
			$ratioSrc = KDLConstants::BitrateH263Factor;
		else if(in_array($source->_id, KDLConstants::$BitrateFactorCategory2))
			$ratioSrc = KDLConstants::BitrateVP6Factor;
		else if(in_array($source->_id, KDLConstants::$BitrateFactorCategory3))
			$ratioSrc = KDLConstants::BitrateH264Factor;
			
		$brSrcNorm = $source->_bitRate*($ratioSrc/$ratioFlvr);
		if($target->_bitRate>$brSrcNorm){
			$target->_bitRate = $brSrcNorm;
		}
		return $target->_bitRate = round($target->_bitRate, 0);
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
		else if($targetAud->_channels==0 && $targetAud->_id!=KDLAudioTarget::AAC){
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
				if($source->_sampleRate>0) {
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

		return $targetAud;
	}

	/* ---------------------------
	 * validateTranscoders
	 * - Remove the engines that in the blacklist for that codec/format/etc
	 */
	private function validateTranscoders(KDLMediaDataSet $source, &$transcoders, $inSet=false){

KalturaLog::log(__METHOD__."==>\n");
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
						KalturaLog::log(__METHOD__.": inSet,cnt:$cnt,i:$i");
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
	 * SetTranscoderCmdLineGenerator
	 */
	public function SetTranscoderCmdLineGenerator($inFile=KDLCmdlinePlaceholders::InFileName, $outFile=KDLCmdlinePlaceholders::OutFileName)
	{
		$cmdLine = new KDLTranscoderCommand($inFile,$outFile, $this);

/*
		if($this->_video){
			$cmdLine->_vidId = $this->_video->_id;
			$cmdLine->_vidBr = $this->_video->_bitRate;
			$cmdLine->_vidWid = $this->_video->_width;
			$cmdLine->_vidHgt = $this->_video->_height;
			$cmdLine->_vidFr = $this->_video->_frameRate;
			$cmdLine->_vidGop = $this->_video->_gop;
			$cmdLine->_vid2pass = $this->_isTwoPass;
			$cmdLine->_vidRotation = $this->_video->_rotation;
			$cmdLine->_vidScanType = $this->_video->_scanType;
		}
		else
			$cmdLine->_vidId="none";
			
		if($this->_audio){
			$cmdLine->_audId = $this->_audio->_id;
			$cmdLine->_audBr = $this->_audio->_bitRate;
			$cmdLine->_audCh = $this->_audio->_channels;
			$cmdLine->_audSr = $this->_audio->_sampleRate;
		}
		else
		$cmdLine->_audId="none";
			
		if($this->_container){
			$cmdLine->_conId = $this->_container->_id;
		}
		else
		$cmdLine->_conId="none";
*/
		return $cmdLine;
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