<?php
 
	/* ===========================
	 * KDLOperatorExpressionEncoder
	 */
class KDLOperatorExpressionEncoder extends KDLOperatorBase {
    public function __construct($id, $name=null, $sourceBlacklist=null, $targetBlacklist=null) {
    	parent::__construct($id,$name,$sourceBlacklist,$targetBlacklist);
    }

    public function GenerateConfigData(KDLFlavor $design, KDLFlavor $target, $extra=null)
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

		if($target->_container) {
			$cont = $target->_container;
			$dir = dirname(__FILE__);
//$dir = '.';
			switch($cont->_id){
				case KDLContainerTarget::ISMV:
					$xmlTemplate = $dir.'/ismPresetTemplate.xml';
					break;
				case KDLContainerTarget::MP4:
				case KDLContainerTarget::WMV:
				case KDLContainerTarget::WMA:
					default:
					$xmlTemplate = $dir.'/wmvPresetTemplate.xml';
					break;
			}
			$xml = simplexml_load_file(realpath($xmlTemplate));
			switch($cont->_id){
				case KDLContainerTarget::MP4:
					$xObj = simplexml_load_string($xml->MediaFile->OutputFormat->WindowsMediaOutputFormat->asXML());
					$xml->MediaFile->OutputFormat->addChild("MP4OutputFormat");
					KDLUtils::AddXMLElement($xml->MediaFile->OutputFormat->MP4OutputFormat, $xObj->AudioProfile);
					KDLUtils::AddXMLElement($xml->MediaFile->OutputFormat->MP4OutputFormat, $xObj->VideoProfile);
					unset($xml->MediaFile->OutputFormat->WindowsMediaOutputFormat);
					$fileFormat = $xml->MediaFile->OutputFormat->MP4OutputFormat;
					break;
				case KDLContainerTarget::ISMV:
				case KDLContainerTarget::WMV:
				case KDLContainerTarget::WMA:
				default:
					$fileFormat = $xml->MediaFile->OutputFormat->WindowsMediaOutputFormat;
					break;
			}
		}
		
		$xml->Job['OutputDirectory']=KDLCmdlinePlaceholders::OutDir;

		if($target->_video){
			$vid = $target->_video;
			$vidProfile=null;
			switch($vid->_id){
				case KDLVideoTarget::WMV2:
				case KDLVideoTarget::WMV3:
				case KDLVideoTarget::WVC1A:
				default:
					$vidProfile = $fileFormat->VideoProfile->AdvancedVC1VideoProfile;
					unset($fileFormat->VideoProfile->MainH264VideoProfile);					
					break;
				case KDLVideoTarget::H264:
				case KDLVideoTarget::H264B:
				case KDLVideoTarget::H264M:
				case KDLVideoTarget::H264H:				
					$vidProfile = $fileFormat->VideoProfile->MainH264VideoProfile;
					unset($fileFormat->VideoProfile->AdvancedVC1VideoProfile);					
					break;
			}
			$vFr = 30;
			if($vid->_frameRate!==null && $vid->_frameRate>0){
				$vFr = $vid->_frameRate;
				$vidProfile['FrameRate']=$vFr;
			}
			if($vid->_gop!==null && $vid->_gop>0){
				$kFr = round($vid->_gop/$vFr);
				$mi = round($kFr/60);
				$se = $kFr%60;
				$vidProfile['KeyFrameDistance']=sprintf("00:%02d:%02d",$mi,$se);
			}
			if($vid->_bitRate){
				if($target->_isTwoPass && !($vid->_id==KDLVideoTarget::H264 || $vid->_id==KDLVideoTarget::H264B || $vid->_id==KDLVideoTarget::H264M || $vid->_id==KDLVideoTarget::H264H)) {
					unset($vidProfile->Streams->StreamInfo->Bitrate->VariableConstrainedBitrate);
					$vidProfile->Streams->StreamInfo->Bitrate->ConstantBitrate['Bitrate'] = $vid->_bitRate;
					
				}
				else {
					unset($vidProfile->Streams->StreamInfo->Bitrate->ConstantBitrate);
					$vid->_bitRate=max(100,$vid->_bitRate); // The minimum video br for the SL is 100
					$vidProfile->Streams->StreamInfo->Bitrate->VariableConstrainedBitrate['PeakBitrate'] = round($vid->_bitRate*1.3);
					$vidProfile->Streams->StreamInfo->Bitrate->VariableConstrainedBitrate['AverageBitrate'] = $vid->_bitRate;
				}
			}
			if($vid->_width!=null && $vid->_height!=null){
				$vidProfile->Streams->StreamInfo['Size'] = $vid->_width.", ".$vid->_height;
			}
			
//			$strmInfo = clone ($vidProfile->Streams->StreamInfo[0]);
//			KDLUtils::AddXMLElement($vidProfile->Streams, $vidProfile->Streams->StreamInfo[0]);
			
		}
		else {
			unset($fileFormat->VideoProfile);				
		}

		if($target->_audio){
			$aud = $target->_audio;
			$audProfile=null;
			switch($aud->_id){
				case KDLAudioTarget::WMA:
				default:
					$audProfile = $fileFormat->AudioProfile->WmaAudioProfile;
					unset($fileFormat->AudioProfile->AacAudioProfile);					
					break;
				case KDLAudioTarget::AAC:
					$audProfile = $fileFormat->AudioProfile->AacAudioProfile;
					unset($fileFormat->AudioProfile->WmaAudioProfile);					
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
		else {
			unset($fileFormat->AudioProfile->WmaAudioProfile);					
			unset($fileFormat->AudioProfile->AacAudioProfile);					
			unset($fileFormat->AudioProfile);							
		}
//$stream = clone $streams->StreamInfo;
//		$streams[1] = $stream;
		//		print_r($xml);
		return $xml->asXML();
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
}

