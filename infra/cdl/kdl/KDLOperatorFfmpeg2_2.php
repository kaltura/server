<?php
/**
 * @package plugins.ffmpeg
 * @subpackage lib
 */
class KDLOperatorFfmpeg2_2 extends KDLOperatorFfmpeg2_1_3 {

	/* ---------------------------
	 * generateVideoParams
	 */
	protected function generateVideoParams(KDLFlavor $design, KDLFlavor $target)
	{
		$cmdStr = parent::generateVideoParams($design, $target);
		if(!isset($target->_video))
			return $cmdStr;
	
		$vid = $target->_video;
			/*
			 * Force explicit aspect ratio setting
			 * for following cases:
			 * - x265
			 * - x264
			 * - on _arProcessingMode==4
			 */
		if((isset($vid->_width) && $vid->_width>0 && isset($vid->_height) && $vid->_height>0) 
		&& (in_array($vid->_id, array(KDLVideoTarget::H265,KDLVideoTarget::H264,KDLVideoTarget::H264B,KDLVideoTarget::H264M,KDLVideoTarget::H264H))
			|| $vid->_arProcessingMode==4)){

			/*
			 * Look for frame-size operand,
			 * use it to generate 'aspect' operand
			 */
			$cmdValsArr = explode(' ', $cmdStr);
			if(in_array('-s', $cmdValsArr)) {
				$key = array_search('-s', $cmdValsArr);
				$aspectStr  = str_replace("x", ":", $cmdValsArr[$key+1]);
				$cmdStr.= " -aspect $aspectStr"; 
			}
		}
		return $cmdStr;
	}
}

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
			/*
			 * There is some quality degradation on old-style VP8 cmd line.
			 * 'qmax=8' fixes it. 
			 */
		$vidCodecSpecStr = parent::getVideoCodecSpecificParams($design, $target);
		if($target->_video->_id==KDLVideoTarget::VP8) {
			$vidCodecSpecStr.= " -quality good -cpu-used 0 -qmin 10";
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

