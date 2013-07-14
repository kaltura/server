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
$cmdStr = "--extraintf logger --log-verbose=10 --verbose-objects=+all --no-sout-transcode-hurry-up -I dummy -vvv";

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

		$transcodeStr = " --sout='#transcode{";
		
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
				case KDLVideoTarget::H264M:
				case KDLVideoTarget::H264H:
					$transcodeStr .= "venc=x264{".$this->generateH264params($vid)."},vcodec=h264";
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
		
		$cmdStr .= "' vlc://quit";

//$cmdStr = "--extraintf logger --log-verbose=2 --verbose-objects=+all --no-sout-transcode-hurry-up -I dummy -vvv \"__inFileName__\" --sout \"#transcode{vcodec=h264,vb=1800,width=256,height=128,acodec=aac,ab=128,channels=2,samplerate=44100}:std{access=file,mux=mp4,dst=\"__outFileName__\"}\"  --sout-x264-profile=baseline vlc://quit";
//$cmdStr = "--extraintf logger --log-verbose=2 --verbose-objects=+all --no-sout-transcode-hurry-up -I dummy -vvv \"__inFileName__\" --start-time=100 --sout=\"#transcode{venc=x264,vcodec=h264,vb=469,width=320,height=240,fps=25,aenc=ffmpeg,acodec=aac,ab=96,samplerate=22050}:standard{access=file,mux=mp4,dst=\"__outFileName__\"}\" vlc://quit";
  
//  $cmdStr = "--extraintf logger --log-verbose=2 --verbose-objects=+all --no-sout-transcode-hurry-up -I dummy -vvv \"__inFileName__\" --sout=\"#transcode{venc=x264,vcodec=h264,,vb=469,width=320,height=240,fps=25,aenc=ffmpeg,acodec=aac,ab=96,samplerate=22050}:standard{access=file,mux=mp4,dst=\"__outFileName__\"}\" vlc://quit";
		return $cmdStr;
	}
	
	/* ---------------------------
	 * CheckConstraints
	 */
	public function CheckConstraints(KDLMediaDataSet $source, KDLFlavor $target, array &$errors=null, array &$warnings=null)
	{
	    return parent::CheckConstraints($source, $target, $errors, $warnings);
	}
	
	/* ---------------------------
	 * generateH264params
	 */
	private function generateH264params($videoObject)
	{
	$h264 = new KDLCodecH264($videoObject);
		switch($videoObject->_id) {
		case KDLVideoTarget::H264:
		case KDLVideoTarget::H264B:
			$params="profile=baseline";;
			break;
		case KDLVideoTarget::H264M:
			$params="profile=main";
			break;
		case KDLVideoTarget::H264H:				
			$params="profile=high";
			break;
		}

		$encopts = "qcomp=$h264->_qcomp,qpmin=$h264->_qmin,qpmax=$h264->_qmax,qpstep=$h264->_qdiff";
		{
			if(isset($h264->_vidBr)) {
				$encopts.= "bitrate=$h264->_vidBr:";
				if(isset($h264->_crf))	$encopts.= "crf=30:";
			}
			if(isset($h264->_subq))			$encopts.= "subme=$h264->_subq,";
			if(isset($h264->_refs))			$encopts.= "ref=$h264->_refs,";
			if(isset($h264->_bframes))		$encopts.= "bframes=$h264->_bframes,";
			if(isset($h264->_b_pyramid))	$encopts.= "bpyramid=1,";
			if(isset($h264->_weight_b))		$encopts.= "weightb=1,";
//			if(isset($h264->_threads))		$encopts.= "threads=$h264->_threads,";
			if(isset($h264->_coder) && $h264->_coder==0) $encopts.= "cabac=$h264->_coder,";
			if(isset($h264->_level))		$encopts.= "level=$h264->_level,";
//			if(isset($h264->_global_header))$encopts.= "global_header,";
			if(isset($h264->_dct8x8))		$encopts.= "8x8dct,";
			if(isset($h264->_trellis))		$encopts.= "trellis=$h264->_trellis,";
			if(isset($h264->_chroma_me))	$encopts.= "chroma-me=$h264->_chroma_me:";

			if(isset($h264->_me))			$encopts.= "me=$h264->_me,";
			if(isset($h264->_keyint_min))	$encopts.= "min-keyint=$h264->_keyint_min,";
			if(isset($h264->_me_range))		$encopts.= "merange=$h264->_me_range,";
			if(isset($h264->_sc_threshold))	$encopts.= "scenecut=$h264->_sc_threshold,";
			if(isset($h264->_i_qfactor))	$encopts.= "ipratio=$h264->_i_qfactor,";
			if(isset($h264->_bt))			$encopts.= "ratetol=$h264->_bt,";
			if(isset($h264->_maxrate))		$encopts.= "vbv-maxrate=$h264->_maxrate,";
			if(isset($h264->_bufsize))		$encopts.= "vbv-bufsize=$h264->_bufsize,";
//			if(isset($h264->_rc_eq))		$encopts.= " -rc_eq $h264->_rc_eq";
			
			if(isset($h264->_partitions)){
				$partArr = explode(",",$h264->_partitions);
				$partitions = null;
				foreach ($partArr as $p) {
					switch($p){
					case "all":
						$partitions.="all";
						break;
					case "p8x8":
						$partitions.="+p8x8";
						break;
					case "p4x4":
						$partitions.="+p4x4";
						break;
					case "b8x8":
						$partitions.="+b8x8";
						break;
					case "i8x8":
						$partitions.="+i8x8";
						break;
					case "i4x4":
						$partitions.="+i4x4";
						break;
					}
				}
				if(isset($partitions))	$encopts.="partitions=$partitions,";
			}
			
			if(isset($encopts))	{
				$encopts = rtrim($encopts,",");
				$params.= ",$encopts";
			}
		}
		
		
		return $params;
	}
}
	