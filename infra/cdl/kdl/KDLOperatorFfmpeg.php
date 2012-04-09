<?php
/**
 * @package plugins.ffmpeg
 * @subpackage lib
 */
class KDLOperatorFfmpeg extends KDLOperatorBase {
/*
    public function __construct($id, $name=null, $sourceBlacklist=null, $targetBlacklist=null) {
    	parent::__construct($id, $name, $sourceBlacklist,$targetBlacklist);
    }
*/
	/* ---------------------------
	 * GenerateCommandLine
	 */
    public function GenerateCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
	{
	$cmdStr = null;
// rem ffmpeg -i <infilename> -vcodec flv   -r 25 -b 500k  -ar 22050 -ac 2 -acodec libmp3lame -f flv -t 60 -y <outfilename>

		if(isset($target->_clipStart) && $target->_clipStart>0){
			$cmdStr .= " -ss ".$target->_clipStart/1000;
		}
		
		if(isset($target->_clipDur) && $target->_clipDur>0){
			$cmdStr .= " -t ".$target->_clipDur/1000;
		}
		
		$cmdStr.= " -i ".KDLCmdlinePlaceholders::InFileName;
		
		$cmdStr.= $this->generateVideoParams($design, $target);
		$cmdStr.= $this->generateAudioParams($design, $target);
		$cmdStr.= $this->generateContainerParams($design, $target);
		
		/*
		 * Following 'dummy' seek-to setting is done to ensure preciseness
		 * of the main seek command that is done at the beginning o fthe command line
		 */
		if(isset($target->_clipStart) && $target->_clipStart>0){
			$cmdStr.= " -ss 0.01";
		}
		
		if($extra)
			$cmdStr.= " ".$extra;
		
		$cmdStr.= " -y ".KDLCmdlinePlaceholders::OutFileName;

		return $cmdStr;
	}
	
	/* ---------------------------
	 * generateVideoParams
	 */
    protected function generateVideoParams(KDLFlavor $design, KDLFlavor $target)
	{
		if(!isset($target->_video)){
			return " -vn";
		}
		
$vcodecParams = "fl";
$cmdStr = null;
$vid = $target->_video;
$vbrFixedVp6=null;
		switch($vid->_id){
			case KDLVideoTarget::VP6:
				if(isset($design->_video) && $design->_video->_bitRate)
					$vbrFixedVp6=$this->fixVP6BitRate($design->_video->_bitRate, $vid->_bitRate);
			case KDLVideoTarget::FLV:
			case KDLVideoTarget::H263:
				$vcodecParams = "flv";
				break; 
			case KDLVideoTarget::H264:
			case KDLVideoTarget::H264B:
			case KDLVideoTarget::H264M:
			case KDLVideoTarget::H264H:
				$vcodecParams = $this->generateH264params($vid);
				break; 				
			case KDLVideoTarget::MPEG4:
				$vcodecParams = "mpeg4";
				break;
			case KDLVideoTarget::THEORA:
				$vcodecParams = "libtheora";
				break;
			case KDLVideoTarget::WMV2:
			case KDLVideoTarget::WMV3:
			case KDLVideoTarget::WVC1A:
				$vcodecParams = "wmv2";
				break;
			case KDLVideoTarget::VP8:
				$vcodecParams = "libvpx";
				break; 
			case KDLVideoTarget::MPEG2:
				$vcodecParams = "mpeg2video";
				break;
			case KDLVideoTarget::COPY:
				$vcodecParams = "copy";
				break; 
		}
		
		$cmdStr = $cmdStr." -vcodec ".$vcodecParams;
$vBr = isset($vbrFixedVp6)? $vbrFixedVp6: $vid->_bitRate;
		if($vBr){
			$cmdStr .= " -b ".$vBr."k";
		}
$bt=0;
		if(isset($vid->_cbr) && $vid->_cbr>0) {
			$bt = round($vBr/10);
			$cmdStr.= " -minrate ".$vBr."k";
			$cmdStr.= " -maxrate ".$vBr."k";
			$cmdStr.= " -bufsize ".round($vBr/5)."k";
		}
		if(isset($vid->_bt) && $vid->_bt>0) {
			$cmdStr.= " -bt ".$vid->_bt."k";
		}
		else if($bt>0){
			$cmdStr.= " -bt $bt"."k";
		}
		
		if($vid->_width!=null && $vid->_height!=null){
			$cmdStr .= " -s ".$vid->_width."x".$vid->_height;
		}
		if(isset($vid->_dar) && $vid->_dar>0) {
			$cmdStr.= " -aspect ".round($vid->_dar,4);
		}
		if($vid->_frameRate!==null && $vid->_frameRate>0){
			$cmdStr .= " -r ".$vid->_frameRate;
		}
		if($vid->_gop!==null && $vid->_gop>0){
			$cmdStr .= " -g ".$vid->_gop;
		}
		if($vid->_scanType!==null && $vid->_scanType>0){ // ScanType 0:progressive, 1:interlaced
			$cmdStr .= " -deinterlace";
		}

		return $cmdStr;
	}
	
	/* ---------------------------
	 * generateAudioParams
	 */
    protected function generateAudioParams(KDLFlavor $design, KDLFlavor $target)
	{
		if(!isset($target->_audio)) {
			return " -an";
		}
		
$acodec = "libmp3lam";
$cmdStr = null;
$aud = $target->_audio;
		switch($aud->_id){
			case KDLAudioTarget::MP3:
				$acodec = "libmp3lame";
				break;
			case KDLAudioTarget::AAC:
				$acodec = "libfaac";
				break;
			case KDLAudioTarget::VORBIS:
				$acodec = "libvorbis";
				break;
			case KDLAudioTarget::WMA:
				$acodec = "wmav2";
				break;
			case KDLAudioTarget::AMRNB:
				// common settings - -ab 12.2k -ar 8000 -ac 1
				$acodec = "libopencore_amrnb";
				break;
			case KDLAudioTarget::MPEG2:
				$acodec = "mp2";
				break;
			case KDLAudioTarget::COPY:
				$acodec = "copy";
				break;
		}
		
		$cmdStr.= " -acodec ".$acodec;
		if($aud->_bitRate!==null && $aud->_bitRate>0){
			$cmdStr.= " -ab ".$aud->_bitRate."k";
		}
		if($aud->_sampleRate!==null && $aud->_sampleRate>0){
			$cmdStr.= " -ar ".$aud->_sampleRate;
		}
		if($aud->_channels!==null && $aud->_channels>0){
			$cmdStr.= " -ac ".$aud->_channels;
		}

		return $cmdStr;
	}

	/* ---------------------------
	 * generateContainerParams
	 */
    protected function generateContainerParams(KDLFlavor $design, KDLFlavor $target)
	{
		if(!isset($target->_container)) 
			return null;

$format = "fl";
$cmdStr = null;
$con = $target->_container;
		switch($con->_id){
			case KDLContainerTarget::FLV:
				$format = "flv";
				break;
			case KDLContainerTarget::AVI:
			case KDLContainerTarget::MP4:
			case KDLContainerTarget::_3GP:
			case KDLContainerTarget::MOV:
			case KDLContainerTarget::MP3:
			case KDLContainerTarget::OGG:
				$format = $con->_id;
				break;
			case KDLContainerTarget::WMV:
				$format = "asf";
				break;
			case KDLContainerTarget::MKV:
				$format = "matroska";
				break;
			case KDLContainerTarget::WEBM:
				$format = "webm";
				break;
			case KDLContainerTarget::MPEGTS:
			case KDLContainerTarget::APPLEHTTP:
				$format = "mpegts";
				break;
			case KDLContainerTarget::MPEG:
				$format = "mpeg";
				break;
		}
		$cmdStr.= " -f ".$format;

		return $cmdStr;
	}
	
	/* ---------------------------
	 * generateH264params
	 */
	protected function generateH264params($videoObject)
	{
/*
-sws - scale quality option, 0-lowest, 9- good
-subq - sub-pixel and partion search algorithms. 1 - fast/low quality, 9-slow/best quality, 5-average
global_header - makes it baseline
b_pyramid - to be used with bframes>2 (main and high profile)

good mencoder32 ~/Media/Canon.Rotated.0_qaqsufbl.avi -o ~/Media/aaa.mp4 -of lavf -lavfopts format=mp4 -ofps 25 -ovc x264 -sws 9 -x264encopts bitrate=300:subq=5:frameref=6:bframes=3:b_pyramid=1:weight_b=1:threads=auto:keyint=60:level_idc=30:global_header:partitions=all:trellis=1:chroma_me:me=umh:8x8dct -vf scale=304:176 -oac faac -faacopts mpeg=4:object=2:br=96 ; mediainfo ~/Media/aaa.mp4
bad  mencoder32 ~/Media/Canon.Rotated.0_qaqsufbl.avi -of lavf -lavfopts format=mp4 -ofps 25 -ovc x264        -x264encopts bitrate=300:subq=7:frameref=6:bframes=3:b_pyramid=1:weight_b=1:threads=auto:level_idc=30:global_header:8x8dct:trellis=1:chroma_me:me=umh:keyint=60 -vf harddup,scale=304:176 -oac faac -faacopts mpeg=4:object=2:br=96 -endpos 10 -o ~/Media/aaa.mp4; mediainfo ~/Media/aaa.mp4
     mencoder32 ~/Media/Canon.Rotated.0_qaqsufbl.avi -of lavf -lavfopts format=mp4 -ofps 25 -ovc x264 -sws 9 -x264encopts bitrate=300:subq=5:frameref=6:bframes=3:b_pyramid=1:weight_b=1:threads=auto:level_idc=30:global_header:partitions=all:trellis=1:chroma_me:me=umh:8x8dct:keyint=60 -vf scale=304:176 -oac faac -faacopts mpeg=4:object=2:br=96 -o ~/Media/aaa.mp4; mediainfo ~/Media/aaa.mp4
 
 */
		$h264params=null;
		$ffQsettings = " -qcomp 0.6 -qmin 10 -qmax 50 -qdiff 4";
		switch($videoObject->_id) {
		case KDLVideoTarget::H264:
			$h264params=" libx264 -subq 2".$ffQsettings;
			if($videoObject->_bitRate<KDLConstants::LowBitrateThresHold) {
				$h264params .= " -crf 30";
			}
			break;
		case KDLVideoTarget::H264B:
			$h264params=" libx264 -subq 2".$ffQsettings." -coder 0";;
			if($videoObject->_bitRate<KDLConstants::LowBitrateThresHold) {
				$h264params .= " -crf 30";
			}
			break;
		case KDLVideoTarget::H264M:
			$h264params="libx264 -subq 5".$ffQsettings." -coder 1 -refs 2";
			if($videoObject->_bitRate<KDLConstants::LowBitrateThresHold) {
				$h264params .= " -crf 30";
			}
			break;
		case KDLVideoTarget::H264H:				
			$h264params=" libx264 -subq 7".$ffQsettings." -bf 16 -coder 1 -refs 6 -flags2 +bpyramid+wpred+mixed_refs+dct8x8+fastpskip";
			if($videoObject->_bitRate<KDLConstants::LowBitrateThresHold) {
				$h264params .= " -crf 30";
			}
			break;
		}
		return $h264params;
	}
	
	/* ---------------------------
	 * CheckConstraints
	 */
	public function CheckConstraints(KDLMediaDataSet $source, KDLFlavor $target, array &$errors=null, array &$warnings=null)
	{
	    if(parent::CheckConstraints($source, $target, $errors, $warnings)==true)
			return true;

		/*
		 * Remove ffmpegs
		 * for rotated videos
		 */
		if($target->_video && $target->_video->_rotation) {
			$warnings[KDLConstants::VideoIndex][] = //"The transcoder (".$key.") does not handle properly DAR<>PAR.";
				KDLWarnings::ToString(KDLWarnings::TranscoderLimitation, $this->_id);
			return true;
		}

		/*
		 * Non Mac transcoders should not mess up with QT/WMV/WMA
		 * 
		 */
		$qt_wmv_list = array("wmv1","wmv2","wmv3","wvc1","wmva","wma1","wma2","wmapro");
		if($source->_container && ($source->_container->_id=="qt" || $source->_container->_format=="qt")
		&& (
			($source->_video && (in_array($source->_video->_format,$qt_wmv_list)||in_array($source->_video->_id,$qt_wmv_list)))
			||($source->_audio && (in_array($source->_audio->_format,$qt_wmv_list)||in_array($source->_audio->_id,$qt_wmv_list)))
			)
		){
			$warnings[KDLConstants::VideoIndex][] = //"The transcoder (".$key.") can not process the (".$sourcePart->_id."/".$sourcePart->_format. ").";
				KDLWarnings::ToString(KDLWarnings::TranscoderFormat, $this->_id, "qt/wmv/wma");
			return true;
		}
		return false;
	}
}
	