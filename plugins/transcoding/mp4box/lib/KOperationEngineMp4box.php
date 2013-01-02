<?php
/**
 * @package plugins.mp4box
 * @subpackage lib
 */
class KOperationEngineMp4box  extends KSingleOutputOperationEngine
{

	/***************************
	* @var KalturaConvertJobData
	*/
	protected $data = null;

	/**
	* @var KalturaClient
	*/
	protected $client = null;

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
			$captionsStr = $this->buildSubTitleCommandParam($this->data, $this->client);
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
	 * @param KSchedularTaskConfig $taskConfig
	 * @param KalturaConvartableJobData $data
	 * @param KalturaClient $client
	 * @return 
	 */
	public function configure(KSchedularTaskConfig $taskConfig, KalturaConvartableJobData $data, KalturaClient $client)
	{
		parent::configure($taskConfig, $data, $client);
		$this->data = $data;
		$this->client = $client;

	}

	/***************************
	 * buildSubTitleCommandParam
	 *
	 * @param KalturaConvartableJobData $data
	 * @param KalturaClient $client
	 * @return 
	 */
	private function buildSubTitleCommandParam(KalturaConvartableJobData $data, KalturaClient $client)
	{//		$cmdStr.= " -add ".KDLCmdlinePlaceholders::OutFileName.".temp.srt:hdlr=sbtl:lang=$lang:group=0:layer=-1";
	
			// impersonite
		$preImpersoniteId = $client->getConfig()->partnerId;
		$client->getConfig()->partnerId = $data->flavorParamsOutput->partnerId;
		
		$flrAsst = $client->flavorAsset->get($data->flavorAssetId);
		if(!isset($flrAsst)){
			$this->message = ("Failed to retrieve the flavor asset object (".$data->flavorAssetId.")");
			return null;
		}
		$filter = new KalturaAssetFilter();
		$filter->entryIdEqual = $flrAsst->entryId;
		$captionsList = $client->captionAsset->listAction($filter, null); 
		if(!isset($captionsList) || count($captionsList->objects)==0){
			$this->message = ("No caption assets for entry (".$flrAsst->entryId.")");
			return null;
		}

		$captionsStr = null;
		$addedSubs=0;
		foreach($captionsList->objects as $captionObj) {
			try{
				$cptUrl = $client->captionAsset->getUrl($captionObj->id, null);
			}
			catch ( Exception $ex ) {
				$cptUrl = null;
				KalturaLog::err("Exception on etrieve caption asset url retrieval (".$captionObj->id."),\nexception:".print_r($ex,1));
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
			// un-impersonite
		$client->getConfig()->partnerId = $preImpersoniteId;
		
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
		$curlWrapper = new KCurlWrapper($captionUrl);
		$cptFilePath = $destFolder.".temp.".$captionObj->languageCode.".srt";
		$res = $curlWrapper->exec($cptFilePath);
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
