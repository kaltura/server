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
		if(!isset($target->_video)) {
			return " -vn";
		}
	
		$vid = $target->_video;
		if($vid->_id==KDLVideoTarget::H265) {
			$cmdStr = " -c:v libx265";
			if((isset($vid->_width) && $vid->_width>0)
			&& (isset($vid->_height) && $vid->_height>0)){
				$cmdStr.= " -aspect $vid->_width:$vid->_height";
			}
			return $cmdStr;
		}
		return parent::generateVideoParams($design, $target);
	}
	
}
	