<?php
/**
 * @package plugins.on2
 * @subpackage lib
 */
class KDLOperatorOn2 extends KDLOperatorBase {
/*
    public function __construct($id, $name=null, $sourceBlacklist=null, $targetBlacklist=null) {
    	parent::__construct($id, $name, $sourceBlacklist,$targetBlacklist);
    }
*/
	/* ---------------------------
	 * GenerateCommandLine
	 */
    public function GenerateCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
	{
	$cmdStr = null;

		$cmdStr.= "-i ".KDLCmdlinePlaceholders::InFileName;
		
		$cmdStr.= $this->generateVideoParams($design, $target);
		$cmdStr.= $this->generateAudioParams($design, $target);
		
		if($target->_isTwoPass)
			$cmdStr.= " --FE2_VP6_CXMODE=1 --FE2_VP6_RC_MODE=3";
		
		if($target->_clipStart!==null && $target->_clipStart>0){
			$cmdStr.= " --FE2_CUT_START_SEC=".$target->_clipStart/1000;
		}
		
		if($target->_clipDur!==null && $target->_clipDur>0){
			$cmdStr.= " --FE2_CUT_STOP_SEC=".$target->_clipDur/1000;
		}
		
		if($extra)
			$cmdStr.= " ".$extra;
		
		$cmdStr.= " -o ".KDLCmdlinePlaceholders::OutFileName;

		return $cmdStr;

	}
	
	/* ---------------------------
	 * generateVideoParams
	 */
    protected function generateVideoParams(KDLFlavor $design, KDLFlavor $target)
	{
		if(!isset($target->_video)){
			return null;
		}

$vcodecParams = "fl";
$cmdStr = null;
$vid = $target->_video;

		switch($vid->_id){
			case KDLVideoTarget::H264:
				$cmdStr.= " -c H264";
				break; 				
			case KDLVideoTarget::H264B:
				$cmdStr.= " -c H264 --FE2_H264_PROFILE=0";
				break; 				
			case KDLVideoTarget::H264M:
				$cmdStr.= " -c H264 --FE2_H264_PROFILE=1";
				break; 				
			case KDLVideoTarget::H264H:
				$cmdStr.= " -c H264 --FE2_H264_PROFILE=2";
				break; 				
			case KDLVideoTarget::VP6:
			case KDLVideoTarget::H263:
			default:
				$cmdStr.= " -c VP6";
				break;
		}

		if($vid->_frameRate)
			$cmdStr.= " -r ".round($vid->_frameRate);
		
		if($vid->_bitRate)
			$cmdStr.= " -b ".$vid->_bitRate;
		if($vid->_gop!==null && $vid->_gop>0) 
			$cmdStr.= " -k ".$vid->_gop;
		if($vid->_width && $vid->_height){
			if(is_null($vid->_rotation) || $vid->_rotation==0  || $vid->_rotation==180)
				$cmdStr.= " -w ".$vid->_width." -h ".$vid->_height;
			else
				$cmdStr.= " -h ".$vid->_width." -w ".$vid->_height;
		}
		if($vid->_scanType!==null && $vid->_scanType>0){ // ScanType 0:progressive, 1:interlaced
			$cmdStr.= " --deinterlace=1";
		}
		
		return $cmdStr;
	}
	
	/* ---------------------------
	 * generateAudioParams
	 */
    protected function generateAudioParams(KDLFlavor $design, KDLFlavor $target)
	{
		if(!isset($target->_audio)) {
			return null;
		}

$cmdStr = null;
$aud = $target->_audio;
		if($aud->_bitRate!==null)
			$cmdStr.= " -a ".$aud->_bitRate;
		if($aud->_sampleRate)
			$cmdStr.= " -s ".$aud->_sampleRate;
		
		return $cmdStr;
	}

	/* ---------------------------
	 * CheckConstraints
	 */
	public function CheckConstraints(KDLMediaDataSet $source, KDLFlavor $target, array &$errors=null, array &$warnings=null)
	{
	    if(parent::CheckConstraints($source, $target, $errors, $warnings)==true)
			return true;

		/*
		 * Remove mencoder, encoding.com and cli_encode
		 * for audio only flavors
		 */
		if($target->_video==null) {
			$warnings[KDLConstants::AudioIndex][] = //"The transcoder (".$key.") does not handle properly DAR<>PAR.";
				KDLWarnings::ToString(KDLWarnings::TranscoderLimitation, $this->_id);
			return true;
		}

		/*
		 * Remove On2
		 * for 270 rotated videos
		 */
		if($target->_video && $target->_video->_rotation==270) {
			$warnings[KDLConstants::VideoIndex][] = //"The transcoder (".$key.") does not handle properly DAR<>PAR.";
				KDLWarnings::ToString(KDLWarnings::TranscoderLimitation, $this->_id);
			return true;
		}

				// Encryption unsupported by On2
		if($target->_isEncrypted==true){
			$warnings[KDLConstants::ContainerIndex][] = 
				KDLWarnings::ToString(KDLWarnings::TranscoderLimitation, $this->_id)."(encryption)";
			return true;
		}
		return false;
	}
}
