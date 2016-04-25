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
	const FrameSizeNonComplyFlagBit = 16;
	
	const ENCRYPTION_KEY_PLACEHOLDER = "__ENCRYPTION_KEY__";
	const ENCRYPTION_KEY_ID_PLACEHOLDER = "__ENCRYPTION_KEY_ID__";

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
	
	public $_optimizationPolicy = KDLOptimizationPolicy::BitrateFlagBit;
	
	public $_isEncrypted = false; // CENC encryption
	
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

		/*
		 * Allow conversion and fixing of invalidly muxed WEB-CAM recordecd files - 
		 * - FLV/Sorenson/Nellimossr
		 * - very HUGE duration
		 * - very LOW bitrate - about several bits-per-sec.
		 * In such cases the 'duration validation' is un-applicable
		 *
		if(isset($srcVid) && $srcVid->IsFormatOf(array("h.263","h263","sorenson spark","vp6")) 
		&& isset($srcAud) && $srcAud->IsFormatOf(array('nellymoser')) && $cDur>0 && isset($srcCont->_fileSize)){
			if($srcCont->_fileSize*8000/$cDur<KDLSanityLimits::MinBitrate) {
				KalturaLog::log("Invalid WEB-CAM source file. Duration validation is un-applicable");
				return true;
			}
		}
		*/
		if($this->_video!==null) {
			if($product->_video===null){
				$product->_errors[KDLConstants::VideoIndex][] = KDLErrors::ToString(KDLErrors::MissingMediaStream);
				$rv=false;
			}
			else {
				$prdVid = $product->_video;
				$trgVid = $this->_video;

					/*
					 *  On short durations, the 'granulariity' of a single frame dur might cause invalidation. 
					 *  Don't check for <2sec
					 */
				if($plannedDur>2000){
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

					/*
					 * On short durations, the 'granulariity' of a single frame dur might cause invalidation.
					 * Don't check for <2sec
					 */
				if($plannedDur>2000){ 
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
			   ||($this->_flags & KDLFlavor::FrameSizeNonComplyFlagBit)
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
			 * Fading needs explicit time limitation on the WM image loop. 
			 * We'll do it with '_explicitClipDur' field.
			 * 
			 * Check each WM data object for multiple-WM mode
			 */
		if(!isset($target->_explicitClipDur) && isset($target->_video) && isset($target->_video->_watermarkData)){
			if(is_array($target->_video->_watermarkData))
				$watermarkDataArr = $target->_video->_watermarkData;
			else
				$watermarkDataArr = array($target->_video->_watermarkData);
			foreach($watermarkDataArr as $watermarkData){
				if(isset($watermarkData->fade)){
					$target->_explicitClipDur = $sourceDur;
					break;
				}
			}
		}

			/*
			 * mpeg2 and theora video formats does not allow reliable 'fastSeekTo' (used on clipping)
			 */
		if($source->_video && $source->_video->IsFormatOf(array("mpeg video","theora"))){
			$target->_fastSeekTo = false;
		}
		else {
			$target->_fastSeekTo = true;
		}
		
			/*
			 * For IMX sources, apply cropping of the top 32 pixs, if the flavor has the ImxCrop flag
			 * 'IMX' ==> mxf/mpeg2 video/ 720x608
			 */
		if(isset($this->_video)){
			if($this->_video->_isCropIMX==true
			&& isset($source->_container) && $source->_container->IsFormatOf(array("mxf")) 
			&& isset($source->_video) && $source->_video->IsFormatOf(array("mpeg video","mpeg2video")) 
			&& isset($source->_video->_width) && $source->_video->_width==720
			&& isset($source->_video->_height) && ($source->_video->_height==608 || $source->_video->_height==576 || $source->_video->_height==486)){
				$this->_video->_isCropIMX=true;
			}
			else {
				$this->_video->_isCropIMX=false;
			}
			KalturaLog::log('IsCropImx('.$this->_video->_isCropIMX.')');
		}
			/*
			 * Analyse the source to determine whether it contains multi-stream audio.
			 * In case it does and the flavor has 'multiStream' set to 'auto-detect' (default action) -
			 * try to define a multiStream processing setup
			 */
		if (isset($source->_contentStreams)){
		    $sourceAnalize = self::analizeSourceContentStreams($source->_contentStreams);
			    /*
			    * Check analyze realts for
			    * - 'streamsAsChannels' - process them as sorround streams
			    * - 'languages - process them as multi-lingual
			    * - otherwise remove the 'multiStream' object'
			    */
		    if(isset($sourceAnalize->streamsAsChannels)){
			    $target->_multiStream = self::sorroundAudioSurceToTarget($source, $target->_multiStream, $sourceAnalize->streamsAsChannels);
		    }
		    else if(isset($sourceAnalize->languages)){
			    $target->_multiStream = self::multiLingualAudioSurceToTarget($source, $target->_multiStream, $sourceAnalize->languages);
		    }
		    else {
			    $target->_multiStream = null;
		    }
		}

		if($target->_container->_id==KDLContainerTarget::COPY){
			$target->_container->_id=self::EvaluateCopyContainer($source->_container);
		}

		$target->_container->_duration = $sourceDur;
		$target->_video = null;
		if($this->_video!="") {
			if($source->_video!="" && ($target->_container && !($target->_container->_id==KDLContainerTarget::MP3 || $target->_container->_id==KDLContainerTarget::WMA))){
				/*
				 * Evaluate target video params
				 */
				$target->_video = $this->evaluateTargetVideo($source->_video);
				
					/*
					 * Apply optimization-policy to evaluate 'compliancy' state - 
					 * if not set - use original BitRate oriented optimization
					 */
				if(isset($this->_optimizationPolicy))
					$optimizationPolicy = $this->_optimizationPolicy;
				else
					$optimizationPolicy = KDLOptimizationPolicy::BitrateFlagBit;
				KalturaLog::log('OptimizationPolicy('.$target->_optimizationPolicy.')');
				
					/*
					 * Bitrate oriented optimization -
					 * NonCompliant if the source bitrate significantly lower than the flavor bitrate
					 */
				if($optimizationPolicy & KDLOptimizationPolicy::BitrateFlagBit) {
					if($target->_video->_bitRate<$this->_video->_bitRate*KDLConstants::FlavorBitrateComplianceFactor) {
						$target->_flags = $this->_flags | self::BitrateNonComplyFlagBit;
						$target->_warnings[KDLConstants::VideoIndex][] = 
							KDLWarnings::ToString(KDLWarnings::TargetBitrateNotComply, $target->_video->_bitRate, $this->_video->_bitRate);
					}
				}
					/*
					 * Frame size oriented optimization -
					 * NonCompliant if the source frame size significantly smaller than the flavor frame size
					 */
				if($optimizationPolicy & KDLOptimizationPolicy::FrameSizeFlagBit){
					$srcVid = $source->_video;
					$trgVid = $target->_video;
					$flvrVid= $this->_video;
					$param1=null;
					$param2=null;
					/*
					 * The BitrateCompliance condition prevented some of flavors to be signed as  'Framesize-non-comply'.
					 * Therefore it was removed.
					 */
		//			if(isset($flvrVid->_bitRate) && $flvrVid->_bitRate>0 && isset($srcVid->_bitRate) && $srcVid->_bitRate>0
		//			&& $flvrVid->_bitRate/KDLConstants::FlavorBitrateComplianceFactor<$srcVid->_bitRate) 
					{
						if(isset($flvrVid->_width) && $flvrVid->_width>0 && isset($trgVid->_width) && $trgVid->_width 
						&& $flvrVid->_width>$trgVid->_width/KDLConstants::FlavorFrameSizeComplianceFactor) {
							$target->_flags = $this->_flags | self::FrameSizeNonComplyFlagBit;
							$param1 = "w:$flvrVid->_width";
							$param2 = "w:$trgVid->_width";
						}
						if(isset($flvrVid->_height) && $flvrVid->_height>0 && isset($trgVid->_height) && $trgVid->_height 
						&& $flvrVid->_height>$trgVid->_height/KDLConstants::FlavorFrameSizeComplianceFactor) {
							if(isset($param1)) { $param1.=","; $param2.=","; }
							$param1.= "h:$flvrVid->_height";
							$param2.= "h:$trgVid->_height";
						}
					}
					if(isset($param1)){
						$target->_flags = $this->_flags | self::FrameSizeNonComplyFlagBit;
						$target->_warnings[KDLConstants::VideoIndex][] = 
							KDLWarnings::ToString(KDLWarnings::TargetFrameSizeNotComply, $param1, $param2);
					}
				}
			}
			else if($target->_container && $target->_container->_id==KDLContainerTarget::ISMV) {
					/*
					 * EE cannot generate audio only ISMV, therefore switch to WMA
					 */
				foreach ($this->_transcoders as $trns){
					$rv = strstr($trns->_id,"expressionEncoder.ExpressionEncoder");
					if($rv!=false) {
						$target->_warnings[KDLConstants::ContainerIndex][] = // "The target flavor bitrate {".$target->_video->_bitRate."} does not comply with the requested bitrate (".$this->_video->_bitRate.").";
							KDLWarnings::ToString(KDLWarnings::ChangingFormt, $target->_container->_id, KDLContainerTarget::WMA);
						$target->_container->_id=KDLContainerTarget::WMA;
						break;
					}
				}
			}
		}

		$target->_audio = null;
		if($this->_audio!=""){
			if($source->_audio!=""){
				if (isset($source->_contentStreams)){
					$target->_audio = $this->evaluateTargetAudio($source->_audio, $target, $source->_contentStreams);
				}else{
					$target->_audio = $this->evaluateTargetAudio($source->_audio, $target, null);
				}
				/*
				 * On multi-lingual flavor, 
				 * if required language does not exist - set NonComply flag 
				 */
				if(isset($target->_multiStream) && isset($target->_multiStream->audio) 
				&& isset($target->_multiStream->audio->languages) && count($target->_multiStream->audio->languages)==0){
					$target->_flags = $this->_flags | self::MissingContentNonComplyFlagBit;
					$target->_warnings[KDLConstants::AudioIndex][] = 
						KDLWarnings::ToString(KDLWarnings::MissingMediaStream);
				}
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
			case 'mxf':
				$format = KDLContainerTarget::MXF;
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
				case KDLContainerTarget::M4V:
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
				case KDLContainerTarget::M2TS:
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
		 * COPY does not require following settings
		 */
		if($targetVid->_id==KDLVideoTarget::COPY) {
			$targetVid->_watermarkData = null;
			return $targetVid;
		}
		
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

			/*
			 * Watermarks, if any ...
			 */
		self::evaluateTargetWaterMark($sourceVid, $targetVid);
		
		$targetVid->_rotation = $sourceVid->_rotation;
		$targetVid->_scanType = $sourceVid->_scanType;
		
		return $targetVid;
	}
	
	/*
	 *	switch frame sizes & inverse display aspect ratio for a certain video.
	 */
	
	private static function invertVideoDimensions(KDLVideoData $video)
	{
		$temp = $video->_height;
		$video->_height = $video->_width;
		$video->_width = $temp;
		if (isset($video->_dar) && $video->_dar != 0)
			$video->_dar = 1/$video->_dar;
	}
	
	/* ---------------------------
	 * evaluateTargetVideoFramesize
	 */
	private function evaluateTargetVideoFramesize(KDLVideoData $source, KDLVideoData $target) 
	{
		$shrinkToSource = $target->_isShrinkFramesizeToSource;
		$invertedVideo = false;
		
		
		/*
		 *	this is for the special case where a source has height > width.
		 *	here it will be inverted & run through the usual flow.
		 *	in this case the source-target frame-sizes ratio after converting should be the same as if the source had a regular height < width.
		 *	boolean flag invertedVideo - for inverting back the source & target later on.
		 */
		if ((isset($source->_dar) && $source->_dar < 1) ||
			(isset($source->_height) && isset($source->_width) && $source->_height > 0 && $source->_width > 0 && $source->_height > $source->_width))
		{
			KalturaLog::debug('inverting source');
			self::invertVideoDimensions($source);
			$invertedVideo = true;
		}
		
		$widSrc = $source->_width;
		$hgtSrc = $source->_height;
		if($widSrc==0 || $hgtSrc==0)
			return;
			
			/*
			 * For IMX - reduce the height by 32 pixs
			 */
		if(isset($target->_isCropIMX) && $target->_isCropIMX==true) {
			$hgtSrc-=32;
		}
		
		$darSrcFrame = $widSrc/$hgtSrc;
		/*
		 * DAR adjustment
		 */
		if($source->_dar!="" && $source->_dar>0){
			$darSrc = $source->_dar;
			$diff = abs(1-$darSrc/$darSrcFrame);
				// Less strict diff (original was diff>0.1) test to allow hadling of 5:4 to 4:3 adjustments
			if($diff>0.05) {  
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
			$darTrg = $target->_width/$target->_height;
			if($target->_height>$hgtSrc) {
				$target->_height=$hgtSrc;
			}
				/*
				 * If the target AR is similar/close (up to 10%) to the src AR,
				 * just trim to the source dims.
				 * Otherwise (src AR != trg AR) - calc the trg wid from trg AR and hgt.
				 */
			if(abs(1-$darTrg/$darSrcFrame)<0.1) {
				if($target->_width>$widSrc) {
					$target->_width=$widSrc;
				}
			}
			else {
				$target->_width = $target->_height*$darTrg;
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
		 * - for h264 targets force MOD 2 for width & height, otherwise x264 crashes.
		 */
		$modVal = 16;
		if((isset($target->_forceMult16) && $target->_forceMult16 == 0)
		|| (($target->_width == 640 || $target->_width == 480) && $target->_height == 360) || ($target->_width == 1920 && $target->_height == 1080)){
			$h264targets = array(KDLVideoTarget::H264, KDLVideoTarget::H264B, KDLVideoTarget::H264M, KDLVideoTarget::H264H);
			if(in_array($target->_id, $h264targets)) {
				$modVal = 2;
		}
		else {
				return;
			}
		}

		self::matchBestModConstrainedVideoFramesize($darSrcFrame, $hgtSrc, $widSrc, $modVal, $target);
		
		/*
		 *      inverting source back for conversion process.
		 *      inverting target back so the output will be inverted as well.
		 */
		if ($invertedVideo)
		{
			KalturaLog::debug('inverting back source & target');
			
			self::invertVideoDimensions($source);
			self::invertVideoDimensions($target);
		}
	}
	
	/* ---------------------------
	 * matchBestModConstrainedVideoFramesize
	 *  The goal is to conform with frame-size 'mod' constraint (mostly mod16) 
	 *  while attempting to match as close as possible the required aspect ratio - 
	 *  - Evaluating the all 4 possible setups (mod-up/mod-down for vid/hgt)
	 *  - Compare each of them to the required AR
	 *  - Find the setup that is closest 
	 */
	protected function matchBestModConstrainedVideoFramesize($darSrcFrame, $hgtSrc, $widSrc, $modVal, KDLVideoData $target) 
	{ 
			/*
			 * Calculate hgt & wid 'mod down' value. If not set - assign 0 
			 */
		$h_dw = ($target->_height>0)? $target->_height - ($target->_height%$modVal): 0;
		$w_dw = ($target->_width>0)? $target->_width - ($target->_width%$modVal): 0;
		
			/*
			 * If 'mod-down' vals equal to original trg val 
			 * ==> leave, further calcs are redundant 
			 * If one of 'mod-down' ==0
			 * ==> assign and leave, further calcs are redundant
			 */
		if($target->_height==$h_dw && $target->_width==$w_dw){
			return;
		}
		else if($h_dw==0 || $w_dw==0){
			$target->_width  = $w_dw;
			$target->_height = $h_dw;
			return;
		}

			/*
			 * Calc 'mod-up' values
			 * Make sure not to exceed the source dims 
			 * and original flavor dims
			 */
		$h_up = $target->_height -($target->_height%$modVal) + $modVal;
		if($h_up>$hgtSrc || ($this->_video->_height>0 && $h_up>$this->_video->_height)) {
			$h_up = $h_dw;
		}
		$w_up = $target->_width  -($target->_width%$modVal) + $modVal;
		if($w_up>$widSrc || ($this->_video->_width>0 && $w_up>$this->_video->_width)) {
			$w_up = $w_dw;
		}
		
			/*
			 * Calc difference between source AR and AR's of various mod-up/down cases.
			 * The target is to find the option that is closest to the source AR.
			 * Array keys notation - 'd' for 'down', 'u' for 'up'
			 */
		$arArr["dd"] = abs($darSrcFrame-$w_dw/$h_dw);
		$arArr["du"] = abs($darSrcFrame-$w_dw/$h_up);
		$arArr["ud"] = abs($darSrcFrame-$w_up/$h_dw);
		$arArr["uu"] = abs($darSrcFrame-$w_up/$h_up);
		
			/*
			 * Sort the array with AR-diffs to find the smallest (closest to source AR)
			 */
		asort($arArr);
		$kAr = key($arArr);
		
			/*
			 * Assign the best match to target dims.
			 */
		switch ($kAr){
		case "dd":
			$target->_width  = $w_dw;
			$target->_height = $h_dw;
			break;
		case "du":
			$target->_width  = $w_dw;
			$target->_height = $h_up;
			break;
		case "ud":
			$target->_width  = $w_up;
			$target->_height = $h_dw;
			break;
		case "uu":
			$target->_width  = $w_up;
			$target->_height = $h_up;
			break;
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

	/**
	 * evaluateTargetWaterMark
	 * Evaluate scale value in case of 'percentage-of-the-source'
	 * Sample 'scale' value "x30%" stands for - 
	 * make the height to be 30% of the source, calculate the width to match the height
	 * 
	 * @param KDLVideoData $target
	 * @param KDLVideoData $target
	 */
	private static function evaluateTargetWaterMark(KDLVideoData $sourceVid, KDLVideoData $target) 
	{
		if(!isset($target->_watermarkData))
			return;
		
		/*
		 * Handle multiple WM settings - WMdata array
		 */
		if(is_array($target->_watermarkData))
			$watermarkDataArr = $target->_watermarkData;
		else
			$watermarkDataArr = array($target->_watermarkData);
		foreach($watermarkDataArr as $wmI=>$watermarkData){
			if(isset($watermarkData->scale)){
				$scaleArrNew = array();
				$scaleArr = explode("x",$watermarkData->scale);
				foreach ($scaleArr as $i=>$val){
					if(isset($val) && strlen($val)>0) {
						$percentArr = explode('%', $val);
						if(count($percentArr)==2){
							if(isset($sourceVid->_width) && isset($sourceVid->_height)) {
								// For 'portrait' sources (rotation -90,90,270) - switch the scaled dims
								if(isset($sourceVid->_rotation) && in_array($sourceVid->_rotation, array(-90,90,270)))
									$val =($i==0?$sourceVid->_height: $sourceVid->_width);
								else
									$val =($i==0?$sourceVid->_width: $sourceVid->_height);
								$val = round($val*$percentArr[0]/100);
							}
							else $val = "";
						}
					}
					$scaleArrNew[$i] = $val;
				}
				$watermarkData->scale = implode('x', $scaleArrNew);
			}
			$watermarkDataArr[$wmI] = $watermarkData;
		}
		$target->_watermarkData = $watermarkDataArr;
	}
	
	/* ---------------------------
	 * evaluateTargetAudio
	 */
	public function evaluateTargetAudio(KDLAudioData $source, KDLMediaDataSet $target, $contentStreams=null)
	{
		/*
		 * Adjust source channnels count to match the mapping settings
		 */
		if (isset($target->_multiStream)){
			$multiStream = $target->_multiStream;
		}else{
			$multiStream = null;
		}
		$multiStreamChannels = null;
		if(isset($multiStream->audio->mapping) && count($multiStream->audio->mapping)>0) {
			if(count($multiStream->audio->mapping)>1){
				$multiStreamChannels = 2;
			}
			else if(isset($contentStreams) && isset($contentStreams->audio)){
				$streams = $contentStreams->audio;
				foreach ($streams as $stream){
					if($stream->id==$multiStream->audio->mapping[0]){
						$multiStreamChannels = $stream->audioChannels;
						break;
					}
				}
			}
		}
		
		$targetAud = clone $this->_audio;
		if($targetAud->_id=="" || $targetAud->_id==null) {
			if($target->_container!=null) {
				switch($target->_container->_id){
					case KDLContainerTarget::MP4:
					case KDLContainerTarget::M4V:
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
					case KDLContainerTarget::M2TS:
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

			/*
			 * For MP3 w/out target bitrate - use 64Kb as default
			 */
		if(isset($target->_container) && $target->_container->_id==KDLContainerTarget::MP3
				&& $targetAud->_id==KDLAudioTarget::MP3 && $targetAud->_bitRate==0) {
			$targetAud->_bitRate = 64;
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
		else if(isset($multiStreamChannels)){
			$targetAud->_channels=$multiStreamChannels;
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
			 * DO-NOT try to resample on 'copy' cases - it can not be done
			 */
		if(!$target->_container->IsFormatOf(array(KDLContainerTarget::OGG,KDLContainerTarget::OGV))
		&& !$targetAud->IsFormatOf(array(KDLAudioTarget::COPY))
		&& ($source->IsFormatOf(array('nellymoser'))||($source->_sampleRate && $source->_sampleRate>0 && $source->_sampleRate<16000))) {
			$targetAud->_useResampleFilter = true;
		}
			/*
			 * Check for 'down' mix audio, it requires special ffmpeg processing 
			 */
		if(isset($contentStreams) && isset($contentStreams->audio) && count($contentStreams->audio)==1 
		&& isset($contentStreams->audio[0]->audioChannelLayout)
		&& $contentStreams->audio[0]->audioChannelLayout==KDLAudioLayouts::DOWNMIX){
			$targetAud->_downmix = true;
		}


			/*
			 * Check for 'down' mix audio, it requires special ffmpeg processing 
			 */
		if(isset($contentStreams) && isset($contentStreams->audio) && count($contentStreams->audio)==1 
		&& isset($contentStreams->audio[0]->audioChannelLayout)
		&& $contentStreams->audio[0]->audioChannelLayout==KDLAudioLayouts::DOWNMIX){
			$targetAud->_downmix = true;
		}

		return $targetAud;
	}

	/**
	 * 
	 * @param unknown_type $contentStreams
	 */
	private static function analizeSourceContentStreams($contentStreams)
	{
		$rvAnalize = new stdClass();
		
			/*
			 * Evaluate stream duration differences
			 * - calc average duration of each stream type (vid, aud,...)
			 * - calc delta between the avg and every stream dur (per type - aud, vid,...)
			 * - evaluate which streams have dur identical to the avg, which one is diff, and which one is zeroed
			 * - store in array for final part of anaylize logic
			 */
		$dursAccm = array();
		foreach($contentStreams as $t=>$streams) {
			foreach($streams as $stream){
				if(!array_key_exists($t, $dursAccm))
					$dursAccm[$t] = 0;
				$fld = $t."Duration";
				$dursAccm[$t] += isset($stream->$fld)?$stream->$fld:0;
			}
		}
		foreach($dursAccm as $t=>$accm){
			$dursAvg[$t] = $accm>0?$accm/count($contentStreams->$t):0;
		}

		$identicalDur = array();
		$zeroedDur = array();
		$differentDur = array();
		foreach($contentStreams as $t=>$streams) {
			foreach($streams as $stream){
				$fld = $t."Duration";
				$dur = $stream->$fld; //audoDuration or videoDuration or dataDuration
				if($dur==0) {
					$zeroedDur[$t][] = $stream;
					continue;
				}
				
				$dlt = abs($dursAvg[$t]-$dur);
					// Identical concidered to be less than 1sec delta and the delts is less than 0.1% 
				if($dlt<1000 && $dlt/$dur<0.001)
					$identicalDur[$t][] = $stream;
				else
					$differentDur[$t][] = $stream;
				
			}
		}
		
			/*
			 * For audio streams - 
			 * Check for 'streamAsChannel' case and for 'multilangual' case
			 * 'streamAsChannel' considerd to be if there are more than 1 mono streams.
			 */
		if(array_key_exists('audio', $identicalDur) && count($identicalDur['audio'])>1){
				// Get all streams that have 'surround' like audio layout - FR, FL, ...
			$channelStreams = KDLAudioLayouts::matchLayouts($identicalDur['audio']);
				// Sort the audio streams for chunnel number. We are looking for mono streams
			$chnNumStreams = array();
			foreach ($channelStreams as $stream){
				if(isset($stream->audioChannels))
					$chnNumStreams[$stream->audioChannels][] = $stream;
			}
				
				/*
				 * The streams that might be used for merging are only mono streams
				 * otherwise - no streamAsChannel
				 */ 
			if(array_key_exists(1, $chnNumStreams) && count($chnNumStreams[1])>1){
				$channelStreams = $chnNumStreams[1];
			}
			else {
				$channelStreams = array();
			}
			
				/* 
				 * Check for multi-langual case
				 * Sort the streams according to stream language
				 */
			$langStreams = array();
			foreach ($identicalDur['audio'] as $stream){
				if(isset($stream->audioLanguage))
					$langStreams[$stream->audioLanguage][] = $stream;
			}
			
				// Set 'streamsAsChannels' only if there are more than 2 audio streams in the file
			if(count($channelStreams)>1){
				$rvAnalize->streamsAsChannels = $channelStreams;
			}
				// Set 'languages' only if there are more than 1 language in the file
			if(count($langStreams)>1){
				$rvAnalize->languages = $langStreams;
			}	// not overlayed streams, probably should be concated
			if(count($contentStreams->audio)-count($identicalDur['audio'])>2){
				$rvAnalize = null;
			}
			 
		}
		
		if(count($identicalDur)>0) $rvAnalize->identicalDur = $identicalDur;
		if(count($differentDur)>0) $rvAnalize->differentDur = $differentDur;		
		if(count($zeroedDur)>0) $rvAnalize->zeroedDur = $zeroedDur;
		
		return $rvAnalize;
	}
	
	/**
	 * 
	 * @param unknown_type $source
	 * @param unknown_type $analyzedStreams
	 * @return NULL|stdClass
	 */
	private static function sorroundAudioSurceToTarget($source, $multiStreamSettings, $analyzedStreams)
	{
		/*
		 * If there is manually defined multiStream/sorround preset - use it, 
		 * don't attempt to figure it out automatically
		 */
		if((isset($multiStreamSettings)
		&& !(isset($multiStreamSettings->detect) && $multiStreamSettings->detect=='auto'))){
			return $multiStreamSettings;
		}

		/*
		 * Sample json string: 
		 * 		- {"detect":"auto"}
		 * 		- {"audio":{"mapping":[1,2]}}
		 * Struct:
		 * 	detect - 'auto', when set all other fields are omitted, (optional)
		 * 	audio - either as a single field or as array
		 * 		mapping - array of stream ids, optionally in ffmpeg syntax (with source file id)
		 * 		action	- 'merge' (default),'languages', (optional)
		 * 		output	- output stream (optional)
		 * 	video
		 * 		...
		 *
		 */
		$multiStream = new stdClass();
		$multiStream->audio = new stdClass();
		$multiStream->audio->mapping = array();
		/*
		 * Use 'downnmix' stream, if there is such,
		 * Otherwise try to map-in FL and FR streams
		 */
		$mappedStreams = KDLAudioLayouts::matchLayouts($source->_contentStreams->audio, KDLAudioLayouts::DOWNMIX);
		if(count($mappedStreams)==0) {
			$mappedStreams = KDLAudioLayouts::matchLayouts($analyzedStreams, array(KDLAudioLayouts::FL, KDLAudioLayouts::FR, KDLAudioLayouts::MONO,));
		}
		foreach ($mappedStreams as $stream){
			$multiStream->audio->mapping[] = $stream->id;
		}
		return $multiStream;
	}
	
	/**
	 * 
	 * @param unknown_type $source
	 * @param unknown_type $languages
	 * @return - 
	 * 	null - not applicable for that source (non multi-lingual) or for the required multiStream settings (no multi-lingual requirement)
	 * 	stdClass obj with set audio-languages array - holding matched languages
	 * 	stdClass obj with empty audio-languages array - no matched languages
	 */
	private static function multiLingualAudioSurceToTarget($source, $multiStreamSettings, $languages)
	{
			/*
			 * Default behavior - avoid multi-lang processing if not asked for 
			 */
		if(!(isset($multiStreamSettings) && isset($multiStreamSettings->audio) 
			 && property_exists($multiStreamSettings->audio,"languages"))){
			return null;
		}
		
			/*
			 * If no multi-lingual data in the source, get out
			 */
		if(!(is_array($languages) && count($languages)>0)){
			return null;
		}

		if(is_array($multiStreamSettings->audio->languages) && count($multiStreamSettings->audio->languages)>0){
			$settingsLanguages = $multiStreamSettings->audio->languages;
		}
		else $settingsLanguages = null;
		
		/*
		 * Sample json string: 
		 * 		- {"audio":{"languages":["eng","esp"]}}
		 */
		$multiStream = new stdClass();
		$multiStream->audio = new stdClass();
		$multiStream->audio->languages = array();
		
		foreach ($languages as $lang=>$streams){
			if(isset($settingsLanguages) && !in_array($lang, $settingsLanguages)){
				continue;
			}
			if(count($streams)>1) {
				return null;
			}
			$multiStream->audio->languages[$lang] = $streams[0];
		}
		
		return $multiStream;
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
