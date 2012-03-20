<?php

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
		 * Remove On2
		 * for 270 rotated videos
		 */
		if($this->_id==KDLTranscoders::ON2
		&& $target->_video && $target->_video->_rotation==270) {
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
			if(isset($vidObj->_dar) && $vidObj->_dar>0) {
				$cmdStr.= " -aspect ".round($vidObj->_dar,4);
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
				else if($this->_vidRotation==270)
					$cmdStr = $cmdStr.",rotate=3";
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
		$ee3 = new KDLExpressionEncoder3();
		return $ee3->GeneratePresetFile($this->_target);
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
		/*
		 * Apple HLS assets should not have the 'global_header' set 
		 * - it fails to get segmented
		 */
		if($this->_target->_container->_id==KDLContainerTarget::APPLEHTTP){
			$h264Codec->_global_header=null;
		}
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

class KDLExpressionEncoder3 {
const jobXml = '<?xml version="1.0"?>
<!--Created with Kaltura Decision Layer module-->
<Preset
  Version="3.0">
  <Job
    OutputDirectory="C:\Tmp\Prod"
    DefaultMediaOutputFileName="{OriginalFilename}.{DefaultExtension}" />
  <MediaFile
    VideoResizeMode="Letterbox"
	ThumbnailCodec="Jpeg" 
	ThumbnailTime="00:00:03"
    ThumbnailMode="Custom">
    <OutputFormat>
    </OutputFormat>
  </MediaFile>
</Preset>';
		
const vc1CodecXml = '<?xml version="1.0"?>
<AdvancedVC1VideoProfile
	SmoothStreaming="True"
	ClosedGop="True"
	OutputMode="ElementaryStreamSequenceHeader"
	DifferentialQuantization="Off"
	InLoopFilter="True"
	MotionSearchRange="MacroblockAdaptive"
	BFrameCount="1"
	AdaptiveDeadZone="Conservative"
	AdaptiveGop="True"
	DenoiseFilter="False"
	KeyFrameDistance="00:00:02"
	MotionChromaSearch="LumaOnly"
	MotionMatchMethod="SAD"
	NoiseEdgeRemovalFilter="False"
	OverlapSmoothingFilter="True"
	AutoFit="True"
	Force16Pixels="False"
	Complexity="Normal"
	FrameRate="0"
	SeparateFilesPerStream="True"
	NumberOfEncoderThreads="0">
	<Streams
		AutoSize="False"
		FreezeSort="False">
		<StreamInfo
			Size="640, 480">
		</StreamInfo>
	</Streams>
</AdvancedVC1VideoProfile>';

const h264CodecXml = '<?xml version="1.0"?>
<MainH264VideoProfile
	SmoothStreaming="False"
	BFrameCount="1"
	EntropyMode="Cabac"
	RDOptimization="False"
	KeyFrameDistance="00:00:05"
	InLoopFilter="True"
	MEPartitionLevel="EightByEight"
	NumberOfReferenceFrames="4"
	SearchRange="32"
	AutoFit="True"
	Force16Pixels="False"
	Complexity="Normal"
	FrameRate="0"
	SeparateFilesPerStream="True"
	NumberOfEncoderThreads="0">
	<Streams
		AutoSize="False"
		FreezeSort="False">
		<StreamInfo
			Size="640, 480">
		</StreamInfo>
	</Streams>
</MainH264VideoProfile>';

const audioBitrateXml = '<Bitrate>
	<ConstantBitrate
		Bitrate="96"
		IsTwoPass="False"
		BufferWindow="00:00:00" />
</Bitrate>';

const videoConstantBitrateXml = '<Bitrate>
                  <ConstantBitrate
                    Bitrate="1111"
                    IsTwoPass="False"
                    BufferWindow="00:00:04" />
			</Bitrate>
';

const videoVariableBitrateXml = '<Bitrate>
				<VariableConstrainedBitrate
					PeakBitrate="1050"
					PeakBufferWindow="00:00:04"
					AverageBitrate="700" />
			</Bitrate>
';

		/* ------------------------------
		 * GeneratePresetFile
		 */
	public static function GeneratePresetFile($target, $outFileName=null)
	{
$fileFormat=null;
$videoProfileElem=null;
		if(isset($target->_video)){
$vidObj = $target->_video;
			$videoProfileElem = new SimpleXMLElement('<?xml version="1.0"?><VideoProfile></VideoProfile>');
			$videoCodec=$videoProfileElem->addChild('VideoProfile');
			switch($vidObj->_id){
				case KDLVideoTarget::WMV2:
				case KDLVideoTarget::WMV3:
				case KDLVideoTarget::WVC1A:
				default:
					$videoCodec = new SimpleXMLElement(self::vc1CodecXml);
					$fileFormat = 'wmv';
					break;
				case KDLVideoTarget::H264:
				case KDLVideoTarget::H264B:
				case KDLVideoTarget::H264M:
				case KDLVideoTarget::H264H:				
					$videoCodec = new SimpleXMLElement(self::h264CodecXml);
					$fileFormat = 'mp4';
					$cbr = 1;
					break;
			}
			if($target->_container->_id==KDLContainerTarget::ISMV)
				$videoCodec['SmoothStreaming'] = 'True';
			else
				$videoCodec['SmoothStreaming'] = 'False';
				
			$vFr = 30;
			if($vidObj->_frameRate!==null && $vidObj->_frameRate>0){
				$vFr = $vidObj->_frameRate;
				$videoCodec['FrameRate']=$vidObj->_frameRate;
			}
			if($vidObj->_gop!==null && $vidObj->_gop>0){
				$kFr = round($vidObj->_gop/$vFr);
				$mi = round($kFr/60);
				$se = $kFr%60;
				$videoCodec['KeyFrameDistance']=sprintf("00:%02d:%02d",$mi,$se);
			}

			if(!isset($cbr)) {
				if(isset($vidObj->_cbr))
					$cbr = $vidObj->_cbr;
				else
					$cbr = 0;
			}
			if($vidObj->_bitRate){
				if($target->_container->_id==KDLContainerTarget::ISMV)
					$vbr=max(100,$vidObj->_bitRate); // The minimum video br for the SL is 100
				else
					$vbr=$vidObj->_bitRate;
				if($cbr==0){
					$videoBitrateElem = new SimpleXMLElement(self::videoVariableBitrateXml);
					KDLUtils::AddXMLElement($videoCodec->Streams->StreamInfo, $videoBitrateElem);
					$videoCodec->Streams->StreamInfo->Bitrate->VariableConstrainedBitrate['PeakBitrate'] = round($vbr*1.3);
					$videoCodec->Streams->StreamInfo->Bitrate->VariableConstrainedBitrate['AverageBitrate'] = $vbr;
				}
				else {
					$videoBitrateElem = new SimpleXMLElement(self::videoConstantBitrateXml);
					KDLUtils::AddXMLElement($videoCodec->Streams->StreamInfo, $videoBitrateElem);
					$videoCodec->Streams->StreamInfo->Bitrate->ConstantBitrate['Bitrate'] = $vbr;
				}
			}
			if($vidObj->_width!=null && $vidObj->_height!=null){
				$videoCodec->Streams->StreamInfo['Size'] = $vidObj->_width.", ".$vidObj->_height;
			}
			
//			$strmInfo = clone ($vidProfile->Streams->StreamInfo[0]);
			KDLUtils::AddXMLElement($videoProfileElem->VideoProfile, $videoCodec);
			
		}

$audioProfileElem=null;
		if(isset($target->_audio)){
$audObj = $target->_audio;
			$aacBitrates = array(96,128,160,192);
			$aacSampleRates = array(44100,48000);
			$wmaBitrates = array(32,48,64,80,96,127,128,160,191,192,255,256,383,384,440,640,768);
			$wmaSampleRates = array(44100,48000);
			
			$audioProfileElem = new SimpleXMLElement('<?xml version="1.0"?><AudioProfile></AudioProfile>');
			switch($audObj->_id){
				case KDLAudioTarget::AAC:
					$audioCodec=$audioProfileElem->addChild('AacAudioProfile');
					$audioCodec['Codec'] = 'AAC';
					$codecBitrates = $aacBitrates;
					$codecSampleRates = $aacSampleRates;
					if(!isset($fileFormat)) $fileFormat = 'mp4';
					break;
				case KDLAudioTarget::WMAPRO:
					$audioCodec=$audioProfileElem->addChild('WmaAudioProfile');
					$audioCodec['Codec'] = 'WmaProfessional';
					$codecBitrates = $wmaBitrates;
					$codecSampleRates = $wmaSampleRates;
					if(!isset($fileFormat)) $fileFormat = 'wmv';
					break;
				case KDLAudioTarget::WMA:
				default:
					$audioCodec=$audioProfileElem->addChild('WmaAudioProfile');
					$audioCodec['Codec'] = 'Wma';
					$codecBitrates = $wmaBitrates;
					$codecSampleRates = $wmaSampleRates;
					if(!isset($fileFormat)) $fileFormat = 'wmv';
					break;
			}
			$audioBitrateElem = new SimpleXMLElement(self::audioBitrateXml);
			if(isset($audObj->_bitRate))
				$br = self::lookForClosest($audObj->_bitRate, $codecBitrates);
			else
				$br = 96;
			if(isset($audObj->_sampleRate))
				$sr = self::lookForClosest($audObj->_sampleRate, $codecSampleRates);
			else
				$sr = 44100;
			$audioBitrateElem->ConstantBitrate['Bitrate'] = (string)$br;
			KDLUtils::AddXMLElement($audioCodec, $audioBitrateElem);
			if(isset($audObj->_channels) && $audObj->_channels>0)
				$audioCodec['Channels']=(string)$audObj->_channels;
//			else
//				$audioCodec['Channels']="2";
            $audioCodec['BitsPerSample']="16";
            $audioCodec['SamplesPerSecond']=(string)$sr;
		}

$jobElem = null;
$outputFormat=null;
		if(isset($target->_container)) {
$contObj = $target->_container;
			switch($contObj->_id){
				case KDLContainerTarget::ISMV:
					if(isset($fileFormat) && $fileFormat=='mp4')
						$formatName='MP4OutputFormat';
					else
						$formatName='WindowsMediaOutputFormat';
					break;
				case KDLContainerTarget::MP4:
					$formatName='MP4OutputFormat';
					break;
				case KDLContainerTarget::WMV:
				default:
					$formatName='WindowsMediaOutputFormat';
					break;
			}
			$jobElem = new SimpleXMLElement(self::jobXml);
			$outputFormat=$jobElem->MediaFile->OutputFormat->addChild($formatName);
		}
		
		if(isset($audioProfileElem)) {
			KDLUtils::AddXMLElement($outputFormat, $audioProfileElem);
		}
		if(isset($videoProfileElem)) {
			KDLUtils::AddXMLElement($outputFormat, $videoProfileElem->VideoProfile);
		}
		
		$jobElem->Job['OutputDirectory']=KDLCmdlinePlaceholders::OutDir;
		if(isset($outFileName)){
			$jobElem->Job['DefaultMediaOutputFileName']=$outFileName.".{DefaultExtension}";
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

//$stream = clone $streams->StreamInfo;
//		$streams[1] = $stream;
KalturaLog::log($jobElem->asXML());
		return $jobElem->asXML();
	}
	
		/* ------------------------------
		 * GenerateSmoothStreamingPresetFile
		 */
	public static function GenerateSmoothStreamingPresetFile($flavors)
	{
		$rootFlavor=null;
		$rootStreams=null;
		foreach ($flavors as $flavor){
			$ee3Id = KDLOperationParams::SearchInArray(KDLTranscoders::EE3, $flavor->_transcoders);
			if(is_null($ee3Id)) {
				continue;
			}
			
$transcoderParams = $flavor->_transcoders[$ee3Id];
KalturaLog::log("transcoder==>\n".print_r($transcoderParams,true)."\n<--");
			if(is_null($transcoderParams->_cmd)){
				KalturaLog::log("ee3 cmd is null");
				continue;
			}
			
			$ee3 = new SimpleXMLElement($transcoderParams->_cmd);
			if(isset($ee3->MediaFile->OutputFormat->WindowsMediaOutputFormat->VideoProfile))
				$videoProfile = $ee3->MediaFile->OutputFormat->WindowsMediaOutputFormat->VideoProfile;
			else if(isset($ee3->MediaFile->OutputFormat->MP4OutputFormat->VideoProfile))
				$videoProfile = $ee3->MediaFile->OutputFormat->MP4OutputFormat->VideoProfile;
			if(!isset($videoProfile)){
				continue;
			}
			switch($flavor->_video->_id){
				case KDLVideoTarget::WVC1A:
					$videoCodec = $videoProfile->AdvancedVC1VideoProfile;
					break;
				case KDLVideoTarget::H264:
				case KDLVideoTarget::H264M:
				case KDLVideoTarget::H264H:
					$videoCodec = $videoProfile->MainH264VideoProfile;
					break;
				case KDLVideoTarget::H264B:
//					$videoCodec = $videoProfile->BaselineH264VideoProfile;
					$videoCodec = $videoProfile->MainH264VideoProfile;
					break;
				default:
					continue;
			}
			if(!isset($videoCodec) || !isset($videoCodec['SmoothStreaming']) 
			|| ($videoCodec['SmoothStreaming']!='true' && $videoCodec['SmoothStreaming']!='True'))
				continue;
			$streams = $videoCodec->Streams;
			if(!(isset($streams) && isset($streams->StreamInfo))) {
				continue;
			}

			$flavorVideoBr = $flavor->_video->_bitRate;
			$br = $streams->StreamInfo->Bitrate;
			if(isset($br->ConstantBitrate)) {
				if($br->ConstantBitrate['Bitrate']!=$flavorVideoBr){
KalturaLog::log("-->xmlBR=".$br->ConstantBitrate['Bitrate'].", flavorBR=".$flavorVideoBr);
					$br->ConstantBitrate['Bitrate']=$flavorVideoBr;
				}
			}
			else if(isset($br->VariableConstrainedBitrate)) {
				if($br->VariableConstrainedBitrate['AverageBitrate']!=$flavorVideoBr){
KalturaLog::log("-->xmlBR=".$br->VariableConstrainedBitrate['AverageBitrate'].", flavorBR=".$flavorVideoBr);
					$br->VariableConstrainedBitrate['AverageBitrate']=$flavorVideoBr;
					$br->VariableConstrainedBitrate['PeakBitrate']=round($flavorVideoBr*1.3);
				}
			}
			
			if($rootFlavor==null) {
				$rootFlavor = $ee3;
				$rootStreams = $streams;						
			}
			else if($streams && isset($streams->StreamInfo) && $rootStreams/*&& is_array($streams->StreamInfo)*/) {
				KDLUtils::AddXMLElement($rootStreams, $streams->StreamInfo);
			}
			$br = null;
		}
		
		if($rootFlavor){
			$rootFlavor->Job['DefaultMediaOutputFileName']=KDLCmdlinePlaceholders::OutFileName.".{DefaultExtension}";
			return $rootFlavor->asXML();
		}
		else
			return null;
	}
	
	private static function lookForClosest($val, $valList)
	{
		$prev = null;
		foreach ($valList as $v){
			if($val==$v)
				return $v;
			if($val<$v){
				if(!isset($prev)){
					return $v;
				}
				if($v-$val<$val-$prev){
					return $v;
				}
				else{
					return $prev;
				}
			}	
			$prev = $v;
		}
		return $prev;
	}
}
?>