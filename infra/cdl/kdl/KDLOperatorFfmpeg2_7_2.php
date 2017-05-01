<?php
/**
 * @package plugins.ffmpeg
 * @subpackage lib
 */

	/**
	 * 
	 * KDLOperatorFfmpeg2_7_2
	 *
	 */
class KDLOperatorFfmpeg2_7_2 extends KDLOperatorFfmpeg2_2 {
	
	/* ---------------------------
	 * generateSinglePassCommandLine
	 */
	public function generateSinglePassCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
	{
		$cmdStr = parent::generateSinglePassCommandLine($design, $target, $extra);
		if(!isset($cmdStr)) 
			return null;
		
		if(isset($target->_decryptionKey)){
			$cmdStr = "-decryption_key $target->_decryptionKey $cmdStr";
		}
		if($target->_isEncrypted==true) {
				// Add key & key_if placeholder. To be replaced by real values after asset creation
			$str = " -encryption_scheme cenc-aes-ctr";
			$str.= " -encryption_key ".KDLFlavor::ENCRYPTION_KEY_PLACEHOLDER;
			$str.= " -encryption_kid ".KDLFlavor::ENCRYPTION_KEY_ID_PLACEHOLDER." -y ";
			$cmdStr = str_replace(" -y ", $str, $cmdStr);
			KalturaLog::log("On Encryption: $cmdStr");
		}
		return $cmdStr;
	}
		
	/* ---------------------------
	 * processTwoPass
	 */
    protected function processTwoPass(KDLFlavor $target, $cmdStr)
	{
		if(!isset($target->_isTwoPass) || $target->_isTwoPass==0)
			return $cmdStr;

		if($target->_video->_id!=KDLVideoTarget::H265) {
			return parent::processTwoPass($target, $cmdStr);
		}
$nullDev = "NUL";
$nullDev ="/dev/null";
		$statsLogFile = KDLCmdlinePlaceholders::OutFileName.".2pass.log";
		$x265PassParams = "stats=$statsLogFile:pass";
		
		$cmdValsArr = explode(' ', $cmdStr);

		$outFileNameKey=array_search(KDLCmdlinePlaceholders::OutFileName, $cmdValsArr);
		$cmdValsArr[$outFileNameKey] = "-an $nullDev";
		
		if(($x265ParamsKey=array_search('-x265-params', $cmdValsArr))!==false){
			$x265PassParams = $cmdValsArr[$x265ParamsKey+1].":$x265PassParams";
			$cmdValsArr[$x265ParamsKey+1] = "$x265PassParams=1:slow-firstpass=0";
		}
		else {
			$cmdValsArr[$outFileNameKey] = "-x265-params $x265PassParams=1:slow-firstpass=0 ".$cmdValsArr[$outFileNameKey];
		}
		$pass1cmdLine = implode(' ', $cmdValsArr);
		
		if($x265ParamsKey!==false) {
			$cmdValsArr[$x265ParamsKey+1] = "$x265PassParams=2";
			$cmdValsArr[$outFileNameKey] = KDLCmdlinePlaceholders::OutFileName;
		}
		else {
			$cmdValsArr[$outFileNameKey] = "-x265-params $x265PassParams=2 ".KDLCmdlinePlaceholders::OutFileName;
		}
		$pass2cmdLine = implode(' ', $cmdValsArr);

		$cmdStr = "$pass1cmdLine && ".KDLCmdlinePlaceholders::BinaryName." $pass2cmdLine";
		return $cmdStr;
	}
	
	/* ---------------------------
	 * generateAudioParams
	 */
	protected function generateAudioParams(KDLFlavor $design, KDLFlavor $target)
	{
		$cmdStr = parent::generateAudioParams($design, $target);
		if(isset($target->_audio)) {
			$cmdValsArr = explode(' ', $cmdStr);
			if(($key=array_search('-channel_layout', $cmdValsArr))!==false){
				$cmdValsArr[$key+1].= "c";
				$cmdStr = implode(" ", $cmdValsArr);
			}
		}
		return $cmdStr;
	}
	
	/**
	 * generateVideoFilters
	 * @param $vid
	 * @return array of filters 
	 */
	protected static function generateVideoFilters($vid)
	{
		/*
		 * FFMpeg 2.7 automatically rotates the output 
		 * into 'non-rotated' orientation. No need to do it explicitly 
		 */
		$rotation = null;
		if(isset($vid->_rotation)) {
			$rotation = $vid->_rotation;
			$vid->_rotation = null;
		}
		$filters = parent::generateVideoFilters($vid);
		$vid->_rotation = $rotation;
		return $filters;
	}

	/* ---------------------------
	 * getVideoCodecSpecificParams
	 */
	protected function getVideoCodecSpecificParams(KDLFlavor $design, KDLFlavor $target)
	{
		switch ($target->_video->_id){
			case  KDLVideoTarget::VP8:
				/*
				 * There is some quality degradation on old-style VP8 cmd line.
				 * 'qmax=8' fixes it. 
				 */
				$vidCodecSpecStr = "libvpx -quality good -cpu-used 0 -qmin 10";
				break;
			case  KDLVideoTarget::VP9:
				$vidCodecSpecStr = "libvpx-vp9".$this->calcForcedKeyFrames($target->_video, $target);
				break;
			case  KDLVideoTarget::H265:
				$vidCodecSpecStr = "libx265".$this->calcForcedKeyFrames($target->_video, $target);
				$vidCodecSpecStr.= " -x265-params min-keyint=1";
				if(isset($target->_video->_gop) && $target->_video->_gop>0){
					$vidCodecSpecStr.= ":keyint=".$target->_video->_gop;
				}
				break;
			default:
				$vidCodecSpecStr = parent::getVideoCodecSpecificParams($design, $target);
				break;
		}
		
		return $vidCodecSpecStr;
	}

	/**
	 *
	 * @param unknown_type $targetVid
	 * @return string
	 */
	protected static function generateWatermarkParams($targetVid, $vidIn)
	{
		/*
		 * FFMpeg 2.7 automatically rotates the output 
		 * into 'non-rotated' orientation. No need to do it explicitly 
		 */
$rotation = null;
		if(isset($targetVid->_rotation)) {
			$rotation = $targetVid->_rotation;
			$targetVid->_rotation = null;
		}
		$watermarkStr = parent::generateWatermarkParams($targetVid, $vidIn);
		$targetVid->_rotation = $rotation;
		return $watermarkStr;
	}

	/**
	 * 
	 * @param unknown_type $targetVid
	 * @param array $cmdValsArr
	 */
	protected static function adjustVideoCodecSpecificParams($targetVid, array &$cmdValsArr)
	{
		if(isset($targetVid) && ($targetVid->_id==KDLVideoTarget::H265)){
			$keys=array_keys($cmdValsArr, "-x265-params");
			if(count($keys)==1) {
				$cmdValsArr[$keys[0]+1].=":pools=4:aq-mode=3:bframes=4:ref=2:limit-refs=3:rc-lookahead=15:subme=2";
			}
			else
				self::mergeOpts("-x265-params", $cmdValsArr);
		}
		else 
			parent::adjustVideoCodecSpecificParams($targetVid, $cmdValsArr);
	}
	
	/* ---------------------------
	 * CheckConstraints
	 */
	public function CheckConstraints(KDLMediaDataSet $source, KDLFlavor $target, array &$errors=null, array &$warnings=null)
	{
		$isEncrypted = $target->_isEncrypted;
		if($target->_isEncrypted==true) {
			$target->_isEncrypted = false;
		}
		$rv = parent::CheckConstraints($source, $target, $errors, $warnings);
		$target->_isEncrypted = $isEncrypted;
		return $rv;
	}
	
}

