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
			 * and Support turning 9:16 content to 16:9, via padding
			 * - on on _arProcessingMode==6
			 */
		if(isset($vid->_width) && $vid->_width>0 
		&& isset($vid->_height) && $vid->_height>0) {
			if(in_array($vid->_id, array(KDLVideoTarget::H265,KDLVideoTarget::H264,KDLVideoTarget::H264B,KDLVideoTarget::H264M,KDLVideoTarget::H264H))
			|| $vid->_arProcessingMode==4){

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
				// 9:16 to 16:9
			if($vid->_arProcessingMode==6){
				$cmdValsArr = explode(' ', $cmdStr);
				if(($key=array_search('-s', $cmdValsArr))!==false) {
					$dims = explode('x', $cmdValsArr[$key+1]);
					$sizeStr = "$dims[1]x$dims[0]";
					$cmdStr = str_replace($cmdValsArr[$key+1], $sizeStr, $cmdStr);
				}
				if(($key=array_search('-aspect', $cmdValsArr))!==false) {
					$ar = explode(':', $cmdValsArr[$key+1]);
					$aspectStr = "$ar[1]:$ar[0]";
					$cmdStr = str_replace($cmdValsArr[$key+1], $aspectStr, $cmdStr);
				}
				else if(isset($dims)) {
					$cmdStr.= " -aspect $dims[1]:$dims[0]";
				}
			}
		}
		return $cmdStr;
	}
}
