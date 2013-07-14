<?php
/**
 * @package plugins.segmenter
 * @subpackage lib
 * 
 * Segmenter plugin accepts 'segmentDuration' settings in the 'extra' field json operator
 */
class KDLOperatorSegmenter extends KDLOperatorBase {
    public function __construct($id, $name=null, $sourceBlacklist=null, $targetBlacklist=null) {
    	parent::__construct($id,$name,$sourceBlacklist,$targetBlacklist);
    }

	/* ---------------------------
	 * GenerateCommandLine
	 */
    public function GenerateCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
	{
	//segmenter 0_3eq4pxgw_0_j5b7ubqa_1.mpeg 2 zzz/segment zzz/playlist.m3u8 ./
	// out_dummyk:/opt/kaltura/tmp/convert/convert_0_6olnx72l_4a32a//out_dummy-1.ts
	
		/*
		 * The segment duration can be evaluated from the 'segmentDuration' field in the operator->extra
		 * or set to the gop value if such values are available.
		 * Otherwise set to 10.
		 */
	$segmentDur = 10;
		$paramsMap = KDLUtils::parseParamStr2Map($extra);
		if (isset($paramsMap) && array_key_exists('segmentDuration', $paramsMap)) {
			$auxDur = $paramsMap['segmentDuration'];
			if($auxDur>0)
				$segmentDur=$auxDur;
		}
/*		else
		if(isset($target->_video) 
		&&(isset($target->_video->_gop) && $target->_video->_gop>0)
		&&(isset($target->_video->_frameRate) && $target->_video->_frameRate>0)){
			$auxDur = round($target->_video->_gop/$target->_video->_frameRate);
			if($auxDur>0)
				$segmentDur=$auxDur;
		}
*/
		$cmdStr = " ".KDLCmdlinePlaceholders::InFileName;
		$cmdStr .= " $segmentDur";
//		$cmdStr .= " ".KDLCmdlinePlaceholders::OutFileName."/segment"; // output MPEG-TS file prefix
//		$cmdStr .= " ".KDLCmdlinePlaceholders::OutFileName."/playlist.m3u8"; // output m3u8 index file
//		$cmdStr .= "zzzz"; // http prefix

		$cmdStr .= " ".KDLCmdlinePlaceholders::OutFileName."//segment"; // output MPEG-TS file prefix
		$cmdStr .= " ".KDLCmdlinePlaceholders::OutFileName."//playlist.m3u8"; // output m3u8 index file
		$cmdStr .= " ---"; // http prefix
		
		return $cmdStr;
	}
	
	/* ---------------------------
	 * CheckConstraints
	 */
	public function CheckConstraints(KDLMediaDataSet $source, KDLFlavor $target, array &$errors=null, array &$warnings=null)
	{
	    return parent::CheckConstraints($source, $target, $errors, $warnings);
	}
}
	