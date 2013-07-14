<?php
/**
 * @package plugins.expressionEncoder3
 */

class KDLExpressionEncoder3  extends KDLOperatorBase {
	
const jobXml = '<?xml version="1.0"?>
<!--Created with Kaltura Decision Layer module-->
<Preset
  Version="3.0">
  <Job
    OutputDirectory="C:\Tmp\Prod"
    DefaultMediaOutputFileName="{OriginalFilename}" CreateSubfolder="False" />
  <MediaFile
    VideoResizeMode="Stretch">
    <OutputFormat>
    </OutputFormat>
  </MediaFile>
</Preset>';

		/*
		 * Switched the AutoSize to "True" to match EE4 constraints.
		 */
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
	AutoFit="False"
	Force16Pixels="False"
	Complexity="Normal"
	FrameRate="0"
	SeparateFilesPerStream="True"
	NumberOfEncoderThreads="0">
	<Streams
		AutoSize="True"
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
	AutoFit="False"
	Force16Pixels="False"
	Complexity="Normal"
	FrameRate="0"
	SeparateFilesPerStream="True"
	NumberOfEncoderThreads="0">
	<Streams
		AutoSize="True"
		FreezeSort="False">
		<StreamInfo
			Size="640, 480">
		</StreamInfo>
	</Streams>
</MainH264VideoProfile>';

const h264hCodecXml = '<?xml version="1.0"?>
<HighH264VideoProfile
	SmoothStreaming="False"
	BFrameCount="1"
	EntropyMode="Cabac"
	RDOptimization="False"
	KeyFrameDistance="00:00:05"
	InLoopFilter="True"
	MEPartitionLevel="EightByEight"
	NumberOfReferenceFrames="4"
	SearchRange="32"
	AutoFit="False"
	Force16Pixels="False"
	Complexity="Normal"
	FrameRate="0"
	SeparateFilesPerStream="True"
	NumberOfEncoderThreads="0">
	<Streams
		AutoSize="True"
		FreezeSort="False">
		<StreamInfo
			Size="640, 480">
		</StreamInfo>
	</Streams>
</HighH264VideoProfile>';

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

const clippedSourcesXml = '<Sources>
      <Source
        AudioStreamIndex="0">
        <Clips>
          <Clip
            StartTime="00:00:00"
            EndTime="00:00:40" />
        </Clips>
      </Source>
    </Sources>
';
    public function GenerateConfigData(KDLFlavor $design, KDLFlavor $target)
	{
				// Remove slaches that were added to solve
				// JSON serialization issue
		$xmlStr=self::GeneratePresetFile($target);
		$xmlStr=str_replace ('"', '\"', $xmlStr);
		return $xmlStr;
	}

    public function GenerateCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
    {
    	return KDLCmdlinePlaceholders::InFileName . ' ' . KDLCmdlinePlaceholders::ConfigFileName;
    }
    
	/* ---------------------------
	 * CheckConstraints
	 */
	public function CheckConstraints(KDLMediaDataSet $source, KDLFlavor $target, array &$errors=null, array &$warnings=null)
	{
	    return parent::CheckConstraints($source, $target, $errors, $warnings);
	}

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
					$videoCodec = new SimpleXMLElement(self::h264CodecXml);
					$fileFormat = 'mp4';
					$cbr = 1;
					break;
				case KDLVideoTarget::H264H:				
					$videoCodec = new SimpleXMLElement(self::h264hCodecXml);
					$fileFormat = 'mp4';
					$cbr = 1;
					break;
			}
			if($target->_container->_id==KDLContainerTarget::ISMV) {
				$videoCodec['SmoothStreaming'] = 'True';
					/*
					 * The 'AutoSize' option is not valid with ConstantBR (cbr).
					 * CBR is the only option for H264.
					 */	
				$videoCodec->Streams['AutoSize'] = (isset($cbr)&& $cbr==1)?'False': 'True';
			}
			else {
				$videoCodec['SmoothStreaming'] = 'False';
				$videoCodec->Streams['AutoSize'] = 'False';
			}
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
					$videoBitrateElem->VariableConstrainedBitrate['PeakBitrate'] = round($vbr*1.3);
					$videoBitrateElem->VariableConstrainedBitrate['AverageBitrate'] = $vbr;
					KDLUtils::AddXMLElement($videoCodec->Streams->StreamInfo, $videoBitrateElem);
				}
				else {
					$videoBitrateElem = new SimpleXMLElement(self::videoConstantBitrateXml);
					$videoBitrateElem->ConstantBitrate['Bitrate'] = $vbr;
					if(isset($target->_isTwoPass) && $target->_isTwoPass==1){
						$videoBitrateElem->ConstantBitrate['IsTwoPass'] = 'True';
					}
					KDLUtils::AddXMLElement($videoCodec->Streams->StreamInfo, $videoBitrateElem);
				}
			}
			if($vidObj->_width!=null && $vidObj->_height!=null){
				$videoCodec->Streams->StreamInfo['Size'] = $vidObj->_width.", ".$vidObj->_height;
			}

 				/*
				 * For anamorphic pixels - update aspect ratio
				 */
 			if(isset($vidObj->_anamorphic) && $vidObj->_anamorphic>0 
			&& isset($vidObj->_dar) && $vidObj->_dar>0) { 		
 				if(abs(1-(16/9)/$vidObj->_dar)<0.1) {
					$videoCodec['AspectRatio'] = "16,9";
				}
				else if(abs(1-(4/3)/$vidObj->_dar)<0.1) {
					$videoCodec['AspectRatio'] = "4,3";
				}
				else if(abs(1-(5/4)/$vidObj->_dar)<0.1) {
					$videoCodec['AspectRatio'] = "5,4";
				}
				else {
					$w=round($vidObj->_height*$vidObj->_dar);
					$videoCodec['AspectRatio'] = "$w,$vidObj->_height";
				}
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
			
			if(isset($audObj->_channels) && $audObj->_channels>0)
				$audioCodec['Channels']=(string)$audObj->_channels;
	
			if(($audioCodec['Channels'] == '1')&&($audioCodec['Codec'] == 'Wma')&&($br > 32))
				$br = 32;			//Fix a bug in EE3 where WMA mono files don't support br > 32
				
			if(isset($audObj->_sampleRate))
				$sr = self::lookForClosest($audObj->_sampleRate, $codecSampleRates);
			else
				$sr = 44100;
			$audioBitrateElem->ConstantBitrate['Bitrate'] = (string)$br;
			
			KDLUtils::AddXMLElement($audioCodec, $audioBitrateElem);

            $audioCodec['BitsPerSample']="16";
            $audioCodec['SamplesPerSecond']=(string)$sr;
		}

$jobElem = null;
$outputFormat=null;
$defaultMediaOutputFileName = KDLCmdlinePlaceholders::OutFileName; // suits MP4/WMV targets. ISMV requires '{DefaultExtension}' as well
		if(isset($target->_container)) {
$contObj = $target->_container;
			switch($contObj->_id){
				case KDLContainerTarget::ISMV:
					if(isset($fileFormat) && $fileFormat=='mp4')
						$formatName='MP4OutputFormat';
					else
						$formatName='WindowsMediaOutputFormat';
					$defaultMediaOutputFileName = KDLCmdlinePlaceholders::OutFileName.".{DefaultExtension}";
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
			$vidObj=$target->_video;
			// Letterboxing
			if(isset($vidObj) && isset($vidObj->_arProcessingMode) && $vidObj->_arProcessingMode==2){
				$jobElem->MediaFile['VideoResizeMode'] = 'Letterbox';
			}
			$outputFormat=$jobElem->MediaFile->OutputFormat->addChild($formatName);
		}
		
		/*
		 * Set EE3 cliping if the target is clipped 
		 */
		$sourcesElem = new SimpleXMLElement(self::clippedSourcesXml);
		$clipElem = null;
		if(isset($target->_clipStart) && $target->_clipStart>0){
			$clipStartStr = self::formatTimesForEE3Xml($target->_clipStart);
			$clipElem = $sourcesElem->Source->Clips->Clip;
			$clipElem['StartTime']= $clipStartStr;
		}
		if(isset($target->_explicitClipDur) && $target->_explicitClipDur>0){
			$clipEndStr = self::formatTimesForEE3Xml($target->_clipStart+$target->_explicitClipDur);
			$clipElem = $sourcesElem->Source->Clips->Clip;
			$clipElem['EndTime'] = $clipEndStr;
		}
		if(isset($clipElem)){
			KDLUtils::AddXMLElement($jobElem->MediaFile, $sourcesElem);
		}
		
		if(isset($audioProfileElem)) {
			KDLUtils::AddXMLElement($outputFormat, $audioProfileElem);
		}
		if(isset($videoProfileElem)) {
			KDLUtils::AddXMLElement($outputFormat, $videoProfileElem->VideoProfile);
		}
		
		$jobElem->Job['OutputDirectory']=KDLCmdlinePlaceholders::OutDir;
		if(isset($outFileName)){
			$jobElem->Job['DefaultMediaOutputFileName']=$outFileName;
		}
		else {
			$jobElem->Job['DefaultMediaOutputFileName']=$defaultMediaOutputFileName;
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
			/*
		 	* Filter in the flavors with EE operator
		 	*/
		$flavorInColl = array();
		foreach ($flavors as $flavor){
			$eeId = KDLOperationParams::SearchInArray(KDLTranscoders::EE3, $flavor->_transcoders);
			if(is_null($eeId)) {
				continue;
			}
		$transcoderParams = $flavor->_transcoders[$eeId];
KalturaLog::log("transcoder==>\n".print_r($transcoderParams,true)."\n<--");
			if(is_null($transcoderParams->_cmd)){
				KalturaLog::log("ee3 cmd is null");
				continue;
			}
			/*
			 * The key if this array will be combination of bitrate,hight and counter, 
			 * in order to solve possible duplication issues and easy the flavor sorting from low to high - 
			 * - vBr*100000+height*10+counter
			 * The counter meant to solve cases (forced) were two (or more) flavors have both the same vr and height 
			 */
			$k = ($flavor->_video->_bitRate*100000+$flavor->_video->_height*10);
			while(array_key_exists(strval($k),$flavorInColl)){
				$k++;
			}
			$flavorInColl[$k] = $flavor;
		}
			/*
			 * Sort the flavors that participate in collection 
			 */
		$rv=ksort($flavorInColl);
		
			/*
			 * Buidl a combined SmoothSteaming preset XML
			 */
		$prevK = null;
		foreach ($flavorInColl as $k=>$flavor){
			/*
			 * Check for IsmvMinimalFlavorRatio compliance,
			 * fix if required.
			 */
			if(isset($prevK)) {
				$ratio = $flavor->_video->_bitRate/$flavorInColl[$prevK]->_video->_bitRate;
				if($ratio<KDLConstants::IsmvMinimalFlavorRatio){
					$flavor->_video->_bitRate = round($flavorInColl[$prevK]->_video->_bitRate*KDLConstants::IsmvMinimalFlavorRatio);
					$flavor->_video->_peakBitRate = round($flavor->_video->_bitRate * KDLConstants::IsmvPeakBitrateRatio * 1.1);
				}
			}
			$prevK = $k;
		}
		
			/*
			 * Sort the flavors that participate in collection - from high to low, to match EE4 constraints
			 */
		$rv=krsort($flavorInColl);
		
			/*
			 * Update the preset XML's and build combined XML
			 */
		$rootFlavorXml=null;
		$rootStreamsXml=null;
		foreach ($flavorInColl as $k=>$flavor){
			$eeId = KDLOperationParams::SearchInArray(KDLTranscoders::EE3, $flavor->_transcoders);
				
			$transcoderParams = $flavor->_transcoders[$eeId];
			$presetXml = new SimpleXMLElement($transcoderParams->_cmd);
			$streamsXml = self::updateToCollectionPreset($flavor, $presetXml);
		
			if($rootFlavorXml==null) {
				$rootFlavorXml  = $presetXml;
				$rootStreamsXml = $streamsXml;
			}
			else if($streamsXml && isset($streamsXml->StreamInfo) && $rootStreamsXml/*&& is_array($streams->StreamInfo)*/) {
				KDLUtils::AddXMLElement($rootStreamsXml, $streamsXml->StreamInfo);
			}
		}
		
		
		if($rootFlavorXml){
			$rootFlavorXml->Job['DefaultMediaOutputFileName']=KDLCmdlinePlaceholders::OutFileName.".{DefaultExtension}";
			return $rootFlavorXml->asXML();
		}
		else
			return null;
	}
	
	/* ------------------------------
	 * updateToCollectionPreset
	 */
	private static function updateToCollectionPreset($flavor, $presetXml)
	{
//		$transcoderParams = $flavor->_transcoders[$eeId];
//		$presetXml = new SimpleXMLElement($transcoderParams->_cmd);
		if(isset($presetXml->MediaFile->OutputFormat->WindowsMediaOutputFormat->VideoProfile))
			$videoProfile = $presetXml->MediaFile->OutputFormat->WindowsMediaOutputFormat->VideoProfile;
		else if(isset($presetXml->MediaFile->OutputFormat->MP4OutputFormat->VideoProfile))
			$videoProfile = $presetXml->MediaFile->OutputFormat->MP4OutputFormat->VideoProfile;
		if(!isset($videoProfile)){
			return null;
		}
		switch($flavor->_video->_id){
			case KDLVideoTarget::WVC1A:
				$videoCodec = $videoProfile->AdvancedVC1VideoProfile;
				break;
			case KDLVideoTarget::H264:
			case KDLVideoTarget::H264M:
				$videoCodec = $videoProfile->MainH264VideoProfile;
				break;
			case KDLVideoTarget::H264H:
				$videoCodec = $videoProfile->HighH264VideoProfile;
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
			return null;
		$streams = $videoCodec->Streams;
		if(!(isset($streams) && isset($streams->StreamInfo))) {
			return null;
		}
		
		$flavorVideoBr = round($flavor->_video->_bitRate);
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
				if(isset($flavor->_video->_peakBitRate)){
					$br->VariableConstrainedBitrate['PeakBitrate']=round($flavor->_video->_peakBitRate);
				}
				else {
					$br->VariableConstrainedBitrate['PeakBitrate']=round($flavorVideoBr*KDLConstants::IsmvPeakBitrateRatio);
				}
			}
				/*
				 * EE4 constraint - no PeakBufferWindow for high flavors.
				 * The PeakBufferWindow is probably required for mobile/low-hw 
				 * devices that does not run HD content
				 */
			if($flavorVideoBr>1500){
				$br->VariableConstrainedBitrate['PeakBufferWindow'] = "00:00:00";
			}
		}
			
		return $streams;
	}
	
	/* ------------------------------
	 * lookForClosest
	 */
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
	
	/* ------------------------------
	 * lookForClosest
	 */
	private static function formatTimesForEE3Xml($timeInMsec)
	{
		$msecs = $timeInMsec%1000;
		$secs = floor($timeInMsec/1000);
		$dt = new DateTime("@$secs");
		$str = $dt->format('H:i:s');
		return "$str.".sprintf("%03d",$msecs);
		
	}
}
