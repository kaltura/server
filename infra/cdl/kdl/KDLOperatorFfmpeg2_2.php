<?php
/**
 * @package plugins.ffmpeg
 * @subpackage lib
 */
class KDLOperatorFfmpeg2_2 extends KDLOperatorFfmpeg2_1_3 {

	/* ---------------------------
	 * getVideoCodecSpecificParams
	 */
	protected function getVideoCodecSpecificParams(KDLFlavor $design, KDLFlavor $target)
	{
		$vid = $target->_video;
		if($vid->_id==KDLVideoTarget::H265) {
			$cmdStr = "libx265 -pix_fmt yuv420p";
			if((isset($vid->_width) && $vid->_width>0)
			&& (isset($vid->_height) && $vid->_height>0)){
				$cmdStr.= " -aspect $vid->_width:$vid->_height";
			}
			return $cmdStr;
		}
		return parent::getVideoCodecSpecificParams($design, $target);
	}
}