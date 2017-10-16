<?php
/**
 * @package plugins.ffmpeg
 * @subpackage lib
 */
class KDLOperatorFfmpeg0_10 extends KDLOperatorFfmpeg {

	/* ---------------------------
	 * GenerateCommandLine
	 */
    public function GenerateCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
	{
		$cmdStr = $this->generateSinglePassCommandLine($design, $target, $extra);
		$cmdStr = $this->processTwoPass($target, $cmdStr);
		return $cmdStr;
	}
	
	/* ---------------------------
	 * generateSinglePassCommandLine
	 */
	protected function generateSinglePassCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
	{
		return parent::GenerateCommandLine($design, $target, $extra);
	}
	
	/* ---------------------------
	 * generateH264params
	 */
	protected function generateH264params($videoObject)
	{
		$h264params=null;
		$ffQsettings = " -qcomp 0.6 -qmin 10 -qmax 50 -qdiff 4";
		switch($videoObject->_id) {
		case KDLVideoTarget::H264H:
			$h264params=" -subq 7".$ffQsettings." -bf 16 -coder 1 -refs 6 -x264opts b-pyramid:weightb:mixed-refs:8x8dct:no-fast-pskip=0";
			if($videoObject->_bitRate<KDLConstants::LowBitrateThresHold) {
				$h264params .= " -crf 30";
			}
			break;
		default:
			$h264params=parent::generateH264params($videoObject);
			break;
		}
		return $h264params;
	}
	
	/* ---------------------------
	 * generateVideoParams
	 */
    protected function generateVideoParams(KDLFlavor $design, KDLFlavor $target)
	{
		$cmdStr = parent::generateVideoParams($design, $target);
		if(!isset($target->_video))
			return $cmdStr;

			/*
			 * On COPY the filters are unrequried/unapplicable - 
			 * therefore - skip it.
			 */
		if($target->_video->IsFormatOf(array(KDLVideoTarget::COPY))){
			return $cmdStr;
		}
		
$vid = $target->_video;
$fltStr = null;

			/*
			 * The 'old' deinterlace failed to handle several HD sources. 
			 * Switched to newer 'yadif'
			 */
		if(strstr($cmdStr, " -deinterlace")!=false) {
			$cmdStr = str_replace(" -deinterlace", "", $cmdStr);
		}
		
		$filters = $this->generateVideoFilters($vid);
		if(count($filters)>0){
			$fltStr = implode(';', $filters);
			$cmdStr.= " -vf '$fltStr'";
		}
		return $cmdStr;
	}

	/* ---------------------------
	 * generateContainerParams
	 */
    protected function generateContainerParams(KDLFlavor $design, KDLFlavor $target)
	{
		$cmdStr = parent::generateContainerParams($design, $target);
		if(!isset($cmdStr)) 
			return null;
			/*
			 * Remove menu and chapter meta data that harms RTMP streaming 
			 * This is not relevant for FLV targets (just MP4 derivatives)
			 */
		if($target->ToTags(array("mbr")) && $target->_container->_id!=KDLContainerTarget::FLV) {
			$cmdStr = " -map_chapters -1 -map_metadata -1 $cmdStr";
		}
		
		return $cmdStr;
	}

	/**
	 * 
	 * @param unknown_type $vid
	 * @return multitype:string
	 */
	protected static function generateVideoFilters($vid)
	{
		/*
		 *  The 'old' deinterlace failed to handle several HD sources.
		 * Switched to newer 'yadif'
		 */
		$filters = array();
		if($vid->_scanType!==null && $vid->_scanType>0){ // ScanType 0:progressive, 1:interlaced
			$filters[] = "yadif";
		}
	
		if(isset($vid->_rotation)) {
			switch($vid->_rotation) {
				case 180:
					$filters[] = "vflip";
					$filters[] = "hflip";
					break;
				case 90:
					$filters[] = "transpose=1";
					break;
				case 270:
				case -90:
					$filters[] = "transpose=2";
					break;
			}
		}
		// Letterboxing
		if(isset($vid->_arProcessingMode) && $vid->_arProcessingMode==2){
			$str ='scale=iw*sar*min('.$vid->_width.'/(iw*sar)\,';
			$str.=$vid->_height.'/ih):ih*min('.$vid->_width.'/(iw*sar)\,';
			$str.=$vid->_height.'/ih)';
			$filters[] = $str;
			$filters[] = 'pad='.$vid->_width.':'.$vid->_height.':(ow-iw)/2:(oh-ih)/2';
		}

			/*
			 * For IMX - crop the source
			 */
		if(isset($vid->_isCropIMX) && $vid->_isCropIMX==true){
			$filters[] = "crop=in_w:in_h-32:in_w:32";
		}
		return $filters;
	}
	
	/* ---------------------------
	 * calcForcedKeyFrames
	 */
    protected function calcForcedKeyFrames($vidObj, KDLFlavor $target)
    {
    	if($vidObj->_forcedKeyFramesMode==0
    	|| !($vidObj->_forcedKeyFramesMode==1 && isset($vidObj->_gop) && isset($vidObj->_frameRate) && $vidObj->_frameRate>0)){
    		return null;
    	}

		/*
		 * For clipped content, calculate the forcedKF 
		 * duration according to clipped dur
		 */
    	$gopInSecs=($vidObj->_gop/$vidObj->_frameRate);
    	
    	//SUP 681 - If source frame rate is smaller than 1 or even 0 than do not use force key frames to avoid creation of unsmooth output video 
    	if(!($gopInSecs>0))
    		return null;
    	
		if(isset($target->_explicitClipDur) && $target->_explicitClipDur)
			$duration=$target->_explicitClipDur/1000;
		else if(isset($target->_clipDur) && $target->_clipDur)
			$duration=$target->_clipDur/1000;
		else
			$duration = $target->_container->_duration/1000;

/*  Replace self-calculated KF's with FFMpeg formula			
		if($duration>7200) {
			$forcedKF = "expr:'gte(t,n_forced*".round($gopInSecs).")'";
		}
		else {
			$forcedKF = KDLCmdlinePlaceholders::ForceKeyframes.$duration."_$gopInSecs";
		}
*/
		$forcedKF = "expr:'gte(t,n_forced*".round($gopInSecs).")'";
		return " -force_key_frames $forcedKF";
    }
    
	/* ---------------------------
	 * getVideoCodecName
	 */
    protected function getVideoCodecSpecificParams(KDLFlavor $design, KDLFlavor $target)
	{
$vidObj = $target->_video;
$paramsStr = null;
		switch($vidObj->_id){
		case KDLVideoTarget::H264:
		case KDLVideoTarget::H264B:
			return parent::getVideoCodecSpecificParams($design, $target)
					." -vprofile baseline".$this->calcForcedKeyFrames($vidObj,$target)
					." -pix_fmt yuv420p";
		case KDLVideoTarget::H264M:
			return parent::getVideoCodecSpecificParams($design, $target)
					." -vprofile main".$this->calcForcedKeyFrames($vidObj,$target)
					." -pix_fmt yuv420p"					;
		case KDLVideoTarget::H264H:
			return parent::getVideoCodecSpecificParams($design, $target)
					." -vprofile high".$this->calcForcedKeyFrames($vidObj,$target)
					." -pix_fmt yuv420p";
		case KDLVideoTarget::APCO:
			return "prores -profile 0 -pix_fmt yuv422p10le";
		case KDLVideoTarget::APCS:
			return "prores -profile 1 -pix_fmt yuv422p10le";
		case KDLVideoTarget::APCN:
			return "prores -profile 2 -pix_fmt yuv422p10le";
		case KDLVideoTarget::APCH:
			return "prores -profile 3 -pix_fmt yuv422p10le";
		case KDLVideoTarget::DNXHD:
			return "dnxhd -mbd rd -pix_fmt yuv422p";
		default:
			return parent::getVideoCodecSpecificParams($design, $target)." -pix_fmt yuv420p";
		}
	}
	
	/* ---------------------------
	 * processTwoPass
	 */
    protected function processTwoPass(KDLFlavor $target, $cmdStr)
	{
		if(!isset($target->_isTwoPass) || $target->_isTwoPass==0)
			return $cmdStr;

		$pass2params = "-passlogfile ".KDLCmdlinePlaceholders::OutFileName.".2pass.log -pass";

$nullDev = "NUL";
$nullDev ="/dev/null";
		$pass1cmdLine =
			str_replace ( 
				array(KDLCmdlinePlaceholders::OutFileName, " -y"), 
				array($nullDev, " -an $pass2params 1 -fastfirstpass 1 -y"),
				$cmdStr);

		$pass2cmdLine =
			str_replace ( 
				array(" -y"), 
				array(" $pass2params 2 -y"),
				$cmdStr);
		$cmdStr = "$pass1cmdLine && ".KDLCmdlinePlaceholders::BinaryName." $pass2cmdLine ";
			
		return $cmdStr;
	}
	
	/* ---------------------------
	 * CheckConstraints
	 */
	public function CheckConstraints(KDLMediaDataSet $source, KDLFlavor $target, array &$errors=null, array &$warnings=null)
	{
	    if(KDLOperatorBase::CheckConstraints($source, $target, $errors, $warnings)==true)
			return true;

		if(!isset($target->_video))
			return false;
		
			/*
			 * HD codecs (prores & dnxhd) can be packaged only in MOV/MXF
			 */
$hdCodecsArr = array(KDLVideoTarget::APCO,KDLVideoTarget::APCS,KDLVideoTarget::APCN,KDLVideoTarget::APCH,KDLVideoTarget::DNXHD);
		if(isset($target->_container))
		{
			if(!$target->_container->IsFormatOf(array(KDLContainerTarget::MOV,KDLContainerTarget::MXF)) && in_array($target->_video->_id, $hdCodecsArr)){
				$target->_errors[KDLConstants::ContainerIndex][] = 
					KDLErrors::ToString(KDLErrors::PackageMovOnly, $target->_video->_id);
				return true;
			}
		}
		
			/*
			 * DNXHD - check validity of frame-size/bitrate mix
			 */
/*
Project Format	Resolution	Frame Size	Bits	FPS		<bitrate>
1080i / 59.94	DNxHD 220	1920 x 1080	8		29.97	220M
1080i / 59.94	DNxHD 145	1920 x 1080	8		29.97	145M
1080i / 50		DNxHD 185	1920 x 1080	8		25		185M
1080i / 50		DNxHD 120	1920 x 1080	8		25		120M
1080p / 25		DNxHD 185	1920 x 1080	8		25		185M
1080p / 25		DNxHD 120	1920 x 1080	8		25		120M
1080p / 25		DNxHD 36	1920 x 1080	8		25		36M
1080p / 24		DNxHD 175	1920 x 1080	8		24		175M
1080p / 24		DNxHD 115	1920 x 1080	8		24		115M
1080p / 24		DNxHD 36	1920 x 1080	8		24		36M
1080p / 23.976	DNxHD 175	1920 x 1080	8		23.976	175M
1080p / 23.976	DNxHD 115	1920 x 1080	8		23.976	115M
1080p / 23.976	DNxHD 36	1920 x 1080	8		23.976	36M
1080p / 29.7	DNxHD 45	1920 x 1080	8		29.97	45M

720p  / 59.94	DNxHD 220	1280 x 720	8		59.94	220M
720p  / 59.94	DNxHD 145	1280 x 720	8		59.94	145M
720p  / 50		DNxHD 175	1280 x 720	8		50		175M
720p  / 50		DNxHD 115	1280 x 720	8		50		115M
720p  / 23.976	DNxHD 90	1280 x 720	8		23.976	90M
720p  / 23.976	DNxHD 60	1280 x 720	8		23.976	60M
 */
		
		if($target->_video->_id==KDLVideoTarget::DNXHD) {
			if(!isset($target->_video->_width) || $target->_video->_width==0)
				$width = 0;
			else
				$width = $target->_video->_width;
			if(!isset($target->_video->_height) || $target->_video->_height==0)
				$height = 0;
			else
				$height = $target->_video->_height;
				
$dnxhd720BitratesArr = array(220000,145000,175000,115000,90000,60000);
$dnxhd1080BitratesArr = array(220000,145000,185000,120000,36000,175000,115000,45000);
			if(in_array($target->_video->_bitRate,$dnxhd720BitratesArr)
			&& (
				($width==1280 && $height==720) || ($width==1280 && $height==0) || ($width==0	&& $height==720)
			)){
KalturaLog::log("Supported DNXHD - br:".$this->_video->_bitRate.",w:$width,h:$height");
			}
			else if(in_array($target->_video->_bitRate,$dnxhd1080BitratesArr)
			&& (
				($width==1920 && $height==1080) || ($width==1920 && $height==0) || ($width==0	&& $height==1080)
			)){
KalturaLog::log("Supported DNXHD - br:".$target->_video->_bitRate.",w:$width,h:$height");
			}
			else {
				$str = "br:".$target->_video->_bitRate.",w:$width,h:$height";
				$target->_errors[KDLConstants::VideoIndex][] = 
					KDLErrors::ToString(KDLErrors::DnxhdUnsupportedParams, $str);
				return true;
			}
				
		}
		
			// Encryption unsupported by ffmpeg < 2.7.2
		if($target->_isEncrypted==true){
			$warnings[KDLConstants::ContainerIndex][] = 
				KDLWarnings::ToString(KDLWarnings::TranscoderLimitation, $this->_id)."(encryption)";
			return true;
		}
		return $this->checkBasicFFmpegConstraints($source, $target, $errors, $warnings);
	}
}
	
