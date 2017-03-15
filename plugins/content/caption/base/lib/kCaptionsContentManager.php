<?php
/**
 * @package plugins.caption
 * @subpackage lib
 */
abstract class kCaptionsContentManager
{
	public function __construct()
	{
		
	} 
	
	/**
	 * @param string $content
	 * @return array
	 */
	public abstract function parse($content);
	
	/**
	 * @param string $content
	 * @return string
	 */
	public abstract function getContent($content);
	
	/**
	 * @param CaptionType $type
	 * @return kCaptionsContentManager
	 */
	public static function getCoreContentManager($type)
	{
		switch($type)
		{
			case CaptionType::SRT:
				return srtCaptionsContentManager::get(); 
				
			case CaptionType::DFXP:
				return dfxpCaptionsContentManager::get();

			case CaptionType::WEBVTT:
				return webVttCaptionsContentManager::get();
				
			case CaptionType::CAP:
				return capCaptionsContentManager::get();

			default:
				return KalturaPluginManager::loadObject('kCaptionsContentManager', $type);
		}
	}
	
	/**
	 * @param KalturaCaptionType $type
	 * @return kCaptionsContentManager
	 */
	public static function getApiContentManager($type)
	{
		switch($type)
		{
			case KalturaCaptionType::SRT:
				return srtCaptionsContentManager::get(); 
				
			case KalturaCaptionType::DFXP:
				return dfxpCaptionsContentManager::get();

			case CaptionType::WEBVTT:
				return webVttCaptionsContentManager::get();

			default:
				return KalturaPluginManager::loadObject('kCaptionsContentManager', $type);
		}
	}
	
	public static function addParseMultiLanguageCaptionAssetJob($captionAsset, $fileLocation)
	{
		$batchJob = new BatchJob();

		$id = $captionAsset->getId();
		$entryId = $captionAsset->getEntryId();

		$jobData = new kParseMultiLanguageCaptionAssetJobData();
		$jobData->setMultiLanaguageCaptionAssetId($id);
		$jobData->setEntryId($entryId);
		$jobData->setFileLocation($fileLocation);

		$jobType = CaptionPlugin::getBatchJobTypeCoreValue(ParseMultiLanguageCaptionAssetBatchType::PARSE_MULTI_LANGUAGE_CAPTION_ASSET);
		$batchJob->setObjectType(BatchJobObjectType::ASSET);
		$batchJob->setEntryId($entryId);
		$batchJob->setPartnerId($captionAsset->getPartnerId());
		$batchJob->setObjectId($id);

		return kJobsManager::addJob($batchJob, $jobData, $jobType);
	}
}