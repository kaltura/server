<?php
/**
 * @package plugins.vlc
 * @subpackage lib
 */
class KDLOperatorVlc extends KDLOperatorBase {
    public function __construct($id, $name=null, $sourceBlacklist=null, $targetBlacklist=null) {
    	parent::__construct($id,$name,$sourceBlacklist,$targetBlacklist);
    }

	/* ---------------------------
	 * GenerateCommandLine
	 */
    public function GenerateCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
	{
		
/*
rem %VLCEXE% -vvv "%infile%" :sout="#transcode{width=320, canvas-height=240, vcodec=mp4v, vb=768, acodec=mp4a, ab=96, channels=2}:standard{access=file,mux=mp4,url="K:\Media\vlnOut.mp4"}" vlc:quit
rem OK1   %VLCEXE% -I dummy -vvv "%infile%" --sout="#transcode{width=320, canvas-height=240, vcodec=mp4v, vb=200, acodec=mp4a, ab=64, channels=2}:standard{access=file,mux=mp4,dst="%outfile%"}"
rem ok2   %VLCEXE% -I dummy -vvv "%infile%" --sout="#transcode{width=320, canvas-height=240, vcodec=h264, vb=1000, acodec=aac, ab=64, channels=2}:standard{access=file,mux=mp4,dst="%outfile%"}"
rem %VLCEXE% -I dummy --rotate-angle=90 -vvv "%infile%" --sout="#transcode{width=320, canvas-height=240, vcodec=h264, vb=1000, acodec=aac, ab=64, channels=2}:standard{access=file,mux=mp4,dst="%outfile%"}"
rem ok3   %VLCEXE% -I dummy --vout-filter="transform" "%infile%" --sout="#transcode{vcodec=h264, vb=1000, acodec=aac, ab=64, channels=2}:standard{access=file,mux=mp4,dst="%outfile%"}"  vlc://quit
@rem --rotate-angle=-90
@rem --no-sout-transcode-hurry-up - disable auto frame drop on cpu overload
-I rc - no output 
--sout-mp4-faststart
--start-time - playback start
--run-time - playback dur
--no-video
--no-audio
--deinterlace={0 (Off), -1 (Automatic), 1 (On)}
%VLCEXE% --extraintf logger --log-verbose=2 --verbose-objects=+all --no-sout-transcode-hurry-up -I dummy -vvv "%infile%" --sout "#transcode{vcodec=h264,vb=1800,width=256,height=128,acodec=aac,ab=128,channels=2,samplerate=44100}:std{access=file,mux=mp4,dst="%outfile%"}"  --rotate-angle=90 --sout-x264-profile=main

 */
$cmdStr = "--extraintf logger --log-verbose=2 --verbose-objects=+all --no-sout-transcode-hurry-up -I dummy -vvv";

$format = "fl";
$acodec = "libmp3lam";

		if(isset($target->_inFileName)){
			$cmdStr .= " ".$target->_inFileName;
		}
		else {
			$cmdStr .= " ".KDLCmdlinePlaceholders::InFileName;
		}
		if(isset($target->_clipStart) && $target->_clipStart>0){
			$cmdStr .= " --start-time=".$target->_clipStart/1000;
		}
		
		if(isset($target->_clipDur) && $target->_clipDur>0){
			$cmdStr .= " --run-time=".$target->_clipDur/1000;
		}

$transcodeStr;
		$transcodeStr = " --sout=#transcode{";
		
$vid = $target->_video;
//$vid->_id="none";
		if(isset($vid) && $vid->_id!="none"){
			if($vid->_rotation) {
				$transcodeStr .= "vfilter=rotate{angle=-".$vid->_rotation."},";
			}
		
			switch($vid->_id){
				case KDLVideoTarget::FLV:
				case KDLVideoTarget::H263:
				case KDLVideoTarget::VP6:
					$transcodeStr .= "venc=ffmpeg,vcodec=flv";
					break; 
				case KDLVideoTarget::H264: //-qcomp 0.6 -qmin 10 -qmax 50 -qdiff 4
				case KDLVideoTarget::H264B:
					$transcodeStr .= "venc=x264{".$this->generateH264params($vid)."},vcodec=h264";
					break; 				
				case KDLVideoTarget::H264M:
					$transcodeStr .= "venc=x264{profile=main},vcodec=h264";
					break; 				
				case KDLVideoTarget::H264H:
					$transcodeStr .= "venc=x264{profile=high},vcodec=h264";
					break; 				
				case KDLVideoTarget::MPEG4:
					$transcodeStr .= "venc=ffmpeg,vcodec=mpeg4";
					break;
				case KDLVideoTarget::THEORA:
					$transcodeStr .= "venc=theora,vcodec=theora,quality=8";
					break;
				case KDLVideoTarget::WMV2:
				case KDLVideoTarget::WMV3:
				case KDLVideoTarget::WVC1A:
					$transcodeStr .= "venc=ffmpeg,vcodec=wmv2";
					break;
				case KDLVideoTarget::VP8:
					$transcodeStr .= "venc=ffmpeg,vcodec=VP80";
					break; 
//				case KDLVideoTarget::COPY:
//					$vcodecParams .= "copy";
//					break; 
			}
			
			if($vid->_bitRate){
				$transcodeStr .= ",vb=".$vid->_bitRate;
			}
			if($vid->_width!=null && $vid->_height!=null){
				$transcodeStr .= ",width=".$vid->_width.",height=".$vid->_height;
			}
			if($vid->_frameRate!==null && $vid->_frameRate>0){
				$transcodeStr .= ",fps=".$vid->_frameRate;
			}
//			if($vid->_gop!==null && $vid->_gop>0){
//				$transcodeStr .= ",keyint=".$vid->_gop;
//		}
			if($vid->_scanType!==null && $vid->_scanType>0){ // ScanType 0:progressive, 1:interlaced
				$transcodeStr .= ",deinterlace";
			}
		}
		else {
			$cmdStr .= " --novideo";
//			$transcodeStr .= "select=novideo";
		}
		
$aud = $target->_audio;
		if(isset($aud) && $aud->_id!="none") {
			switch($aud->_id){
				case KDLAudioTarget::MP3:
					$transcodeStr .= ",aenc=ffmpeg,acodec=mp3";
					break;
				case KDLAudioTarget::AAC:
					$transcodeStr .= ",aenc=ffmpeg,acodec=aac";
					break;
				case KDLAudioTarget::VORBIS:
					$transcodeStr .= ",acodec=vorb";
					break;
				case KDLAudioTarget::WMA:
					$transcodeStr .= ",aenc=ffmpeg,acodec=wma";
					break;
//				case KDLAudioTarget::COPY:
//					$acodec = "copy";
//					break;
			}
			if($aud->_bitRate!==null && $aud->_bitRate>0){
				$transcodeStr .= ",ab=".$aud->_bitRate;
			}
			if($aud->_sampleRate!==null && $aud->_sampleRate>0){
				$transcodeStr .= ",samplerate=".$aud->_sampleRate;
			}
			if($aud->_channels!==null && $aud->_channels>0){
				$transcodeStr .= ",channels=".$aud->_channels;
			}
		}
		else {
			$cmdStr .= " --noaudio";
//			$transcodeStr .= ",noaudio";
		}
		$cmdStr .= $transcodeStr."}";
		
		$cmdStr .= ":standard{access=file";
$con = $target->_container;
		if(isset($con) && $con->_id!="none") {
			switch($con->_id){
				case KDLContainerTarget::FLV:
					$format = ",mux=flv";
					break;
				case KDLContainerTarget::AVI:
				case KDLContainerTarget::_3GP:
				case KDLContainerTarget::MOV:
				case KDLContainerTarget::MP3:
				case KDLContainerTarget::OGG:
					$format = ",mux=".$con->_id;
					break;
				case KDLContainerTarget::MP4:
					$format = ",mux=mp4{faststart}";
					break;
				case KDLContainerTarget::WMV:
					$format = ",mux=asf";
					break;
				case KDLContainerTarget::MKV:
					$format = ",mux=ffmpeg{mux=matroska}";
					break;
				case KDLContainerTarget::WEBM:
					$format = ",mux=ffmpeg{mux=webm}";
					break;
				case KDLContainerTarget::MPEGTS:
				case KDLContainerTarget::APPLEHTTP:
					$format = ",mux=mpegts";
					break;
				case KDLContainerTarget::MPEG:
					$format = ",mux=mpeg";
					break;
			}
			$cmdStr .= $format;
		}
		
		if(isset($target->_outFileName)){
			$cmdStr .= ",dst=".$target->_outFileName."}";
		}
		else {
			$cmdStr .= ",dst=".KDLCmdlinePlaceholders::OutFileName."}";
		}
		if($extra)
			$cmdStr .= " ".$extra;
		
		$cmdStr .= " vlc://quit";

//$cmdStr = "--extraintf logger --log-verbose=2 --verbose-objects=+all --no-sout-transcode-hurry-up -I dummy -vvv \"__inFileName__\" --sout \"#transcode{vcodec=h264,vb=1800,width=256,height=128,acodec=aac,ab=128,channels=2,samplerate=44100}:std{access=file,mux=mp4,dst=\"__outFileName__\"}\"  --sout-x264-profile=baseline vlc://quit";
//$cmdStr = "--extraintf logger --log-verbose=2 --verbose-objects=+all --no-sout-transcode-hurry-up -I dummy -vvv \"__inFileName__\" --start-time=100 --sout=\"#transcode{venc=x264,vcodec=h264,vb=469,width=320,height=240,fps=25,aenc=ffmpeg,acodec=aac,ab=96,samplerate=22050}:standard{access=file,mux=mp4,dst=\"__outFileName__\"}\" vlc://quit";
  
//  $cmdStr = "--extraintf logger --log-verbose=2 --verbose-objects=+all --no-sout-transcode-hurry-up -I dummy -vvv \"__inFileName__\" --sout=\"#transcode{venc=x264,vcodec=h264,,vb=469,width=320,height=240,fps=25,aenc=ffmpeg,acodec=aac,ab=96,samplerate=22050}:standard{access=file,mux=mp4,dst=\"__outFileName__\"}\" vlc://quit";
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
		$ffQsettings = "qcomp=0.6,qpmin=10,qpmax=50,qpstep=4";
		//					$transcodeStr .= "venc=x264{profile=baseline,subme=2,qcomp=0.6,qpmin=10,qpmax=50,qpstep=4},vcodec=h264";
		
		switch($videoObject->_id) {
		case KDLVideoTarget::H264:
		case KDLVideoTarget::H264B:
			$h264params="profile=baseline,subme=2,".$ffQsettings.",no-cabac";;
			break;
		case KDLVideoTarget::H264M:
			$h264params="profile=main,subme=5,".$ffQsettings.",ref=2";
			break;
		case KDLVideoTarget::H264H:				
			$h264params="profile=high,subme=7,".$ffQsettings.",bframes=16,ref=6";
			break;
		default:
			return null;
		}
		if($videoObject->_bitRate<KDLConstants::LowBitrateThresHold) {
			$h264params .= ",crf=30";
		}
		if(1) {//$videoObject->_gop!==null && $videoObject->_gop>0){
			$h264params .= ",keyint=".$videoObject->_gop.",min-keyint=".$videoObject->_gop;
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
	