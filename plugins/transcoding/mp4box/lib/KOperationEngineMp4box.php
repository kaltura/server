<?php
/**
 * @package plugins.mp4box
 * @subpackage lib
 */
class KOperationEngineMp4box  extends KSingleOutputOperationEngine
{
	public function __construct($cmd, $outFilePath)
	{
		parent::__construct($cmd,$outFilePath);
		KalturaLog::info(": cmd($cmd), outFilePath($outFilePath)");
	}

	/***************************
	 * @param 
	 * @return string
	 */
	protected function getCmdLine() 
	{
		$exeCmd =  parent::getCmdLine();

		if(strstr($exeCmd, KDLOperatorMp4box::ACTION_EMBED_SUBTITLES)!==FALSE) {
			$captionsStr = null;
			{
					// impersonite
				$preImpersoniteId = KBatchBase::$kClient->getConfig()->partnerId;
				
				$captionsStr = $this->buildSubTitleCommandParam($this->data);
					// un-impersonite
				KBatchBase::$kClient->getConfig()->partnerId = $preImpersoniteId;
			}
			if(isset($captionsStr)){
				$exeCmd = str_replace(
						array(KDLOperatorMp4box::ACTION_EMBED_SUBTITLES, KDLOperatorMp4box::SUBTITLE_PLACEHOLDER), 
						array("", $captionsStr), 
						$exeCmd);
			}
			else if(!(isset($this->operator) && isset($this->operator->isOptional) && $this->operator->isOptional>0)){
				$this->message.=".".print_r($this->operator,1);
				throw new KOperationEngineException($this->message);
			}
		}
		else if(strstr($exeCmd, KDLOperatorMp4box::ACTION_HINT)!==FALSE) {
			$exeCmd = str_replace (KDLOperatorMp4box::ACTION_HINT,"", $exeCmd);
		}
		return $exeCmd; 
	}

	/***************************
	 * buildSubTitleCommandParam
	 *
	 * @param KalturaConvartableJobData $data
	 * @return 
	 */
	private function buildSubTitleCommandParam(KalturaConvartableJobData $data)
	{//		$cmdStr.= " -add ".KDLCmdlinePlaceholders::OutFileName.".temp.srt:hdlr=sbtl:lang=$lang:group=0:layer=-1";
	
		try {
			KBatchBase::$kClient->getConfig()->partnerId = $data->flavorParamsOutput->partnerId;
			
			$flrAsst = KBatchBase::$kClient->flavorAsset->get($data->flavorAssetId);
			if(!isset($flrAsst)){
				$this->message = ("Failed to retrieve the flavor asset object (".$data->flavorAssetId.")");
KalturaLog::log("ERROR:".$this->message);
				return null;
			}
			$filter = new KalturaAssetFilter();
			$filter->entryIdEqual = $flrAsst->entryId;
			$captionsList = KBatchBase::$kClient->captionAsset->listAction($filter, null); 
			if(!isset($captionsList) || count($captionsList->objects)==0){
				$this->message = ("No caption assets for entry (".$flrAsst->entryId.")");
KalturaLog::log("ERROR:".$this->message);
				return null;
			}
		}
		catch( Exception $ex ){
			$this->message = ("Exception on captions list retrieval  (".print_r($ex,1).")");
			return null;
		}
		$captionsStr = null;
		$addedSubs=0;
		foreach($captionsList->objects as $captionObj) {
			try{
				$cptUrl = KBatchBase::$kClient->captionAsset->getUrl($captionObj->id, null);
			}
			catch ( Exception $ex ) {
				$cptUrl = null;
				KalturaLog::err("Exception on retrieve caption asset url retrieval (".$captionObj->id."),\nexception:".print_r($ex,1));
			}		
			if(!isset($cptUrl)){
				KalturaLog::err("Failed to retrieve caption asset url (".$captionObj->id.")");
				continue;
			}
			$cptFilePath = self::retrieveCaptionFile($captionObj, $cptUrl, $data->destFileSyncLocalPath);
			if(!isset($cptFilePath)){
				continue;
			}
			/*
			 * group - "An integer that specifies a group or collection of tracks. If this field is 0 there is no information
			 * 	on possible relations to other tracks. If this field is not 0, it should be the same for tracks that contain 
			 * 	alternate data for one another and different for tracks belonging to different such groups. Only one track 
			 * 	within an alternate group should be played or streamed at any one time, and must be distinguishable from other 
			 * 	racks in the group via attributes such as bitrate, codec, language, packet size etc. A group may have only one member. "
			 *	To follow that desc, the group id for all subtitles would be set to 1.
			 *	Apart from the first subs track, all the others would be tagged with 'disabled', otherwise the older iOS devices (pre 5.x)
			 *	does not handle it properly.
			 * layer - "Specifies the front-to-back ordering of video tracks; tracks with lower numbers are closer to the viewer. 
			 *	0 is the normal value, and -1 would be in front of track 0, and so on."
			 *	layer=-1, closest to the viewer
			 */
			$captionsStr.= " -add ".$cptFilePath.":hdlr=sbtl:lang=".$captionObj->languageCode.":group=1:layer=-1";
			if($addedSubs>0) {
				$captionsStr.= ":disabled";
			}
			$addedSubs++;
		}

		if(!isset($captionsStr))
		{
			$this->message = ("Error: missing caption data or files.");
			return null;
		}
		return $captionsStr;
	}

	/***************************
	 * retrieveCaptionFile
	 *
	 * @param $captionObj
	 * @param $destFolder
	 * @return $localCaptionFilePath
	 */
	private static function retrieveCaptionFile($captionObj, $captionUrl, $destFolder)
	{
		KalturaLog::debug("Caption object:\n".print_r($captionObj, 1));
		KalturaLog::debug("Executing curl to retrieve caption asset file from - $captionUrl");
		$curlWrapper = new KCurlWrapper();
		$cptFilePath = $destFolder.".temp.".$captionObj->languageCode.".srt";
		$res = $curlWrapper->exec($captionUrl, $cptFilePath);
		KalturaLog::debug("Curl results: $res");
		if(!$res || $curlWrapper->getError())
		{
			$errDescription = "Error: " . $curlWrapper->getError();
			$curlWrapper->close();
			KalturaLog::err("Failed to curl the caption file url($captionUrl). Error ($errDescription)");
			return null;
		}
		$curlWrapper->close();
		
		if(!file_exists($cptFilePath))
		{
			KalturaLog::err("Error: output file ($cptFilePath) doesn't exist");
			return null;
		}
		KalturaLog::debug("Finished");
		return $cptFilePath;
	}
}
