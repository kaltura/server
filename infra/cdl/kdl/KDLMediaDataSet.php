<?php

//include_once("KDLCommon.php");
//include_once("KDLMediaObjectData.php");

	/* ---------------------------
	 * KDLMediaDataSet
	 */
class KDLMediaDataSet  {
	
	/* ---------------------
	 * Data
	 */
	public  $_pdf=null;
	public  $_swf=null;
	public	$_container=null;
	public	$_video=null;
	public	$_audio=null;
	public	$_image=null;
	public	$_multiStream=null;
	public  $_errors=array(),
			$_warnings=array();
	
	/* ----------------------
	 * Cont/Dtor
	 */
	public function __construct() {
		;
	}
	public function __destruct() {
		unset($this);
	}

	/* ------------------------------
	 * Initialize
	 */
	public function Initialize() {
		 
		if($this->_video) {
			$this->_video->CheckAndFixFormat();
				/*
				 * If video.BR is undefined ==> evaluate from file size & duration
				 */
			$br = 0;
			if($this->_video->_bitRate=="" && $this->_container
			&& $this->_container->_duration>0){
				$br = round($this->_container->_fileSize*8*1024/$this->_container->_duration);
				if(isset($this->_audio) && $this->_audio->_bitRate!=="")
					$br = $br - $this->_audio->_bitRate;
				if($br>0)
					$this->_video->_bitRate = $br;
				else {
					$this->_video->_bitRate = 100;
					$this->_warnings[KDLConstants::VideoIndex][] = //"Invalid bitrate value. Set to defualt ".$this->_video->_bitRate;
						KDLWarnings::ToString(KDLWarnings::SetDefaultBitrate, $this->_video->_bitRate);
				}
			}
		}

		if($this->_audio) {
			$this->_audio->CheckAndFixFormat();
		}
		
		if($this->_container) {
			$this->_container->CheckAndFixFormat();
			if($this->_container->_bitRate=="" && $this->_container->_duration>0){
				$this->_container->_bitRate = round($this->_container->_fileSize*8*1024/$this->_container->_duration);
			}
		}
		if($this->Validate()!=true)
			return false;

		return true;
	}

	/* ------------------------------
	 * Validate
	 */
	public function Validate() {
		if($this->_video!="") 
			$this->_video->Validate($this->_errors, $this->_warnings);
		if($this->_audio!="") 
			$this->_audio->Validate($this->_errors, $this->_warnings);
		if($this->_container!="" && $this->_image=="")
			$this->_container->Validate($this->_errors, $this->_warnings);
		if($this->_video=="" && $this->_audio=="" && $this->_image==""){
				// "Invalid File - No media content";
			$this->_errors[KDLConstants::ContainerIndex][] = KDLErrors::ToString(KDLErrors::NoValidMediaStream);
		}
		if(count($this->_errors)>0)
			return false;

		return true;
	}
	
	/* ---------------------------
	 * IsDataSet
	 */
	public function IsDataSet()
	{
	$count=0;
		if($this->_container && $this->_container->IsDataSet())
			$count++;
		
		if($this->_video && $this->_video->IsDataSet())
			$count++;
		
		if($this->_audio && $this->_audio->IsDataSet())
			$count++;
		
		if($count==0)
			return false;
		else
			return true;
	}
	
	/* ---------------------------
	 * ToTags
	 */
	public function ToTags($tagList)
	{
		$tagsOut = array();
		if($this->_container==null){
			return $tagsOut;
		}
		/*
		 * The format/codec names used here are both real-life media-info meta data
		 *  and KDL constants.
		 */
		$flashContainerFormats = array("flash video", "flv", "f4v","flash","flashvideo");
		$mp4ContainerFormats = array("mpeg-4",KDLContainerTarget::MP4,KDLContainerTarget::MOV);
		$mp4VideoFormats = array("avc","avc1","h264","h.264",
						KDLVideoTarget::H264,KDLVideoTarget::H264B,KDLVideoTarget::H264M,KDLVideoTarget::H264H);
		$mp4AudioFormats = array("mpeg audio","mp3","aac");
		$wmvContainerFormats = array("windows media");
		$itunesContainerFormats = array("mpeg-4","mpeg audio", "aiff", "wave");
		
		foreach($tagList as $tag) {
			switch($tag){
				case "flv":
					if($this->_container->IsFormatOf($flashContainerFormats))
						$tagsOut[] = $tag;
					break;
				case "web":
							// MP3 sources are flash-web-playable as well
					if($this->_container->IsFormatOf(array("mpeg audio"))) {
						$tagsOut[] = $tag;
					}
				case "mbr":
					if($this->_video && $this->_video->_rotation>0) {
						break;
					}
					if($this->_container->IsFormatOf($flashContainerFormats))
						$tagsOut[] = $tag;
					else {
						if($this->_container->IsFormatOf($mp4ContainerFormats)) {
							if($this->_video || $this->_audio) {
								if((!$this->_video || ($this->_video && $this->_video->IsFormatOf($mp4VideoFormats)))
								&& (!$this->_audio || ($this->_audio && $this->_audio->IsFormatOf($mp4AudioFormats)))){
									$tagsOut[] = $tag;
								}
							}
						}
					}
					break;
				case "ipad":
				case "ipadnew":
					if($this->_container->IsFormatOf($mp4ContainerFormats)) {
						if($this->_video || $this->_audio) {
							if((!$this->_video || ($this->_video && $this->_video->IsFormatOf($mp4VideoFormats)))
							&& (!$this->_audio || ($this->_audio && $this->_audio->IsFormatOf($mp4AudioFormats)))){
								$tagsOut[] = $tag;
							}
						}
					}
					break;
				case "slweb":
					if($this->_container->IsFormatOf($wmvContainerFormats))
						$tagsOut[] = $tag;
					break;
				case "itunes":
					if($this->_container->_id=="qt" 
					|| $this->_container->IsFormatOf($itunesContainerFormats))
						$tagsOut[] = $tag;
					break;
				default:
					break;
			}
		}

		return $tagsOut;
	}

	/**
	 * 
	 * @param unknown_type $condition
	 * 
	 * Check whether the KDLMediaDataSet object comply with provided condtion string.
	 * The condition string can contain - 
	 * - numbers
	 * - signs
	 * -- boolean operators -  '!=<>&|'
	 * -- aritmetic operators - '/+-*'
	 * -- brackets '()'
	 * -- space
	 * - 'Variables' that repsent KDLMediaDataSet fields -
	 * -- containerFormat, containerDuration, containerBitRate, fileSize
	 * -- videoFormat, videoDuration, videoBitRate, videoWidth, videoHeight, videoFrameRate, videoDar, videoRotation, scanType, contentAwareness, videoGop
	 * -- audioFormat, audioDuration, audioBitRate, audioChannels, audioSampleRate, audioResolution
	 * - Allowed codecs/formats - 
	 * -- mp4, mxf, wmv3, mpegps, mpegts, webm, mp3
	 * -- h264, h265, vp6, vp8, vp9, wmv3, mpeg2
	 * -- mp3, aac, mpeg2, pcm
	 * -- Preset conditions
	 * --- isMbr 
	 * --- isWeb
	 * 
	 * Since this funtion uses php 'eval' function, following precautions are applied to make sure that 
	 * executed statement is harmless:
	 * - the prepare logic interperts all the operands either into numbers or into the allowed list of strings (see above)
	 * - the resultant interpreted condition string is tokenized with allowed signs (see above)
	 * - the tokens must be either numbers or allowed strings (see above - 'Allowed codecs/formats'/Preset conditions.
	 * 
	 * Example:
	 * - Input raw condition 
	 * 		containerFormat==mp4 && videoHeight<1080 && videoWidth<1920 && videoDar<1.77777 && isMbr==1
	 * - Interpreted condition, based on the source media info - 
	 * 		mp4==mp4 && 720<1080 && 1280<1920 && 1.25<1.77777 && isMbr==1
	 * - Result - true
	 */
	public function IsCondition($condition)
	{
		KalturaLog::log("Input condition - $condition");
		$valsArr = array();
			/*
			 *  Set conatiner related fields
			 */
		if(isset($this->_container) && $this->_container->IsDataSet()){
			$obj = $this->_container;
			if($obj->IsFormatOf(array("isom","mp42","qt","quicktime","m4v"))){
				$valsArr["containerFormat"]="mp4";
			}
			else if($obj->IsFormatOf(array("mxf"))){
				$valsArr["containerFormat"]="mxf";
			}
			else if($obj->IsFormatOf(array("windows media"))){
				$valsArr["containerFormat"]="wmv";
			}
			else if($obj->IsFormatOf(array("mpeg-ps"))){
				$valsArr["containerFormat"]="mpegps";
			}
			else if($obj->IsFormatOf(array("mpeg-ts"))){
				$valsArr["containerFormat"]="mpegts";
			}
			else if($obj->IsFormatOf(array("webm"))){
				$valsArr["containerFormat"]="webm";
			}
			else if($obj->IsFormatOf(array("mpeg audio"."mp3"))){
				$valsArr["containerFormat"]="mp3";
			}
			else {
				$valsArr["containerFormat"]="undefined";
			}
			$valsArr["containerDuration"]=$obj->_duration;
			$valsArr["containerBitRate"]=$obj->_bitRate;
			$valsArr["fileSize"]=$obj->_fileSize;
		}
		else {
			$valsArr["containerFormat"]="undefined";
			$valsArr["containerDuration"]=0;
			$valsArr["containerBitRate"]=0;
			$valsArr["fileSize"]=0;
		}
			/*
			 * Set video related fields
			 */
		$valsArr["videoFormat"]="undefined";
		$valsArr["videoDuration"]=0;
		$valsArr["videoBitRate"]=0;
		$valsArr["videoWidth"]=0;
		$valsArr["videoHeight"]=0;
		$valsArr["videoFrameRate"]=0;
		$valsArr["videoDar"]=0;
		$valsArr["videoRotation"]=0;
		$valsArr["scanType"]=0;
		$valsArr["contentAwareness"]=0;		
		$valsArr["videoGop"]=0;
		if(isset($this->_video) && $this->_video->IsDataSet()){
			$obj = $this->_video;
			if($obj->IsFormatOf(array("avc","avc1","h264"))){
				$valsArr["videoFormat"]="h264";
			}
			else if($obj->IsFormatOf(array("hev","hev1","hevc","h265"))){
				$valsArr["videoFormat"]="h265";
			}
			else if($obj->IsFormatOf(array("vp6","vp6.1","vp6.2","vp6f","flv4"))){
				$valsArr["videoFormat"]="vp6";
			}
			else if($obj->IsFormatOf(array("vc-1","wmv3"))){
				$valsArr["videoFormat"]="wmv3";
			}
			else if($obj->IsFormatOf(array("vp8"))){
				$valsArr["videoFormat"]="vp8";
			}
			else if($obj->IsFormatOf(array("vp9","v_vp9"))){
				$valsArr["videoFormat"]="vp9";
			}
			else if($obj->IsFormatOf(array("mpegvideo","mpeg video"))){
				$valsArr["videoFormat"]="mpeg2";
			}
			else
				$valsArr["videoFormat"]="undefined";
			if(isset($obj->_duration))	$valsArr["videoDuration"]=$obj->_duration;
			if(isset($obj->_bitRate))	$valsArr["videoBitRate"]=$obj->_bitRate;
			if(isset($obj->_width))		$valsArr["videoWidth"]=$obj->_width;
			if(isset($obj->_height))	$valsArr["videoHeight"]=$obj->_height;
			if(isset($obj->_frameRate))	$valsArr["videoFrameRate"]=$obj->_frameRate;
			if(isset($obj->_dar))		$valsArr["videoDar"]=$obj->_dar;
			if(isset($obj->_rotation))	$valsArr["videoRotation"]=$obj->_rotation;
			if(isset($obj->_scanType))	$valsArr["scanType"]=$obj->_scanType;
			if(isset($obj->_contentAwareness)) $valsArr["contentAwareness"]=$obj->_contentAwareness;
			if(isset($obj->_gop))		$valsArr["videoGop"]=$obj->_gop;
		}
			/*
			 * Set audio related fields
			 */
                $valsArr["audioFormat"]="undefined";
                $valsArr["audioDuration"]=0;
                $valsArr["audioBitRate"]=0;
                $valsArr["audioChannels"]=0;
                $valsArr["audioSamplingRate"]=0;
                $valsArr["audioResolution"]=0;
                $valsArr["audioStreams"]=0;
		if(isset($this->_audio) && $this->_audio->IsDataSet()){
			$obj = $this->_audio;
			$valsArr["audioFormat"]=$obj->GetIdOrFormat();
			if($obj->IsFormatOf(array("aac"))){
				$valsArr["audioFormat"]="aac";
			}
			else if($obj->IsFormatOf(array("mpeg audio","mp3"))){
				$valsArr["audioFormat"]="mp3";
			}
			else if($obj->IsFormatOf(array("mp2"))){
				$valsArr["audioFormat"]="mp2";
			}
			else if($obj->IsFormatOf(array("pcm"))){
				$valsArr["audioFormat"]="pcm";
			}
			else if($obj->IsFormatOf(array("ac3","ac-3"))){
				$valsArr["audioFormat"]="ac3";
			}
			else if($obj->IsFormatOf(array("eac3","eac-3","e-ac-3"))){
				$valsArr["audioFormat"]="eac3";
			}
			else
				$valsArr["audioFormat"]="undefined";
			if(isset($obj->_duration))	$valsArr["audioDuration"]=$obj->_duration;
			if(isset($obj->_bitRate))	$valsArr["audioBitRate"]=$obj->_bitRate;
			if(isset($obj->_channels))	$valsArr["audioChannels"]=$obj->_channels;
			if(isset($obj->_sampleRate))	$valsArr["audioSampleRate"]=$obj->_sampleRate;
			if(isset($obj->_resolution))	$valsArr["audioResolution"]=$obj->_resolution;
			if(isset($this->_contentStreams->audio)) {
				$valsArr["audioStreams"]=count($this->_contentStreams->audio);
			}
			else 
				$valsArr["audioStreams"]=0;
		}
			/*
			 * Check 'isWeb' and 'isMbr' presets
			 */
		$tagsOut = $this->ToTags(array("mbr","web"));
		$valsArr["isWeb"] = in_array("web", $tagsOut)? 1: 0;
		$valsArr["isMbr"] = in_array("mbr", $tagsOut)? 1: 0;
		
			/*
			 * Assign the 'variables' with KDLMediaDataSet values
			 */
		$opsArr = array_keys($valsArr);
		$condition = str_replace($opsArr, $valsArr, $condition);
		KalturaLog::log("After assignment - $condition");

			/*
			 * Verify that the condition statement contains ONLY allowed strings (see decription in the func header)
			 */
		$containerFormats = array("mp4","mxf","wmv3","mpegps","mpegts","webm","mp3");
		$videoCodecs = array("h264","h265","vp6","vp8","vp9","wmv3","mpeg2");
		$audioCodecs = array("mp3","aac","mpeg2","pcm","ac3","eac3");
		$allowedFormats = array_merge($containerFormats, $videoCodecs, $audioCodecs);
		$allowedFormats[] = "undefined";
		$allowedChars = ' +-*/()!=<>&|;';
		$tok = strtok($condition, $allowedChars);
		while ($tok !== false) {
			if(!(is_numeric($tok)||in_array($tok,$allowedFormats))){
				KalturaLog::log("Invalid token - $tok. Leaving");
				return null;
			}
			$tok = strtok($allowedChars);
		}

			/*
			 * Add '"' to all strings, otherwise it would be an invalid php statement.
			 */
		$strArr = array();
		foreach($allowedFormats as $aux){
			$strArr[] = "\"$aux\"";
		}
		$condition = str_replace($allowedFormats, $strArr, $condition).";";
		KalturaLog::log("Final - $condition");
		
			/*
			 * The precautions to ensure the harmlessness of the condition string are described in the func header.
			 */
		$rv = eval("return ".$condition);

		KalturaLog::log("RV - ".($rv? "true":"false"));
		return $rv;
	}
		
	/* ---------------------------
	 * ToString
	 */
	public function ToString(){
	$rvStr = null;
	try {
		if($this->_container && method_exists ($this->_container, 'ToString')) {
			$str = $this->_container->ToString();
			if($str)
				$rvStr = $str;
		}
		if($this->_video && method_exists($this->_video, 'ToString')){
			$str = $this->_video->ToString();
			if($str)
				if($rvStr) $rvStr = $rvStr.", ".$str;
				else $rvStr = $str;
		}
		if($this->_audio && method_exists ($this->_audio, 'ToString')){
			$str = $this->_audio->ToString();
				if($rvStr) $rvStr = $rvStr.", ".$str;
				else $rvStr = $str;
		}
		if($this->_image && method_exists ($this->_image, 'ToString')){
			$str = $this->_image->ToString();
			if($str)
				if($rvStr) $rvStr = $rvStr.", ".$str;
				else $rvStr = $str;
		}
		if($this->_pdf && method_exists ($this->_pdf, 'ToString')){
			$str = $this->_pdf->ToString();
			if($str)
				if($rvStr) $rvStr = $rvStr.", ".$str;
				else $rvStr = $str;
		}
		if($this->_swf && method_exists ($this->_swf, 'ToString')){
			$str = $this->_swf->ToString();
			if($str)
				if($rvStr) $rvStr = $rvStr.", ".$str;
				else $rvStr = $str;
		}
		}
		catch(Exception $ex){
			;
		}
		return $rvStr;
	}
}

?>
