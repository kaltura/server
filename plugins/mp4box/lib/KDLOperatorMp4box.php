<?php
/**
 * @package plugins.mp4box
 * @subpackage lib
 */
class KDLOperatorMp4box extends KDLOperatorBase {
    public function __construct($id, $name=null, $sourceBlacklist=null, $targetBlacklist=null) {
    	parent::__construct($id,$name,$sourceBlacklist,$targetBlacklist);
    }

	/* ---------------------------
	 * GenerateCommandLine
	 */
    public function GenerateCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
	{
		
/*
MP4Box -hint C:\Users\Anatol\Downloads\src_3.3gp -out c:\tmp\rtsp_3.3gp
 */
$cmdStr = "-hint";

$format = "fl";
$acodec = "libmp3lam";

		if(isset($target->_inFileName)){
			$cmdStr .= " \"".$target->_inFileName."\"";
		}
		else {
			$cmdStr .= " \"".KDLCmdlinePlaceholders::InFileName."\"";
		}

		$cmdStr .= " -out";
		if(isset($target->_outFileName)){
			$cmdStr .= " \"".$target->_outFileName."\"";
		}
		else {
			$cmdStr .= " \"".KDLCmdlinePlaceholders::OutFileName."\"";
		}
		return $cmdStr;
	}
	

}
	