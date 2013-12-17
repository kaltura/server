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
//	public	$_multiStream=null;
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