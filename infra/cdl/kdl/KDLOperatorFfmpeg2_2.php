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
