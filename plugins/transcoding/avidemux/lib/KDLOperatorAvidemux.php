<?php
/**
 * @package plugins.avidemux
 * @subpackage lib
 */
class KDLOperatorAvidemux extends KDLOperatorBase {

    public function GenerateCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
	{
// avidemux2_cli.exe --force-alt-h264 n --autoindex --rebuild-index --nogui --force-smart --load split_810.mp4 --append split_840.mp4 --save ccc.mp4
// avidemux2_cli.exe --force-alt-h264 n --autoindex --rebuild-index --nogui --force-smart --load split_810.mp4 --append split_840.mp4 --save ccc.mp4
//avidemux2 --load input.avi --audio-process --audio-normalize --audio-resample 44100 --audio-codec MP2 --audio-bitrate 224 --output-format PS --video-process --vcd-res --video-codec VCD --save output.mpg --quit

			$cmdStr = null;
// rem ffmpeg -i <infilename> -vcodec flv   -r 25 -b 500k  -ar 22050 -ac 2 -acodec libmp3lame -f flv -t 60 -y <outfilename>
$vcodecParams = "fl";
$format = "fl";
$acodec = "libmp3lam";

		$cmdStr = "--force-alt-h264 n --autoindex --rebuild-index --nogui --force-smart --load ".KDLCmdlinePlaceholders::InFileName;
		$cmdStr.= " --video-codec x264 --save ".KDLCmdlinePlaceholders::OutFileName." --quit";
	return $cmdStr;
		if($target->_video){
			$vid = $target->_video;
			switch($vid->_id){
				case KDLVideoTarget::H263:
					$vcodecParams = "263";
					break; 
				case KDLVideoTarget::H264:
				case KDLVideoTarget::H264B:
				case KDLVideoTarget::H264M:
				case KDLVideoTarget::H264H:
					$vcodecParams = "avc1";
					break; 				
				case KDLVideoTarget::MPEG4:
					$vcodecParams = "mp4v";
					break;
				default:
					$vcodecParams="";
					break;
			}
			
			$cmdStr .= " --video=".$vcodecParams;

			$cmdStr .= ",";
			if($vid->_frameRate!==null && $vid->_frameRate>0){
				$cmdStr .= $vid->_frameRate;
			}

			$cmdStr .= ",100";
			
			if($vid->_bitRate){
				$cmdStr .= " --datarate=".round($vid->_bitRate/8);
			}
			if($vid->_gop!==null && $vid->_gop>0){
				$cmdStr .= " --keyframerate=".$vid->_gop;
			}
/*			if($vid->_width!=null && $vid->_height!=null){
				$cmdStr = $cmdStr." -s ".$vid->_width."x".$vid->_height;
			}
			if($vid->_scanType!==null && $vid->_scanType>0){ // ScanType 0:progressive, 1:interlaced
				$cmdStr = $cmdStr." -deinterlace";
			}
*/
		}
		else {
			$cmdStr .= " --video=0";
		}

		if(0 && $target->_audio) {
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
				case KDLAudioTarget::COPY:
					$acodec = "copy";
					break;
				default:
					$acodec="MAC6";
					break;
			}
			$cmdStr .= " --audio=".$acodec;
			$cmdStr .= ",";
			if($aud->_sampleRate!==null && $aud->_sampleRate>0){
				$cmdStr .= $aud->_sampleRate;
			}
			$cmdStr .= ",";  // instead of bits-per-sample
			$cmdStr .= ",";
			if($aud->_channels!==null && $aud->_channels>0){
				$cmdStr .= $aud->_channels;
			}
//			if($aud->_bitRate==null && $aud->_bitRate>0){
//				$cmdStr = $cmdStr." -ab ".$aud->_bitRate."k";
//			}
		}
		else {
			//$cmdStr .= " --audio=0";
		}
		
		if($target->_clipStart!==null && $target->_clipStart>0){
//			$cmdStr .= " --strtrtduration=".$target->_clipStart;
		}

		if($target->_clipDur!==null && $target->_clipDur>0){
			$cmdStr .= " --duration=".$target->_clipDur;
		}

		if(0 && $target->_container) {
			$cont = $target->_container;
			switch($cont->_id){
				case KDLContainerTarget::FLV:
					$format = "flv";
					break;
				case KDLContainerTarget::AVI:
				case KDLContainerTarget::MP4:
				case KDLContainerTarget::_3GP:
				case KDLContainerTarget::MOV:
				case KDLContainerTarget::MP3:
				case KDLContainerTarget::OGG:
				case KDLContainerTarget::WEBM:
					$format = $cont->_id;
					break;
				case KDLContainerTarget::WMV:
					$format = "asf";
					break;
				case KDLContainerTarget::MKV:
					$format = "matroska";
					break;
				default:
					$format = "";
					break;
			}
			$cmdStr = $cmdStr." -f ".$format;
		}
	
		if($extra)
			$cmdStr .= " ".$extra;
		
		$cmdStr .= " --replacefile -1 ".KDLCmdlinePlaceholders::OutFileName;

		return $cmdStr;
	}
}

