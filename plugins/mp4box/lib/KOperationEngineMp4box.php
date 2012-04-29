<?php
/**
 * @package plugins.mp4box
 * @subpackage lib
 */
class KOperationEngineMp4box  extends KSingleOutputOperationEngine
{

	/**
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

	protected function getCmdLine() 
	{
		$exeCmd =  parent::getCmdLine();
		
		if(strstr($exeCmd, KDLOperatorMp4box::ACTION_EMBED_SUBTITLES)!==FALSE) {
			$exeCmd = str_replace (KDLOperatorMp4box::ACTION_EMBED_SUBTITLES,"", $exeCmd);
			$this->retrieveCaptionFile($this->data, $this->client);
		}
		else if(strstr($exeCmd, KDLOperatorMp4box::ACTION_HINT)!==FALSE) {
			$exeCmd = str_replace (KDLOperatorMp4box::ACTION_HINT,"", $exeCmd);
		}
//		KalturaLog::info(print_r($this,true));
		return $exeCmd; 
	}

	public function configure(KSchedularTaskConfig $taskConfig, KalturaConvartableJobData $data, KalturaClient $client)
	{
		parent::configure($taskConfig, $data, $client);
		$this->data = $data;
		$this->client = $client;

//		KalturaLog::info("client-->".print_r($client,true));
//		KalturaLog::info("taskConfig-->".print_r($taskConfig,true)."\ndata->".print_r($data,true));
	}

	private static function retrieveCaptionFile(KalturaConvartableJobData $data, KalturaClient $client)
	{
			// inpersonize
		$client->getConfig()->partnerId = $data->flavorParamsOutput->partnerId;
//		$results = $client->flavorAsset->get($data->flavorAssetId);
		$flrAsst = $client->flavorAsset->get($data->flavorAssetId);
		if(!isset($flrAsst)){
			throw new KOperationEngineException("Failed to retrieve the flavor asset object (".$data->flavorAssetId.")");
		}
		$filter = new KalturaAssetFilter();
		$filter->entryIdEqual = $flrAsst->entryId;
		$cptList = $client->captionAsset->listAction($filter, null); 
		if(!isset($cptList) || count($cptList->objects)==0){
			throw new KOperationEngineException("No caption assets for entry (".$flrAsst->entryId.")");
		}
		
		$cptUrl = $client->captionAsset->getUrl($cptList->objects[0]->id, null);
		if(!isset($cptUrl)){
			throw new KOperationEngineException("Failed to retrieve caption asset url (".$cptList->objects[0]->id.")");
		}
//		throw new KOperationEngineException("!!!caption url (".print_r($cptUrl,1).")");
		{
			KalturaLog::debug("Executing curl to retrieve caption asset file from - $cptUrl");
			$curlWrapper = new KCurlWrapper($cptUrl);
			$cptFilePath = $data->destFileSyncLocalPath.".temp.srt";
			$res = $curlWrapper->exec($cptFilePath);
			KalturaLog::debug("Curl results: $res");
		
			if(!$res || $curlWrapper->getError())
			{
				$errDescription = "Error: " . $curlWrapper->getError();
				$curlWrapper->close();
				throw new KOperationEngineException("Failed to curl the caption file url(".$cptUrl."). Error ($errDescription)");
			}
			$curlWrapper->close();
			
			if(!file_exists($cptFilePath))
			{
				throw new KOperationEngineException("Error: output file ($cptFilePath) doesn't exist");
			}
			KalturaLog::debug("Finished");
		}
	}
}
