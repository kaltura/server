<?php
/**
 * @package plugins.mp4box
 * @subpackage lib
 */
class KDLOperatorMp4box extends KDLOperatorBase {

	const ACTION_HINT = "actionHint";
	const ACTION_EMBED_SUBTITLES = "actionEmbedSubtitles";
	const SUBTITLE_PLACEHOLDER = "__subTitlesData__";

    public function __construct($id, $name=null, $sourceBlacklist=null, $targetBlacklist=null) {
    	parent::__construct($id,$name,$sourceBlacklist,$targetBlacklist);
    }

	/* ---------------------------
	 * GenerateCommandLine
	 */
    public function GenerateCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
	{
		
$action="hint";
		$paramsMap = KDLUtils::parseParamStr2Map($extra);
		if (isset($paramsMap)){
			if(array_key_exists('action', $paramsMap)) {
				$action = $paramsMap['action'];
			}
		}

$cmdStr = null;
		switch($action){
		case self::ACTION_EMBED_SUBTITLES:
			$cmdStr = $this->generateEmbedSubtitlesCommandLine($design, $target, $paramsMap);
			break;
		case self::ACTION_HINT:
		default:
			$cmdStr = $this->generateHintCommandLine($design, $target, $paramsMap);
			break;
		}

		return $cmdStr;
	}
	
	/* ---------------------------
	 * generateHintCommandLine
	 */
    private function generateHintCommandLine(KDLFlavor $design, KDLFlavor $target, $paramsMap)
	{
/*
MP4Box -hint C:\Users\Anatol\Downloads\src_3.3gp -out c:\tmp\rtsp_3.3gp
 */
		$cmdStr = " -hint ".KDLCmdlinePlaceholders::InFileName;
		$cmdStr.= " -out ".KDLCmdlinePlaceholders::OutFileName;
		return self::ACTION_HINT.$cmdStr;
	}
	
	/* ---------------------------
	 * generateEmbedSubtitlesCommandLine
	 */
    private function generateEmbedSubtitlesCommandLine(KDLFlavor $design, KDLFlavor $target, $paramsMap)
	{
/*
MP4Box -add InFileName#video -add InFileName#audio -add InCfgFileName:hdlr=sbtl:lang=en:group=2:layer=-1 -new OutFileName
MP4Box -add /web/content/r70v1/entry/data/77/287/1_vlm98u6b_1_ktl7nvmm_1.mp4#video -add /web/content/r70v1/entry/data/77/287/1_vlm98u6b_1_ktl7nvmm_1.mp4#audio -add /web/content/r70v1/entry/data/77/287/1_vlm98u6b_1_y2grw85h_1.srt:hdlr=sbtl:lang=en:group=2:layer=-1 -new /web/content/shared/tmp/1_vlm98u6b_subt.1.mp4
 */
		$cmdStr = " -add ".KDLCmdlinePlaceholders::InFileName."#video";
		$cmdStr.= " -add ".KDLCmdlinePlaceholders::InFileName."#audio";

			// The SUBTITLE_PLACEHOLDER would be interpreted by the Mp4Box Operation Engine with required 
			// captions according to the capion assets that r accossiated with the processed entry
		$cmdStr.= " ".self::SUBTITLE_PLACEHOLDER;
		$cmdStr.= " -new ".KDLCmdlinePlaceholders::OutFileName;
		return self::ACTION_EMBED_SUBTITLES.$cmdStr;
	}
}
	