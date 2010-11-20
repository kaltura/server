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

		if($target->_video)
			$cmdLineGenerator->_vidBr = $target->_video->_bitRate;
		$params = new KDLOperationParams($this->_id, $extra);
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
				KDLWarnings::ToString(KDLWarnings::TranscoderFormat, $this->_id, ($medSetSec->_id."/".$medSetSec->_format));
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
		return false;	
	}
}


	/* ===========================
	 * KDLTranscoderCommand
	 */
class KDLTranscoderCommand {
	
			public $_vidId;
			public $_vidBr;
			public $_vidWid;
			public $_vidHgt;
			public $_vidFr;
			public $_vidGop;
			public $_vid2pass;
			public $_vidRotation;
			public $_vidScanType;
			
			public $_audId;
			public $_audBr; 
			public $_audCh;
			public $_audSr;
			
			public $_conId;
			
			public $_inFileName=KDLCmdlinePlaceholders::InFileName;
			public $_outFileName=KDLCmdlinePlaceholders::OutFileName;
			public $_clipStart=null;
			public $_clipDur=null;
			
	public function KDLTranscoderCommand($inFileName=KDLCmdlinePlaceholders::InFileName, $outFileName=KDLCmdlinePlaceholders::OutFileName, $clipStart=null, $clipDur=null)
	{
		$this->_inFileName=$inFileName;
		$this->_outFileName=$outFileName;
		$this->_clipStart=$clipStart;
		$this->_clipDur=$clipDur;
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

		if($this->_inFileName){
			$cmdStr = "-i ".$this->_inFileName;
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
/*
				case KDLVideoTarget::H264:
					$vcodecParams = "libx264";
					break;
				case KDLVideoTarget::H264B:
					$vcodecParams = "libx264 -coder 0";
					break;
				case KDLVideoTarget::H264M:
					$vcodecParams = "libx264 -bf 8 -coder 1 -refs 2";
					break;
				case KDLVideoTarget::H264H:
					$vcodecParams = "libx264 -bf 16 -coder 1 -refs 6 -flags2 +bpyramid+wpred+mixed_refs+dct8x8+fastpskip";
					break;
*/
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
//			if($vid->_id==KDLVideoTarget::H264)
//				$cmdStr = $cmdStr." -qmin 10";
			if($this->_vidBr){
				$cmdStr = $cmdStr." -b ".$this->_vidBr."k";
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
		
		if($this->_clipStart!==null && $this->_clipStart>0){
			$cmdStr = $cmdStr." -ss ".$this->_clipStart/1000;
		}
		
		if($this->_clipDur!==null && $this->_clipDur>0){
			$cmdStr = $cmdStr." -t ".$this->_clipDur/1000;
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
			}
			$cmdStr = $cmdStr." -f ".$format;
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
/*				$cmdStr = $cmdStr." -ovc x264 -x264encopts";
					if($this->_vidBr) {
						$cmdStr .= " bitrate=".$this->_vidBr;
						$cmdStr .= ":";
					}
					else {
						$cmdStr .= " ";
					}
					$cmdStr = $cmdStr."subq=5:8x8dct:frameref=2:bframes=3:b_pyramid=1:weight_b:threads=auto";
					break;*/
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
			if($this->_vidRotation)
				$cmdStr = $cmdStr.",rotate=1";
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
				if($this->_vidRotation)
					$cmdStr = $cmdStr." -h ".$this->_vidWid." -w ".$this->_vidHgt;
				else
					$cmdStr = $cmdStr." -w ".$this->_vidWid." -h ".$this->_vidHgt;
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
	 * Generate
	 */
	private function generateH264params($transcoder)
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
						break;
					case KDLTranscoders::MENCODER:
						$h264params = $h264params." -ovc x264 -x264encopts ";
						if($this->_vidBr) {
							$h264params .= "bitrate=".$this->_vidBr;
							$h264params .= ":";
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
						break;
					case KDLTranscoders::MENCODER:
						$h264params = $h264params." -ovc x264 -sws 9 -x264encopts ";
						if($this->_vidBr) {
							$h264params .= " bitrate=".$this->_vidBr;
							$h264params .= ":";
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
						break;
					case KDLTranscoders::MENCODER:
						$h264params = $h264params." -ovc x264 -sws 9 -x264encopts ";
						if($this->_vidBr) {
							$h264params .= " bitrate=".$this->_vidBr;
							$h264params .= ":";
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
						break;
					case KDLTranscoders::MENCODER:
						$h264params = $h264params." -ovc x264 -sws 9 -x264encopts ";
						if($this->_vidBr) {
							$h264params .= " bitrate=".$this->_vidBr;
							$h264params .= ":";
						}
						$h264params .= "subq=7:frameref=6:bframes=3:b_pyramid=1:weight_b=1:threads=auto:level_idc=30:global_header:8x8dct:trellis=1:chroma_me:me=umh";
						break;
					}
					break;
		}
		return $h264params;
	}
	
}

?>