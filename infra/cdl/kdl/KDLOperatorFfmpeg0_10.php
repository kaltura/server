<?php
/**
 * @package plugins.ffmpeg
 * @subpackage lib
 */
class KDLOperatorFfmpeg0_10 extends KDLOperatorFfmpeg {
/*
    public function __construct($id, $name=null, $sourceBlacklist=null, $targetBlacklist=null) {
    	parent::__construct($id,$name,$sourceBlacklist,$targetBlacklist);
    }
*/
	/* ---------------------------
	 * GenerateCommandLine
	 */
    public function GenerateCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
	{
		$cmdStr = parent::GenerateCommandLine($design, $target, $extra);
		if($target->_isTwoPass) {
	$pass2params = "-passlogfile ".KDLCmdlinePlaceholders::OutFileName.".2pass.log -pass";

$nullDev = "NUL";
$nullDev ="/dev/null";
			$pass1cmdLine =
				str_replace ( 
					array(KDLCmdlinePlaceholders::OutFileName, " -y"), 
					array($nullDev, " -an $pass2params 1 -fastfirstpass 1 -y"),
					$cmdStr);

			$pass2cmdLine =
				str_replace ( 
					array(" -y"), 
					array(" $pass2params 2 -y"),
					$cmdStr);
			$cmdStr = "$pass1cmdLine && ".KDLCmdlinePlaceholders::BinaryName." $pass2cmdLine ";
		}
		return $cmdStr;
	}
	
	/* ---------------------------
	 * generateVideoParams
	 */
    protected function generateVideoParams(KDLFlavor $design, KDLFlavor $target)
	{
		$cmdStr = parent::generateVideoParams($design, $target);
		if(!isset($target->_video))
			return $cmdStr;

$vid = $target->_video;
$forcedKF=0;
		switch($vid->_id) {
		case KDLVideoTarget::H264:
		case KDLVideoTarget::H264B:
			$cmdStr.=" -vprofile baseline";
			break;
		case KDLVideoTarget::H264M:
			$cmdStr.=" -vprofile main";
			break;
		case KDLVideoTarget::H264H:				
			$cmdStr.=" -vprofile high";
			break;
		default:
			$forcedKF=null;	// forced keyframes are required only for h264 (meanwhile)
							// this var will be used as a flag for further stages,
			break;
		}
//print_r($target); die;
		if(isset($forcedKF) and isset($vid->_gop) && isset($vid->_frameRate) && $vid->_frameRate>0){
			$forcedKF=null;
			$gopInSecs=($vid->_gop/$vid->_frameRate);
			for($t=0,$tr=0;$t<=$target->_container->_duration/1000; $t+=$gopInSecs, $tr+=round($gopInSecs)){
				if(round($t)>$tr) {
					echo "$t,$tr\n"; $t=$tr;
				}
				$forcedKF.=",".round($t,4);
			}
			$forcedKF[0] = ' ';
			$cmdStr.= " -force_key_frames$forcedKF";
		}
		
		if(isset($vid->_rotation)) {
			if($vid->_rotation==180)
				$cmdStr.= " -vf vflip";
			else if($vid->_rotation==90)
				$cmdStr.= " -vf transpose=1";
			else if($vid->_rotation==270 || $vid->_rotation==-90)
				$cmdStr.=" -vf transpose=2";
		}
		$cmdStr.=" -pix_fmt yuv420p";

		return $cmdStr;
	}
	
	/* ---------------------------
	 * CheckConstraints
	 */
	public function CheckConstraints(KDLMediaDataSet $source, KDLFlavor $target, array &$errors=null, array &$warnings=null)
	{
	    if(KDLOperatorBase::CheckConstraints($source, $target, $errors, $warnings)==true)
			return true;

		/*
		 * Remove ffmpegs
		 * for rotated videos
		 
		if($target->_video && $target->_video->_rotation) {
			$warnings[KDLConstants::VideoIndex][] = //"The transcoder (".$key.") does not handle properly DAR<>PAR.";
				KDLWarnings::ToString(KDLWarnings::TranscoderLimitation, $this->_id);
			return true;
		}*/

		/*
		 * Non Mac transcoders should not mess up with QT/WMV/WMA
		 * 
		 */
		$qt_wmv_list = array("wmv1","wmv2","wmv3","wvc1","wmva","wma1","wma2","wmapro");
		if($source->_container && ($source->_container->_id=="qt" || $source->_container->_format=="qt")
		&& (
			($source->_video && (in_array($source->_video->_format,$qt_wmv_list)||in_array($source->_video->_id,$qt_wmv_list)))
			||($source->_audio && (in_array($source->_audio->_format,$qt_wmv_list)||in_array($source->_audio->_id,$qt_wmv_list)))
			)
		){
			$warnings[KDLConstants::VideoIndex][] = //"The transcoder (".$key.") can not process the (".$sourcePart->_id."/".$sourcePart->_format. ").";
				KDLWarnings::ToString(KDLWarnings::TranscoderFormat, $this->_id, "qt/wmv/wma");
			return true;
		}
		return false;
	}
}
	