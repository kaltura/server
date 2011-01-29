<?php


	/* ===========================
	 * KDLOperatorSegmenter
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
		$cmdStr = " ".KDLCmdlinePlaceholders::InFileName;
		$cmdStr .= " 2";
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
	