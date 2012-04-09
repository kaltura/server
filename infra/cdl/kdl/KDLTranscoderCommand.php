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
		$cmdLineGenerator = $target->SetTranscoderCmdLineGenerator($predesign);

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
		if(parent::CheckConstraints($source, $target, $errors, $warnings)==true)
			return true;

		if($this->_id==KDLTranscoders::FFMPEG_AUX) {
			$transcoder = new KDLOperatorFfmpeg0_10($this->_id);
			if($transcoder->CheckConstraints($source, $target, $errors, $warnings)==true)
				return true;
		}
			
		if($this->_id==KDLTranscoders::FFMPEG) {
			$transcoder = new KDLOperatorFfmpeg($this->_id);
			if($transcoder->CheckConstraints($source, $target, $errors, $warnings)==true)
				return true;
		}
	
		
		if($this->_id==KDLTranscoders::MENCODER) {
			$transcoder = new KDLOperatorMencoder($this->_id);
			if($transcoder->CheckConstraints($source, $target, $errors, $warnings)==true)
				return true;
		}
	
		
		if($this->_id==KDLTranscoders::ON2) {
			$transcoder = new KDLOperatorOn2($this->_id);
			if($transcoder->CheckConstraints($source, $target, $errors, $warnings)==true)
				return true;
		}
	
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
		 
		if(($this->_id==KDLTranscoders::MENCODER || $this->_id==KDLTranscoders::ENCODING_COM || $this->_id==KDLTranscoders::ON2)
		&& $target->_video==null) {
			$warnings[KDLConstants::AudioIndex][] = //"The transcoder (".$key.") does not handle properly DAR<>PAR.";
				KDLWarnings::ToString(KDLWarnings::TranscoderLimitation, $this->_id);
			return true;
		}*/

		/*
		 * Remove encoding.com and ffmpegs
		 * for rotated videos
		 
		if(($this->_id==KDLTranscoders::ENCODING_COM || $this->_id==KDLTranscoders::FFMPEG)
		&& $target->_video && $target->_video->_rotation) {
			$warnings[KDLConstants::VideoIndex][] = //"The transcoder (".$key.") does not handle properly DAR<>PAR.";
				KDLWarnings::ToString(KDLWarnings::TranscoderLimitation, $this->_id);
			return true;
		}*/
		
		/*
		 * Remove On2
		 * for 270 rotated videos
		 
		if($this->_id==KDLTranscoders::ON2
		&& $target->_video && $target->_video->_rotation==270) {
			$warnings[KDLConstants::VideoIndex][] = //"The transcoder (".$key.") does not handle properly DAR<>PAR.";
				KDLWarnings::ToString(KDLWarnings::TranscoderLimitation, $this->_id);
			return true;
		}*/
		
		/*
		 * Non Mac transcoders should not mess up with QT/WMV/WMA
		 * 
		 
		$qt_wmv_list = array("wmv1","wmv2","wmv3","wvc1","wmva","wma1","wma2","wmapro");
		if((# $this->_id==KDLTranscoders::ENCODING_COM || $this->_id==KDLTranscoders::MENCODER || $this->_id==KDLTranscoders::ON2 || 
			$this->_id==KDLTranscoders::FFMPEG)
		&& $source->_container && ($source->_container->_id=="qt" || $source->_container->_format=="qt")
		&& (
			($source->_video && (in_array($source->_video->_format,$qt_wmv_list)||in_array($source->_video->_id,$qt_wmv_list)))
			||($source->_audio && (in_array($source->_audio->_format,$qt_wmv_list)||in_array($source->_audio->_id,$qt_wmv_list)))
			)
		){
			$warnings[KDLConstants::VideoIndex][] = //"The transcoder (".$key.") can not process the (".$sourcePart->_id."/".$sourcePart->_format. ").";
				KDLWarnings::ToString(KDLWarnings::TranscoderFormat, $this->_id, "qt/wmv/wma");
			return true;
		}*/
		
		return false;	
	}
}


	/* ===========================
	 * KDLTranscoderCommand
	 */
class KDLTranscoderCommand {
	
			private $_design;
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
			
			private $_clipStart=null;
			private $_clipDur=null;
			
	public function KDLTranscoderCommand(KDLFlavor $design, KDLFlavor $target)
	{
		$this->_design = $design;
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
				$cmd=$this->FFMpeg($transParams->_extra);
				break;
			case KDLTranscoders::MENCODER:
				$cmd=$this->Mencoder($transParams->_extra);
				break;
			case KDLTranscoders::ENCODING_COM:
				$cmd=$transParams->_id;
				break;
			case KDLTranscoders::FFMPEG_AUX:
			case KDLTranscoders::FFMPEG_VP8:
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
		$transcoder = new KDLOperatorFfmpeg0_10(KDLTranscoders::FFMPEG); 
		return $transcoder->GenerateCommandLine($this->_design,  $this->_target,$extra);
	}

	/* ---------------------------
	 * Mencoder
	 */
	public function Mencoder($extra=null)
	{
		$transcoder = new KDLOperatorMencoder(KDLTranscoders::MENCODER); 
		return $transcoder->GenerateCommandLine($this->_design,  $this->_target,$extra);
	}

	/* ---------------------------
	 * CLI_Encode
	 */
	public function CLI_Encode($extra=null)
	{
		$transcoder = new KDLOperatorOn2(KDLTranscoders::ON2); 
		return $transcoder->GenerateCommandLine($this->_design,  $this->_target,$extra);
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
	{/**/
		$transcoder = new KDLOperatorFfmpeg(KDLTranscoders::FFMPEG_AUX); 
		return $transcoder->GenerateCommandLine($this->_design,  $this->_target,$extra);
	}

	/* ---------------------------
	 * EE3
	 */
	public function EE3($extra=null)
	{
		$ee3 = new KDLExpressionEncoder3();
		return $ee3->GeneratePresetFile($this->_target);
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