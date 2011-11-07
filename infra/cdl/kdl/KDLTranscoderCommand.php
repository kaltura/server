<?php

include_once("KDLCommon.php");
include_once("KDLMediaDataSet.php");
include_once("KDLFlavor.php");
include_once("KDLOperatorBase.php");

	/* ===========================
	 * KDLOperatorWrapper
	 */
class KDLOperatorWrapper extends KDLOperatorBase {
    public function __construct($id, $name=null, $sourceBlacklist=null, $targetBlacklist=null) {
    	$srcBlacklist = $sourceBlacklist;
		if(is_null($sourceBlacklist) && array_key_exists($id, KDLConstants::$TranscodersSourceBlackList)) {
			$srcBlacklist = KDLConstants::$TranscodersSourceBlackList[$id];
		}
		$trgBlacklist = $targetBlacklist;
		if(is_null($targetBlacklist) && array_key_exists($id, KDLConstants::$TranscodersTargetBlackList)) {
			$trgBlacklist = KDLConstants::$TranscodersTargetBlackList[$id];
		}
    	parent::__construct($id,$name,$srcBlacklist,$trgBlacklist);
    }

	public function GenerateCommandLine(KDLFlavor $predesign, KDLFlavor $target, $extra=null)
	{
		$cmdLineGenerator = $target->SetTranscoderCmdLineGenerator();

// The setting below seems to be redundant, since in the prev line the same vidBr is being set
//		if($target->_video)
//			$cmdLineGenerator->_vidBr = $target->_video->_bitRate;
		
		$params = new KDLOperationParams();
		$params->Set($this->_id, $extra);
		return $cmdLineGenerator->Generate($params, $predesign->_video->_bitRate);
	}
	
    /* ---------------------------
	 * CheckConstraints
	 */
	public function CheckConstraints(KDLMediaDataSet $source, KDLFlavor $target, array &$errors=null, array &$warnings=null)
	{
//		if(array_key_exists($this->_id, KDLConstants::$TranscodersSourceBlackList)) {
//			$this->_sourceBlacklist = KDLConstants::$TranscodersSourceBlackList[$this->_id];
//		}
//		if(array_key_exists($this->_id, KDLConstants::$TranscodersTargetBlackList)) {
//			$this->_targetBlacklist = KDLConstants::$TranscodersTargetBlackList[$this->_id];
//		}
		if(parent::CheckConstraints($source, $target, $errors, $warnings)==true)
			return true;
			
		/*
		 * Remove encoding.com for DAR<>PAR
		 */
		if($this->_id==KDLTranscoders::ENCODING_COM
		&& $source->_video && $source->_video->_dar
		&& abs($source->_video->GetPAR()-$source->_video->_dar)>0.01) {
			$warnings[KDLConstants::VideoIndex][] = //"The transcoder (".$key.") can not process the (".$sourcePart->_id."/".$sourcePart->_format. ").";
				KDLWarnings::ToString(KDLWarnings::TranscoderFormat, $this->_id, "non square pixels");
			return true;
		}
			
		/*
		 * Remove mencoder, encoding.com and cli_encode
		 * for audio only flavors
		 */
		if(($this->_id==KDLTranscoders::MENCODER || $this->_id==KDLTranscoders::ENCODING_COM || $this->_id==KDLTranscoders::ON2)
		&& $target->_video==null) {
			$warnings[KDLConstants::AudioIndex][] = //"The transcoder (".$key.") does not handle properly DAR<>PAR.";
				KDLWarnings::ToString(KDLWarnings::TranscoderLimitation, $this->_id);
			return true;
		}

		/*
		 * Remove encoding.com and ffmpegs
		 * for rotated videos
		 */
		if(($this->_id==KDLTranscoders::ENCODING_COM || $this->_id==KDLTranscoders::FFMPEG || $this->_id==KDLTranscoders::FFMPEG_AUX)
		&& $target->_video && $target->_video->_rotation) {
			$warnings[KDLConstants::VideoIndex][] = //"The transcoder (".$key.") does not handle properly DAR<>PAR.";
				KDLWarnings::ToString(KDLWarnings::TranscoderLimitation, $this->_id);
			return true;
		}
		
		/*
		 * Non Mac transcoders should not mess up with QT/WMV/WMA
		 * 
		 */
		$qt_wmv_list = array("wmv1","wmv2","wmv3","wvc1","wmva","wma1","wma2","wmapro");
		if((# $this->_id==KDLTranscoders::ENCODING_COM || $this->_id==KDLTranscoders::MENCODER || $this->_id==KDLTranscoders::ON2 || 
			$this->_id==KDLTranscoders::FFMPEG || $this->_id==KDLTranscoders::FFMPEG_AUX)
		&& $source->_container && ($source->_container->_id=="qt" || $source->_container->_format=="qt")
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


	/* ===========================
	 * KDLTranscoderCommand
	 */
class KDLTranscoderCommand {
	
			private $_target;
			
			private $_vidId;
			private $_vidBr;
			private $_vidWid;
			private $_vidHgt;
			private $_vidFr;
			private $_vidGop;
			private $_vid2pass;
			private $_vidRotation;
			private $_vidScanType;
			
			private $_audId;
			private $_audBr; 
			private $_audCh;
			private $_audSr;
			
			private $_conId;
			
			private $_inFileName=KDLCmdlinePlaceholders::InFileName;
			private $_outFileName=KDLCmdlinePlaceholders::OutFileName;
			private $_clipStart=null;
			private $_clipDur=null;
			
	public function KDLTranscoderCommand($inFileName=KDLCmdlinePlaceholders::InFileName, $outFileName=KDLCmdlinePlaceholders::OutFileName, KDLFlavor $target)
	{
		$this->_inFileName=$inFileName;
		$this->_outFileName=$outFileName;
		$this->_target = $target;
		$this->setParameters($target);
	}
	
	/* ---------------------------
	 * setParameters
	 */
	private function setParameters(KDLFlavor $target)
	{
		if($target->_video){
			$this->_vidId = $target->_video->_id;
			$this->_vidBr = $target->_video->_bitRate;
			$this->_vidWid = $target->_video->_width;
			$this->_vidHgt = $target->_video->_height;
			$this->_vidFr = $target->_video->_frameRate;
			$this->_vidGop = $target->_video->_gop;
			$this->_vid2pass = $target->_isTwoPass;
			$this->_vidRotation = $target->_video->_rotation;
			$this->_vidScanType = $target->_video->_scanType;
		}
		else
			$this->_vidId="none";
			
		if($target->_audio){
			$this->_audId = $target->_audio->_id;
			$this->_audBr = $target->_audio->_bitRate;
			$this->_audCh = $target->_audio->_channels;
			$this->_audSr = $target->_audio->_sampleRate;
		}
		else
			$this->_audId="none";
			
		if($target->_container){
			$this->_conId = $target->_container->_id;
		}
		else
			$this->_conId="none";
			
		$this->_clipStart=$target->_clipStart;
		$this->_clipDur=$target->_clipDur;
	}
	
	/* ---------------------------
	 * Generate
	 */
	public function Generate(KDLOperationParams $transParams, $maxVidRate)
	{
		$cmd=null;
		switch($transParams->_id){
			case KDLTranscoders::KALTURA:
				$cmd=$transParams->_id;
				break;
			case KDLTranscoders::ON2:
				$cmd=$this->CLI_Encode($transParams->_extra);;
				break;
			case KDLTranscoders::FFMPEG:
				$this->fixVP6BitRate($maxVidRate);
				$cmd=$this->FFMpeg($transParams->_extra);
				break;
			case KDLTranscoders::MENCODER:
				$this->fixVP6BitRate($maxVidRate);
				$cmd=$this->Mencoder($transParams->_extra);
				break;
			case KDLTranscoders::ENCODING_COM:
				$cmd=$transParams->_id;
				break;
			case KDLTranscoders::FFMPEG_AUX:
			case KDLTranscoders::FFMPEG_VP8:
				$this->fixVP6BitRate($maxVidRate);
				$cmd=$this->FFMpeg_aux($transParams->_extra);
				break;
			case KDLTranscoders::EE3:
				$cmd=$this->EE3($transParams->_extra);
				break;
		}
		return $cmd;
	}
	
	/* ---------------------------
	 * FFMpeg
	 */
	public function FFMpeg($extra=null)
	{
	$cmdStr = null;
// rem ffmpeg -i <infilename> -vcodec flv   -r 25 -b 500k  -ar 22050 -ac 2 -acodec libmp3lame -f flv -t 60 -y <outfilename>
$vcodecParams = "fl";
$format = "fl";
$acodec = "libmp3lam";

		if($this->_clipStart!==null && $this->_clipStart>0){
			$cmdStr = $cmdStr." -ss ".$this->_clipStart/1000;
		}
		
		if($this->_clipDur!==null && $this->_clipDur>0){
			$cmdStr = $cmdStr." -t ".$this->_clipDur/1000;
		}
		
		if($this->_inFileName){
			$cmdStr .= " -i ".$this->_inFileName;
		}
		if($this->_vidId!="none"){
			switch($this->_vidId){
				case KDLVideoTarget::FLV:
				case KDLVideoTarget::H263:
				case KDLVideoTarget::VP6:
					$vcodecParams = "flv";
					break; 
				case KDLVideoTarget::H264:
				case KDLVideoTarget::H264B:
				case KDLVideoTarget::H264M:
				case KDLVideoTarget::H264H:
					$vcodecParams = $this->generateH264params(KDLTranscoders::FFMPEG);
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
$vidObj = $this->_target->_video;
			$cmdStr = $cmdStr." -vcodec ".$vcodecParams;
			if($this->_vidBr){
				$cmdStr = $cmdStr." -b ".$this->_vidBr."k";
			}
$bt=0;
			if(isset($vidObj->_cbr) && $vidObj->_cbr>0) {
				$bt = round($this->_vidBr/10);
				$cmdStr.= " -minrate ".$this->_vidBr."k";
				$cmdStr.= " -maxrate ".$this->_vidBr."k";
				$cmdStr.= " -bufsize ".round($this->_vidBr/5)."k";
			}
			if(isset($vidObj->_bt) && $vidObj->_bt>0) {
				$cmdStr.= " -bt ".$vidObj->_bt."k";
			}
			else if($bt>0){
				$cmdStr.= " -bt $bt"."k";
			}
			if($this->_vidWid!=null && $this->_vidHgt!=null){
				$cmdStr = $cmdStr." -s ".$this->_vidWid."x".$this->_vidHgt;
			}
			if($this->_vidFr!==null && $this->_vidFr>0){
				$cmdStr = $cmdStr." -r ".$this->_vidFr;
			}
			if($this->_vidGop!==null && $this->_vidGop>0){
				$cmdStr = $cmdStr." -g ".$this->_vidGop;
			}
			if($this->_vidScanType!==null && $this->_vidScanType>0){ // ScanType 0:progressive, 1:interlaced
				$cmdStr = $cmdStr." -deinterlace";
			}
		}
		else {
			$cmdStr = $cmdStr." -vn";
		}
		
		if($this->_audId!="none") {
			switch($this->_audId){
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
			$cmdStr = $cmdStr." -acodec ".$acodec;
			if($this->_audBr!==null && $this->_audBr>0){
				$cmdStr = $cmdStr." -ab ".$this->_audBr."k";
			}
			if($this->_audSr!==null && $this->_audSr>0){
				$cmdStr = $cmdStr." -ar ".$this->_audSr;
			}
			if($this->_audCh!==null && $this->_audCh>0){
				$cmdStr = $cmdStr." -ac ".$this->_audCh;
			}
		}
		else {
			$cmdStr = $cmdStr." -an";
		}
		
		if($this->_conId!="none") {
			switch($this->_conId){
				case KDLContainerTarget::FLV:
					$format = "flv";
					break;
				case KDLContainerTarget::AVI:
				case KDLContainerTarget::MP4:
				case KDLContainerTarget::_3GP:
				case KDLContainerTarget::MOV:
				case KDLContainerTarget::MP3:
				case KDLContainerTarget::OGG:
					$format = $this->_conId;
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
			$cmdStr = $cmdStr." -f ".$format;
		}
		
		/*
		 * Following 'dummy' seek-to setting is done to ensure preciseness
		 * of the main seek command that is done at the beginning o fthe command line
		 */
		if($this->_clipStart!==null && $this->_clipStart>0){
			$cmdStr = $cmdStr." -ss 0.01";
		}
		
		if($extra)
			$cmdStr = $cmdStr." ".$extra;
		
		if($this->_outFileName)
			$cmdStr = $cmdStr." -y ".$this->_outFileName;

		return $cmdStr;
	}

	/* ---------------------------
	 * Mencoder
	 */
	public function Mencoder($extra=null)
	{
	$cmdStr = null;
// mencoder <_INPUT_FILE_> -of lavf -ofps <_TRG_FR_> -oac mp3lame -srate <_TRG_ASAMPLE_RATE_> -ovc lavc -lavcopts vcodec=flv:vbitrate=<_TRG_BR_>:mbd=2:mv0:trell:v4mv:cbp:last_pred=3:keyint=100 –vf harddup -o <_TARGET_FILE_>
// mencoder 5.flv          -of lavf -lavfopts format=avi -ovc lavc -ofps 30 -lavcopts vcodec=x264:vbitrate=806:mbd=2:mv0:trell:v4mv:cbp:last_pred=3:keyint=60 -vf harddup,scale=640:352 -oac faac -faacopts mpeg=4:object=2:br=96 -srate 44100 -endpos 2 -o aaa1.flv
// mencoder $1             -of lavf -lavfopts format=avi -ovc x264 -ofps 25 -x264encopts bitrate=500:subq=5:8x8dct:frameref=2:bframes=3:b_pyramid:weight_b:threads=auto -vf scale=1280:720,harddup -nosound -endpos 4 -o $outputFile
//BASELINE mencoder32 ~/Media/Canon.Rotated.0_qaqsufbl.avi -o ~/Media/aaa.mp4 -of lavf -lavfopts format=mp4 -ofps 25 -ovc x264 -sws 9 -x264encopts bitrate=300:subq=5:frameref=6:bframes=0:threads=auto:keyint=60:nocabac:level_idc=30:global_header:partitions=all:trellis=1:chroma_me:me=umh -vf scale=304:176 -oac faac -faacopts mpeg=4:object=2:br=96 ; mediainfo ~/Media/aaa.mp4
//MAINLINE mencoder32 ~/Media/Canon.Rotated.0_qaqsufbl.avi -o ~/Media/aaa.mp4 -of lavf -lavfopts format=mp4 -ofps 25 -ovc x264 -sws 9 -x264encopts bitrate=300:subq=5:frameref=6:bframes=3:threads=auto:keyint=60:level_idc=30:partitions=all:trellis=1:chroma_me:me=umh -vf scale=304:176 -oac faac -faacopts mpeg=4:object=2:br=96 ; mediainfo ~/Media/aaa.mp4
//HIGH     mencoder32 ~/Media/Canon.Rotated.0_qaqsufbl.avi -o ~/Media/aaa.mp4 -of lavf -lavfopts format=mp4 -ofps 25 -ovc x264 -sws 9 -x264encopts bitrate=300:subq=7:frameref=6:bframes=3:b_pyramid=1:weight_b=1:threads=auto:keyint=60:level_idc=30:8x8dct:trellis=1:chroma_me:me=umh -vf scale=304:176 -oac faac -faacopts mpeg=4:object=2:br=96 ; mediainfo ~/Media/aaa.mp4
	
$vcodec = "fl";
$format = "fl";

		if($this->_inFileName)
			$cmdStr = $cmdStr." ".$this->_inFileName;
		
		if($this->_conId!="none") {
			switch($this->_conId){
				case KDLContainerTarget::WMV:
					$format = "asf";
					break;
				default:
					$format = $this->_conId;
					break;
			}
			
			// This will not work for mp4 - TO FIX
			$cmdStr = $cmdStr." -of lavf -lavfopts format=".$format;
		}
		
		if($this->_vidId!="none"){
			if($this->_vidFr)
				$cmdStr = $cmdStr." -ofps ".$this->_vidFr;
			
			switch($this->_vidId){
				case KDLVideoTarget::FLV:
				case KDLVideoTarget::H263:
				case KDLVideoTarget::VP6:
					$cmdStr = $cmdStr." -ovc lavc";
					$cmdStr = $cmdStr." -lavcopts vcodec=flv";
					if($this->_vidBr) {
						$cmdStr = $cmdStr.":vbitrate=".$this->_vidBr;
					}
					$cmdStr = $cmdStr.":mbd=2:mv0:trell:v4mv:cbp:last_pred=3";
//					$cmdStr = $cmdStr.":mbd=2:mv0:trell:v4mv:cbp";
					break;
				case KDLVideoTarget::H264:
				case KDLVideoTarget::H264B:
				case KDLVideoTarget::H264M:
				case KDLVideoTarget::H264H:
					$cmdStr .= $this->generateH264params(KDLTranscoders::MENCODER);
					break; 				
				case KDLVideoTarget::MPEG4:
					$cmdStr = $cmdStr." -ovc lavc";
					$cmdStr = $cmdStr." -lavcopts vcodec=mpeg4";
					if($this->_vidBr) {
						$cmdStr = $cmdStr.":vbitrate=".$this->_vidBr;
					}
					break;
				case KDLVideoTarget::WMV2:
				case KDLVideoTarget::WMV3:
				case KDLVideoTarget::WVC1A:
					$cmdStr = $cmdStr." -ovc lavc";
					$cmdStr = $cmdStr." -lavcopts vcodec=wmv2";
					if($this->_vidBr) {
						$cmdStr = $cmdStr.":vbitrate=".$this->_vidBr;
					}
					break;
			}

			if($this->_vidGop!==null && $this->_vidGop>0)
				$cmdStr = $cmdStr.":keyint=".$this->_vidGop;
			$cmdStr = $cmdStr." -vf harddup";
			if($this->_vidWid && $this->_vidHgt)
				$cmdStr = $cmdStr.",scale=".$this->_vidWid.":".$this->_vidHgt;
			if($this->_vidScanType!==null && $this->_vidScanType>0) // ScanType 0:progressive, 1:interlaced
				$cmdStr = $cmdStr.",yadif=3,mcdeint,framestep=2";
			if(isset($this->_vidRotation)) {
				if($this->_vidRotation==180)
					$cmdStr = $cmdStr.",flip";
				else if($this->_vidRotation==90 || $this->_vidRotation==-90)
					$cmdStr = $cmdStr.",rotate=1";
				
			}
		}
		else {
			$cmdStr = $cmdStr." -novideo";
		}

// mencoder <_INPUT_FILE_> -of lavf -ofps <_TRG_FR_> -ovc lavc -lavcopts vcodec=flv:vbitrate=<_TRG_BR_>:mbd=2:mv0:trell:v4mv:cbp:last_pred=3:keyint=100 –vf harddup 
//-oac mp3lame -srate <_TRG_ASAMPLE_RATE_> -o <_TARGET_FILE_>

		if($this->_audId!="none") {
			if($this->_audId==KDLAudioTarget::MP3){
				$cmdStr = $cmdStr." -oac mp3lame -lameopts abr";
				if($this->_audBr)
					$cmdStr = $cmdStr.":br=".$this->_audBr;
//				if($aud->_channels)
//					$cmdStr = $cmdStr." -ac ".$aud->_channels;
			}
///web/kaltura/bin/x64/mencoder -endpos 2 $1 -of lavf -lavfopts format=avi -ovc x264 -ofps 25 -x264encopts bitrate=500 -vf scale=1280:720,harddup -oac faac -srate 48000 -channels 5 -faacopts mpeg=4:object=2:br=32 -o $outputFile
			else if($this->_audId==KDLAudioTarget::AAC){
				$cmdStr = $cmdStr." -oac faac -faacopts mpeg=4:object=2:tns:raw";
				if($this->_audBr)
					$cmdStr = $cmdStr.":br=".$this->_audBr;
			}
			else if($this->_audId==KDLAudioTarget::WMA){
				$cmdStr = $cmdStr." -oac lavc -lavcopts acodec=wmav2";
				if($this->_audBr)
					$cmdStr = $cmdStr.":abitrate=".$this->_audBr;
			}

			if($this->_audSr)
				$cmdStr = $cmdStr." -srate ".$this->_audSr;
		}
		else {
			$cmdStr = $cmdStr." -nosound";
		}
		
		$clipStart=0;
		if($this->_clipStart!==null && $this->_clipStart>0){
			$clipStart = $this->_clipStart;
			$cmdStr = $cmdStr." -ss ".$clipStart/1000;
		}
		
		if($this->_clipDur!==null && $this->_clipDur>0){
			$cmdStr = $cmdStr." -endpos ".($clipStart+$this->_clipDur)/1000;
		}
		
		if($extra)
			$cmdStr = $cmdStr." ".$extra;
		
		if($this->_outFileName)
			$cmdStr = $cmdStr." -o ".$this->_outFileName;

		return $cmdStr;
	}

	/* ---------------------------
	 * CLI_Encode
	 */
	public function CLI_Encode($extra=null)
	{
	$cmdStr = null;
//cli_encode -i $1 -r 25 -b 1200 -k 100 --FE2_VP6_CXMODE=1 --FE2_VP6_RC_MODE=3 --FE2_CUT_STOP_SEC=5 -o $outputFile 

		if($this->_inFileName)
			$cmdStr = $cmdStr."-i ".$this->_inFileName;
		
		if($this->_conId!="none") {
			//$format = $con->_id;
			// This will not work for mp4 - TO FIX
			//$cmdStr = $cmdStr." -of lavf -lavfopts format=".$format;
		}

		if($this->_vidId!="none"){
			switch($this->_vidId){
				case KDLVideoTarget::H264:
				case KDLVideoTarget::H264B:
				case KDLVideoTarget::H264M:
				case KDLVideoTarget::H264H:
					$cmdStr .= $this->generateH264params(KDLTranscoders::ON2);
					break; 				
/*
				case KDLVideoTarget::H264:
					$cmdStr = $cmdStr." -c H264";;
					break;
				case KDLVideoTarget::H264B:
					$cmdStr = $cmdStr." -c H264 --FE2_H264_PROFILE=0";;
					break;
				case KDLVideoTarget::H264M:
					$cmdStr = $cmdStr." -c H264 --FE2_H264_PROFILE=1";;
					break;
				case KDLVideoTarget::H264H:				
					$cmdStr = $cmdStr." -c H264 --FE2_H264_PROFILE=2";;
					break;
*/
				case KDLVideoTarget::VP6:
				case KDLVideoTarget::H263:
					default:
					$cmdStr = $cmdStr." -c VP6";
					break;
			}

			if($this->_vidFr)
				$cmdStr = $cmdStr." -r ".round($this->_vidFr);
			
			if($this->_vidBr)
				$cmdStr = $cmdStr." -b ".$this->_vidBr;
			if($this->_vidGop!==null && $this->_vidGop>0) 
				$cmdStr = $cmdStr." -k ".$this->_vidGop;
			if($this->_vidWid && $this->_vidHgt){
				if(is_null($this->_vidRotation) || $this->_vidRotation==0  || $this->_vidRotation==180)
					$cmdStr = $cmdStr." -w ".$this->_vidWid." -h ".$this->_vidHgt;
				else
					$cmdStr = $cmdStr." -h ".$this->_vidWid." -w ".$this->_vidHgt;
			}
			if($this->_vidScanType!==null && $this->_vidScanType>0){ // ScanType 0:progressive, 1:interlaced
				$cmdStr .= " --deinterlace=1";
			}
		}
		
		if($this->_audId!="none") {
			if($this->_audBr!==null)
				$cmdStr = $cmdStr." -a ".$this->_audBr;
			if($this->_audSr)
				$cmdStr = $cmdStr." -s ".$this->_audSr;
		}
		
		if($this->_vid2pass==true)
			$cmdStr = $cmdStr." --FE2_VP6_CXMODE=1 --FE2_VP6_RC_MODE=3";
		
			if($this->_clipStart!==null && $this->_clipStart>0){
			$cmdStr = $cmdStr." --FE2_CUT_START_SEC=".$this->_clipStart/1000;
		}
		
		if($this->_clipDur!==null && $this->_clipDur>0){
			$cmdStr = $cmdStr." --FE2_CUT_STOP_SEC=".$this->_clipDur/1000;
		}
		
		if($extra)
			$cmdStr = $cmdStr." ".$extra;
		
		if($this->_outFileName)
			$cmdStr = $cmdStr." -o ".$this->_outFileName;

		return $cmdStr;
	}
	
	/* ---------------------------
	 * Encoding_com
	 */
	public function Encoding_com($extra=null)
	{
		return $this->CLI_Encode($extra);
	}

	/* ---------------------------
	 * FFMpeg_aux
	 */
	public function FFMpeg_aux($extra=null)
	{
		return $this->FFMpeg($extra);
	}

	/* ---------------------------
	 * fixVP6BitRate
	 */
	private function fixVP6BitRate($maxVidRate)
	{
		if($this->_vidBr){
			if($this->_vidId==KDLVideoTarget::VP6){
				$this->_vidBr = round($this->_vidBr*KDLConstants::BitrateVP6Factor);
			}
			if($this->_vidBr>$maxVidRate){
				$this->_vidBr=$maxVidRate;
			}
		}
	}

	/* ---------------------------
	 * EE3
	 */
	public function EE3($extra=null)
	{
/*		
$tryXML = "<StreamInfo
                Size=\"512, 384\">
                <Bitrate>
                  <ConstantBitrate
                    Bitrate=\"1045\"
                    IsTwoPass=\"False\"
                    BufferWindow=\"00:00:05\" />
                </Bitrate>
              </StreamInfo>
";
		$xml = new SimpleXMLElement($tryXML);
*/
		if($this->_conId!="none") {
			$pinfo = pathinfo(__FILE__);
			$dir = $pinfo['dirname'];
			switch($this->_conId){
				case KDLContainerTarget::ISMV:
					$xmlTemplate = $dir.'/ismPresetTemplate.xml';
					break;
				case KDLContainerTarget::MP4:
				case KDLContainerTarget::WMV:
				default:
					$xmlTemplate = $dir.'/wmvPresetTemplate.xml';
					break;
			}
			$xml = simplexml_load_file($xmlTemplate);
		}
		
		$xml->Job['OutputDirectory']=KDLCmdlinePlaceholders::OutDir;
		if($this->_inFileName){
			$xml->Job['DefaultMediaOutputFileName']=$this->_outFileName.".{DefaultExtension}";
		}
		if($this->_vidId!="none"){
$vidProfile=null;
			switch($this->_vidId){
				case KDLVideoTarget::WMV2:
				case KDLVideoTarget::WMV3:
				case KDLVideoTarget::WVC1A:
				default:
					$vidProfile = $xml->MediaFile->OutputFormat->WindowsMediaOutputFormat->VideoProfile->AdvancedVC1VideoProfile;
					unset($xml->MediaFile->OutputFormat->WindowsMediaOutputFormat->VideoProfile->MainH264VideoProfile);					
					break;
				case KDLVideoTarget::H264:
				case KDLVideoTarget::H264B:
				case KDLVideoTarget::H264M:
				case KDLVideoTarget::H264H:				
					$vidProfile = $xml->MediaFile->OutputFormat->WindowsMediaOutputFormat->VideoProfile->MainH264VideoProfile;
					unset($xml->MediaFile->OutputFormat->WindowsMediaOutputFormat->VideoProfile->AdvancedVC1VideoProfile);					
					break;
			}
			$vFr = 30;
			if($this->_vidFr!==null && $this->_vidFr>0){
				$vFr = $this->_vidFr;
				$vidProfile['FrameRate']=$this->_vidFr;
			}
			if($this->_vidGop!==null && $this->_vidGop>0){
				$kFr = round($this->_vidGop/$vFr);
				$mi = round($kFr/60);
				$se = $kFr%60;
				$vidProfile['KeyFrameDistance']=sprintf("00:%02d:%02d",$mi,$se);
			}
			if($this->_vidBr){
				$this->_vidBr=max(100,$this->_vidBr); // The minimum video br for the SL is 100
				$vidProfile->Streams->StreamInfo->Bitrate->VariableConstrainedBitrate['PeakBitrate'] = round($this->_vidBr*1.3);
				$vidProfile->Streams->StreamInfo->Bitrate->VariableConstrainedBitrate['AverageBitrate'] = $this->_vidBr;
			}
			if($this->_vidWid!=null && $this->_vidHgt!=null){
				$vidProfile->Streams->StreamInfo['Size'] = $this->_vidWid.", ".$this->_vidHgt;
			}
			
//			$strmInfo = clone ($vidProfile->Streams->StreamInfo[0]);
//			KDLUtils::AddXMLElement($vidProfile->Streams, $vidProfile->Streams->StreamInfo[0]);
			
		}
		else {
			unset($xml->MediaFile->OutputFormat->WindowsMediaOutputFormat->VideoProfile);				
		}

		if($this->_audId!="none"){
$audProfile=null;
			switch($this->_audId){
				case KDLAudioTarget::WMA:
				default:
					$audProfile = $xml->MediaFile->OutputFormat->WindowsMediaOutputFormat->AudioProfile->WmaAudioProfile;
					unset($xml->MediaFile->OutputFormat->WindowsMediaOutputFormat->AudioProfile->AacAudioProfile);					
					break;
				case KDLAudioTarget::AAC:
					$audProfile = $xml->MediaFile->OutputFormat->WindowsMediaOutputFormat->AudioProfile->AacAudioProfile;
					unset($xml->MediaFile->OutputFormat->WindowsMediaOutputFormat->AudioProfile->WmaAudioProfile);					
					break;
			}
/*
	Since there are certain constraints on those values for the EE3 presets, 
	those values are set in the templates only
	
			if($this->_audBr!==null && $this->_audBr>0){
				$audProfile->Bitrate->ConstantBitrate['Bitrate'] = $this->_audBr;
			}
			if($this->_audSr!==null && $this->_audSr>0){
				$audProfile['SamplesPerSecond'] = $this->_audSr;
			}
			if($this->_audCh!==null && $this->_audCh>0){
				$audProfile['Channels'] = $this->_audCh;
			}
*/
		}
//$stream = clone $streams->StreamInfo;
//		$streams[1] = $stream;
		//		print_r($xml);
		return $xml->asXML();
	}

	/* ---------------------------
	 * generateH264params
	 */
	private function generateH264params($transcoder)
	{
		/*
		 * From Eagle and on, the H264 should be generated to match Akami HD constarints 
		 * for Apple HLS/adaptive playbck:
		 * - aligned key frames across all bitrates
		 * - same frame rate across all bitrates
		 * 
		 * '_h264ForMobile' flag rules the generation mode 
		 */
		if(is_null($this->_target->_engineVersion) || $this->_target->_engineVersion==0){
			return $this->generateH264paramsOriginal($transcoder);
		}
		return $this->generateH264paramsManaged($transcoder);
	}
	
	/* ---------------------------
	 * generateH264paramsOriginal
	 */
	private function generateH264paramsOriginal($transcoder)
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
		switch($this->_vidId) {
				case KDLVideoTarget::H264:
					switch($transcoder){
					case KDLTranscoders::ON2:
						$h264params=" -c H264";;
						break;
					case KDLTranscoders::FFMPEG:
					case KDLTranscoders::FFMPEG_AUX:
					case KDLTranscoders::FFMPEG_VP8:
						$h264params=" libx264 -subq 2".$ffQsettings;
						if($this->_vidBr<KDLConstants::LowBitrateThresHold) {
							$h264params .= " -crf 30";
						}
						break;
					case KDLTranscoders::MENCODER:
						$h264params = $h264params." -ovc x264 -x264encopts ";
						if($this->_vidBr) {
							$h264params .= "bitrate=".$this->_vidBr;
							$h264params .= ":";
							if($this->_vidBr<KDLConstants::LowBitrateThresHold) {
								$h264params .= "crf=30:";
							}
						}
						$h264params .= "subq=2:8x8dct:frameref=2:bframes=3:b_pyramid=1:weight_b:threads=auto";
						break;
					}
					break;
				case KDLVideoTarget::H264B:
					switch($transcoder){
					case KDLTranscoders::ON2:
						$h264params=" -c H264 --FE2_H264_PROFILE=0";
						break;
					case KDLTranscoders::FFMPEG:
					case KDLTranscoders::FFMPEG_AUX:
					case KDLTranscoders::FFMPEG_VP8:
						$h264params=" libx264 -subq 2".$ffQsettings." -coder 0";;
						if($this->_vidBr<KDLConstants::LowBitrateThresHold) {
							$h264params .= " -crf 30";
						}
						break;
					case KDLTranscoders::MENCODER:
						$h264params = $h264params." -ovc x264 -sws 9 -x264encopts ";
						if($this->_vidBr) {
							$h264params .= " bitrate=".$this->_vidBr;
							$h264params .= ":";
							if($this->_vidBr<KDLConstants::LowBitrateThresHold) {
								$h264params .= "crf=30:";
							}
						}
						$h264params .= "subq=2:frameref=6:bframes=0:threads=auto:nocabac:level_idc=30:global_header:partitions=all:trellis=1:chroma_me:me=umh";
						break;
					}
					break;
				case KDLVideoTarget::H264M:
					switch($transcoder){
					case KDLTranscoders::ON2:
						$h264params=" -c H264 --FE2_H264_PROFILE=1";;
						break;
					case KDLTranscoders::FFMPEG:
					case KDLTranscoders::FFMPEG_AUX:
					case KDLTranscoders::FFMPEG_VP8:
						$h264params="libx264 -subq 5".$ffQsettings." -coder 1 -refs 2";
						if($this->_vidBr<KDLConstants::LowBitrateThresHold) {
							$h264params .= " -crf 30";
						}
						break;
					case KDLTranscoders::MENCODER:
						$h264params = $h264params." -ovc x264 -sws 9 -x264encopts ";
						if($this->_vidBr) {
							$h264params .= " bitrate=".$this->_vidBr;
							$h264params .= ":";
							if($this->_vidBr<KDLConstants::LowBitrateThresHold) {
								$h264params .= "crf=30:";
							}
						}
						$h264params .= "subq=5:frameref=6:bframes=3:threads=auto:level_idc=30:global_header:partitions=all:trellis=1:chroma_me:me=umh";
						break;
					}
					break;
				case KDLVideoTarget::H264H:				
					switch($transcoder){
					case KDLTranscoders::ON2:
						$h264params=" -c H264 --FE2_H264_PROFILE=2";
						break;
					case KDLTranscoders::FFMPEG:
					case KDLTranscoders::FFMPEG_AUX:
					case KDLTranscoders::FFMPEG_VP8:
						$h264params=" libx264 -subq 7".$ffQsettings." -bf 16 -coder 1 -refs 6 -flags2 +bpyramid+wpred+mixed_refs+dct8x8+fastpskip";
						if($this->_vidBr<KDLConstants::LowBitrateThresHold) {
							$h264params .= " -crf 30";
						}
						break;
					case KDLTranscoders::MENCODER:
						$h264params = $h264params." -ovc x264 -sws 9 -x264encopts ";
						if($this->_vidBr) {
							$h264params .= " bitrate=".$this->_vidBr;
							$h264params .= ":";
							if($this->_vidBr<KDLConstants::LowBitrateThresHold) {
								$h264params .= "crf=30:";
							}
						}
						$h264params .= "subq=7:frameref=6:bframes=3:b_pyramid=1:weight_b=1:threads=auto:level_idc=30:global_header:8x8dct:trellis=1:chroma_me:me=umh";
						break;
					}
					break;
		}
		return $h264params;
	}

	/* ---------------------------
	 * generateH264paramsManaged
	 */
	private function generateH264paramsManaged($transcoder)
	{
			/*
			 * From Eagle and on, the H264 should be generated to match Akami HD constarints 
			 * for Apple HLS/adaptive playbck:
			 * - aligned key frames across all bitrates
			 * - same frame rate across all bitrates
			 * 
			 * '_h264ForMobile' flag rules the generation mode 
			 */
		if(property_exists($this->_target->_video,"_h264ForMobile")) {
			$h264ForMobile = $this->_target->_video->_h264ForMobile;
		}
		else if(isset($this->_target->_engineVersion) && $this->_target->_engineVersion==1){
			$h264ForMobile = 1;
		}
		else {
			$h264ForMobile = 0;
		}
		
//		return $this->generateH264paramsOriginal($transcoder);

$h264Codec = new KDLCodecH264($this->_target->_video);
		switch($transcoder){
		case KDLTranscoders::ON2:
			return $this->generateH264paramsOriginal($transcoder);
			break;
		case KDLTranscoders::FFMPEG:
		case KDLTranscoders::FFMPEG_AUX:
		case KDLTranscoders::FFMPEG_VP8:
			return $h264Codec->FFmpeg();
			break;
		case KDLTranscoders::MENCODER:
			return $h264Codec->Mencoder();
			break;
		}
	}
	
}

abstract class KDLBaseCodec {
	
	public function __construct(KDLVideoData $vidObj=null){
		if(isset($vidObj)){
			$this->Evaluate($vidObj);
		}
	}
	abstract public function Evaluate(KDLVideoData $vidObj);
}
	
class KDLCodecH264 extends KDLBaseCodec{
	public	$_crf;			/*	"Constant quality mode (also known as constant ratefactor). 
								Bitrate corresponds approximately to that of constant quantizer, 
								but gives better quality overall at little speed cost."
								default=23
								For presentation-style - 10
							*/
	public 	$_refs;			/*	reference frames*/
	public	$_subq=2;		/* subpixel estimation complexity. Higher numbers are better */
	public	$_coder=0;

	public 	$_qcomp=0.6; 
	public 	$_qmin=10;
	public 	$_qmax=50;
	public 	$_qdiff=4;
	
	public	$_bframes=3;	/* B-Frames */
	
	public	$_b_pyramid;	/* "it increases the DPB (decoding picture buffer) size required
								for playback, so when encoding for hardware, disable"
								to disable for mobiles
							*/
	public	$_weight_b=1; 	/* dragonfly - not set
								x264 recommendation - no cost -> always enabled
							*/
	public	$_threads;
	public	$_partitions;
	public	$_level;		/* Represents minimal required decoder capability - frmSz/frmRt/br*/
	public	$_global_header=1;/*"is used to force ffmpeg to spit out some important audio specifications"
								Important for akmi-hd/hls. somehow related with vglobal/aglobal options
								that i do not use here.
							*/
	public	$_trellis = 1;	/* "The main decision made in quantization is which coefficients 
								to round up and which to round down. Trellis chooses the optimal 
								rounding choices for the maximum rate-distortion score, 
								to maximize PSNR relative to bitrate."
								BP does not support it.
							*/
	public	$_chroma_me=1;	/* "Normally, motion estimation works off both the luma and 
								chroma planes."
								can be turned on to gain speed. relevant for mencoder
							*/
	public	$_dct8x8;		/* "the only reason to disable it is when one needs support 
								on a device not compatible with High Profile."
							*/
	public	$_fastskip=0;	/* "By default, x264 will skip macroblocks in P-frames that 
								don't appear to	have changed enough between two frames 
								to justify encoding the difference. This considerably speeds
								 up encoding. However, for a slight quality boost, 
								P-skip can be disabled."
								To turm on for 'presentation' assets
							*/
	public	$_mixed_refs;	/* "boosts quality with little speed impact. 
								It should generally be used, though it 
								obviously has no effect with only one reference frame."
							*/
	
	public	$_me="umh";
	public	$_loop;
	public	$_mv4;
	public	$_cmp;
	public	$_me_range; 
	public	$_keyint_min;	/* "Minimum GOP length, the minimum distance between I-frames. 
								Recommended default: 25"
								should match gop.
							*/
	public	$_sc_threshold; /* "Adjusts the sensitivity of x264's scenecut detection. Rarely needs to be adjusted. 
								Recommended default: 40"
							*/
	public	$_i_qfactor; 
	public	$_bt; 
	public	$_maxrate; 
	public	$_bufsize; 
	public	$_rc_eq;

	/* none h264 */
	public	$_sws;			/*	0 (Fast bilinear), 
								1 (Bilinear), 
								2 (Bicubic (good quality)), 
								3 (Experimental), 
								4 (Nearest neighbour (bad quality)), 
								5 (Area), 
								6 (Luma bicubic / chroma bilinear), 
								7 (Gauss), 
								8 (SincR), 
								9 (Lanczos), 
								10 (Bicubic spline)
							*/
	
	public	$_async; 
	public	$_vsync;
	
	public	$_vidBr;
	
	/* ----------------------
	 * Evaluate(KDLFlavor $target)
	 */
	public function Evaluate(KDLVideoData $vidObj){

		
			/*
			 * From Eagle and on, the H264 should be generated to match Akami HD constarints 
			 * for Apple HLS/adaptive playbck:
			 * - aligned key frames across all bitrates
			 * - same frame rate across all bitrates
			 * 
			 * '_h264ForMobile' flag rules the generation mode 
			 */
		$h264ForMobile = 0;
		if(property_exists($vidObj,"_h264ForMobile")) {
			$h264ForMobile = $vidObj->_h264ForMobile;
		}
			/*
			 * Check for 'presentation-style' video mode
			 */
		$presentationStyleMode = 0;
		if($vidObj->_bitRate<KDLConstants::LowBitrateThresHold) {
			$presentationStyleMode=1;
			$this->_crf=10;
		}
		
		if(isset($vidObj)){
			$this->_vidBr = $vidObj->_bitRate;
		}
/*
			$this->_vidId = $target->_video->_id;
			$this->_vidBr = $target->_video->_bitRate;
			$this->_vidWid = $target->_video->_width;
			$this->_vidHgt = $target->_video->_height;
			$this->_vidFr = $target->_video->_frameRate;
			$this->_vidGop = $target->_video->_gop;
			$this->_vid2pass = $target->_isTwoPass;
			$this->_vidRotation = $target->_video->_rotation;
			$this->_vidScanType = $target->_video->_scanType;

ffmp - -flags +loop+mv4 -cmp 256 -partitions +parti4x4+partp8x8+partb8x8 -trellis 1 -refs 1 -me_range 16 -keyint_min 20 -sc_threshold 40 -i_qfactor 0.71 -bt 800k -maxrate 1200k -bufsize 1200k -rc_eq 'blurCplx^(1-qComp)' -level 30 -async 2 -vsync 2 
 */
		$h264params=null;
		switch($vidObj->_id) {
		case KDLVideoTarget::H264:
			$this->_refs = 2;
			$this->_coder = 0;
			$this->_subq = 2;
			$this->_bframes=3;
			$this->_b_pyramid;
			$this->_weight_b;
			$this->_threads="auto";
			$this->_partitions;
			$this->_dct8x8=null;

			if($h264ForMobile) {
				$this->forMobile();
			}
			break;
		case KDLVideoTarget::H264B:
			$this->_refs = 6; // ffm - 2
			$this->_coder = 0;
			$this->_sws = 9; // ffm - none
			$this->_subq = 2;
			$this->_bframes=0;
			$this->_b_pyramid;
			$this->_weight_b;
			$this->_threads="auto";
			$this->_level;
			$this->_partitions;  // ffm - none/default
			$this->_global_header=1;
			$this->_trellis = 1;
			$this->_chroma_me;
			$this->_me="umh";
			$this->_dct8x8=null;

			if($h264ForMobile) {
				$this->forMobile();
			}
			break;
		case KDLVideoTarget::H264M:
			$this->_refs = 6;// ffm - 2
			$this->_coder = 1;
			$this->_sws = 9; // ffm - none
			$this->_subq = 5;
			$this->_bframes=3;
			$this->_b_pyramid;
			$this->_weight_b;
			$this->_threads="auto";
			$this->_level;
			$this->_partitions="all";  // ffm - none/default
			$this->_global_header=1;
			$this->_trellis = 1;
			$this->_chroma_me;
			$this->_me="umh";
			$this->_dct8x8=null;

			if($h264ForMobile) {
				$this->forMobile();
			}
			break;
		case KDLVideoTarget::H264H:				
			$this->_refs = 6;// ffm - 2
			$this->_coder = 1;
			$this->_sws = 9; // ffm - none
			$this->_subq = 7;
			$this->_bframes=3; //ffm - 16
			$this->_b_pyramid=1;
			$this->_weight_b=1; // ffmpeg - wpred
			$this->_threads="auto";
			$this->_level;
			$this->_partitions="p8x8,b8x8,i8x8,i4x4";  // ffm - none/default
			$this->_global_header=1;
			$this->_trellis = 1;
			$this->_chroma_me;
			$this->_me="umh";
			$this->_dct8x8=1;
			$this->_fastskip=1;
			$this->_mixed_refs=1;
			
			if($h264ForMobile) {
				$this->forMobile();
			}
			break;
		}
		return true;
	}

	/* ----------------------
	 * forMobile
	 */
	private function forMobile(){
//ffmp - -flags +loop+mv4 -cmp 256 -partitions +parti4x4+partp8x8+partb8x8 -trellis 1 -refs 1 
//-me_range 16 -keyint_min 20 -sc_threshold 40 -i_qfactor 0.71 -bt 800k 
//-maxrate 1200k - 1200k -rc_eq 'blurCplx^(1-qComp)' -level 30 -async 2 -vsync 2 
		$this->_cmp = 256;
		$this->_partitions="p8x8,b8x8,i4x4";
		$this->_loop=1;
		$this->_mv4=1;
		$this->_trellis = 1;
		$this->_refs = 1;
		$this->_me_range = 16;
		$this->_keyint_min = 25; 	//should match gop
		$this->_sc_threshold = 40; 	// x264 recommendation
		$this->_i_qfactor = 0.71;
		$this->_bt = 800;			// bit rate tolleranceto be relative to vidBr
		$this->_maxrate = 1200;		//should match vidBr
		$this->_bufsize = 1200;		//should match vidBr ??? "Depends on the profile level of the video being encoded. Set only if you're encoding for a hardware device"
		$this->_rc_eq = '\'blurCplx^(1-qComp)\'';
		$this->_level = 30; 		// to match iPhone processing constraints
		$this->_vsync = 2;
		$this->_async = 2;
		
		$this->_b_pyramid = null;
		$this->_mixed_refs= null;
		$this->_dct8x8=null;
		$this->_bframes=0;
	}
	
	/* ----------------------
	 * FFmpeg
	 */
	public function FFmpeg()
	{
// main=" libx264 -subq 5".$ffQsettings." -coder 1 -refs 2";
// High=" libx264 -subq 7".$ffQsettings." -bf 16 -coder 1 -refs 6 -flags2 +bpyramid+wpred+mixed_refs+dct8x8+fastpskip";
		$params = " libx264";
		if(isset($this->_subq)) 	$params.=" -subq $this->_subq";
		$params.= " -qcomp $this->_qcomp -qmin $this->_qmin -qmax $this->_qmax -qdiff $this->_qdiff";
		if(isset($this->_bframes)) 	$params.=" -bf $this->_bframes";
		if(isset($this->_coder)) 	$params.=" -coder $this->_coder";
		if(isset($this->_refs)) 	$params.=" -refs $this->_refs";
		if(isset($this->_crf)) 		$params.=" -crf $this->_crf";
		
		if(isset($this->_partitions)){
			$partArr = explode(",",$this->_partitions);
			$partitions = null;
			foreach ($partArr as $p) {
				switch($p){
				case "all":
					$partitions.="+partp8x8+partp4x4+partb8x8+parti8x8+parti4x4";
					break;
				case "p8x8":
					$partitions.="+partp8x8";
					break;
				case "p4x4":
					$partitions.="+partp4x4";
					break;
				case "b8x8":
					$partitions.="+partb8x8";
					break;
				case "i8x8":
					$partitions.="+parti8x8";
					break;
				case "i4x4":
					$partitions.="+parti4x4";
					break;
				}
			}
			if(isset($partitions))	$params.=" -partitions $partitions";
		}
// ffmpeg -i <in file> -f mpegts -acodec libmp3lame -ar 48000 -ab 64k -s 320×240 -vcodec libx264 -b 96k 
// -flags +loop -cmp +chroma -partitions +parti4x4+partp8x8+partb8x8 -subq 5 -trellis 1 -refs 1 -coder 0 
// -me_range 16 -keyint_min 25 -sc_threshold 40 -i_qfactor 0.71 -bt 200k 
// -maxrate 96k -bufsize 96k -rc_eq 'blurCplx^(1-qComp)' 
// -qcomp 0.6 -qmin 10 -qmax 51 -qdiff 4 -level 30 -aspect 320:240 -g 30 -async 2 
		if(isset($this->_trellis))		$params.= " -trellis $this->_trellis";
		if(isset($this->_keyint_min))	$params.= " -keyint_min $this->_keyint_min";
		if(isset($this->_me_range))		$params.= " -me_range $this->_me_range";
		if(isset($this->_sc_threshold))	$params.= " -sc_threshold $this->_sc_threshold";
		if(isset($this->_i_qfactor))	$params.= " -i_qfactor $this->_i_qfactor";
		if(isset($this->_bt))			$params.= " -bt $this->_bt";
		if(isset($this->_maxrate))		$params.= " -maxrate $this->_maxrate";
		if(isset($this->_bufsize))		$params.= " -bufsize $this->_bufsize";
		if(isset($this->_rc_eq))		$params.= " -rc_eq $this->_rc_eq";
		if(isset($this->_level))		$params.= " -level $this->_level";
		
		$flags=null;
		{
			if(isset($this->_loop)) {
				if($this->_loop>0) $flags.= "+loop";
				else $flags.= "-loop";
			}
			if(isset($this->_mv4)) {
				if($this->_mv4>0) $flags.= "+mv4";
				else $flags.= "-mv4";
			}
			if(isset($this->_global_header)) {
				if($this->_global_header>0) $flags.= "+global_header";
				else $flags.= "-global_header";
			}
			
			if(isset($flags))	$params.=" -flags $flags";
		}
		
		$flags2=null;
		{
			if(isset($this->_b_pyramid)) 	$flags2.= "+bpyramid";
			if(isset($this->_weight_b))		$flags2.= "+wpred";
			if(isset($this->_mixed_refs))	$flags2.= "+mixed_refs";
			if(isset($this->_dct8x8))		$flags2.= "+dct8x8";
			if(isset($this->_fastpskip)) {
				if($this->_fastpskip>0) $flags2.= "+fastpskip";
				else $flags2.= "-fastpskip";
			}
			
			if(isset($flags2))	$params.=" -flags2 $flags2";
		}
		
		if(isset($this->_vsync))		$params.= " -vsync $this->_vsync";
		if(isset($this->_async))		$params.= " -async $this->_async";
		
		return $params;
	} 
	
	/* ----------------------
	 * Mencoder
	 */
	public function Mencoder()
	{
/*
						$h264params = $h264params." -ovc x264 -x264encopts ";
						if($this->_vidBr) {
							$h264params .= "bitrate=".$this->_vidBr;
							$h264params .= ":";
							if($this->_vidBr<KDLConstants::LowBitrateThresHold) {
								$h264params .= "crf=30:";
							}
						}
						$h264params .= "subq=2:8x8dct:frameref=2:bframes=3:b_pyramid=1:weight_b:threads=auto";
*/
/*
						$h264params = $h264params." -ovc x264 -sws 9 -x264encopts ";
						if($this->_vidBr) {
							$h264params .= " bitrate=".$this->_vidBr;
							$h264params .= ":";
							if($this->_vidBr<KDLConstants::LowBitrateThresHold) {
								$h264params .= "crf=30:";
							}
						}
						$h264params .= "subq=2:frameref=6:bframes=0:threads=auto:nocabac:level_idc=30:
						global_header:partitions=all:trellis=1:chroma_me:me=umh";

 */
		$params = " -ovc x264";
		if(isset($this->_sws))		$params.= " -sws $this->_sws";

		$encopts = " qcomp=$this->_qcomp:qpmin=$this->_qmin:qpmax=$this->_qmax:qpstep=$this->_qdiff:";
		{
			if(isset($this->_vidBr)) {
				$encopts.= "bitrate=$this->_vidBr:";
				if(isset($this->_crf))	$encopts.= "crf=30:";
			}
			if(isset($this->_subq))			$encopts.= "subq=$this->_subq:";
			if(isset($this->_refs))			$encopts.= "frameref=$this->_refs:";
			if(isset($this->_bframes))		$encopts.= "bframes=$this->_bframes:";
			if(isset($this->_b_pyramid))	$encopts.= "b_pyramid=1:";
			if(isset($this->_weight_b))		$encopts.= "weight_b=1:";
			if(isset($this->_threads))		$encopts.= "threads=$this->_threads:";
			if(isset($this->_coder) && $this->_coder==0) $encopts.= "nocabac:";
			if(isset($this->_level))		$encopts.= "level_idc=$this->_level:";
			if(isset($this->_global_header))$encopts.= "global_header:";
			if(isset($this->_dct8x8))		$encopts.= "8x8dct:";
			if(isset($this->_trellis))		$encopts.= "trellis=$this->_trellis:";
			if(isset($this->_chroma_me))	$encopts.= "chroma_me=$this->_chroma_me:";

			if(isset($this->_me))			$encopts.= "me=$this->_me:";
			if(isset($this->_keyint_min))	$encopts.= "keyint_min=$this->_keyint_min:";
			if(isset($this->_me_range))		$encopts.= "me_range=$this->_me_range:";
			if(isset($this->_sc_threshold))	$encopts.= "scenecut=$this->_sc_threshold:";
			if(isset($this->_i_qfactor))	$encopts.= "ipratio=$this->_i_qfactor:";
			if(isset($this->_bt))			$encopts.= "ratetol=$this->_bt:";
			if(isset($this->_maxrate))		$encopts.= "vbv-maxrate=$this->_maxrate:";
			if(isset($this->_bufsize))		$encopts.= "vbv-bufsize=$this->_bufsize:";
//			if(isset($this->_rc_eq))		$encopts.= " -rc_eq $this->_rc_eq";
			
			if(isset($this->_partitions)){
				$partArr = explode(",",$this->_partitions);
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
				if(isset($partitions))	$encopts.="partitions=$partitions:";
			}
			
			if(isset($encopts))	{
				$encopts = rtrim($encopts,":");
				$params.= " -x264encopts $encopts";
			}
		}
		
		
		return $params;
	}
}

?>