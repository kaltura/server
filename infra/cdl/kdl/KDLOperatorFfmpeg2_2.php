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
			 * origionaly required by x265, but 
			 */
		if($vid->_arProcessingMode==4 && isset($vid->_width) && $vid->_width>0 && isset($vid->_height) && $vid->_height>0){
			$cmdStr.= " -aspect $vid->_width:$vid->_height"; 
		}
		return $cmdStr;
	}
}
