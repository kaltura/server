<?php
/**
 * @package plugins.ffmpeg
 * @subpackage lib
 */
class KDLOperatorFfmpeg extends KDLOperatorBase {
    public function __construct($id, $name=null, $sourceBlacklist=null, $targetBlacklist=null) {
    	parent::__construct($id,$name,$sourceBlacklist,$targetBlacklist);
    }

	/* ---------------------------
	 * GenerateCommandLine
	 */
    public function GenerateCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
	{
		
	$cmdStr = null;
// rem ffmpeg -i <infilename> -vcodec flv   -r 25 -b 500k  -ar 22050 -ac 2 -acodec libmp3lame -f flv -t 60 -y <outfilename>
$vcodecParams = "fl";
$format = "fl";
$acodec = "libmp3lam";

		if(isset($target->_clipStart) && $target->_clipStart>0){
			$cmdStr .= " -ss ".$target->_clipStart/1000;
		}
		
		if(isset($target->_clipDur) && $target->_clipDur>0){
			$cmdStr .= " -t ".$target->_clipDur/1000;
		}
		
		if(isset($target->_inFileName)){
			$cmdStr .= " -i ".$target->_inFileName;
		}

$vid = $target->_video;
		if($vid->_id!="none"){
			switch($vid->_id){
				case KDLVideoTarget::FLV:
				case KDLVideoTarget::H263:
				case KDLVideoTarget::VP6:
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
				case KDLVideoTarget::COPY:
					$vcodecParams = "copy";
					break; 
			}
			
			$cmdStr = $cmdStr." -vcodec ".$vcodecParams;
			if($vid->_bitRate){
				$cmdStr .= " -b ".$vid->_bitRate."k";
			}
			if($vid->_width!=null && $vid->_height!=null){
				$cmdStr .= " -s ".$vid->_width."x".$vid->_height;
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
		}
		else {
			$cmdStr .= " -vn";
		}
		
$aud = $target->_audio;
		if($aud->_id!="none") {
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
				case KDLAudioTarget::COPY:
					$acodec = "copy";
					break;
			}
			$cmdStr .= " -acodec ".$acodec;
			if($aud->_bitRate!==null && $aud->_bitRate>0){
				$cmdStr .= " -ab ".$aud->_bitRate."k";
			}
			if($aud->_sampleRate!==null && $aud->_sampleRate>0){
				$cmdStr .= " -ar ".$aud->_sampleRate;
			}
			if($aud->_channels!==null && $aud->_channels>0){
				$cmdStr .= " -ac ".$$aud->_channels;
			}
		}
		else {
			$cmdStr .= " -an";
		}

$con = $target->_container;
		if($con->_id!="none") {
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
			$cmdStr .= " -f ".$format;
		}
		
		/*
		 * Following 'dummy' seek-to setting is done to ensure preciseness
		 * of the main seek command that is done at the beginning o fthe command line
		 */
		if(isset($target->_clipStart) && $target->_clipStart>0){
			$cmdStr .= " -ss 0.01";
		}
		
		if($extra)
			$cmdStr .= " ".$extra;
		
		if(isset($target->_outFileName)){
			$cmdStr .= " -y ".$target->_outFileName;
		}
		return $cmdStr;
	}
	
	/* ---------------------------
	 * generateH264params
	 */
	private function generateH264params($videoObject)
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
	    return parent::CheckConstraints($source, $target, $errors, $warnings);
	}
}
	