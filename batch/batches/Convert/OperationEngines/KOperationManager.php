<?php
/**
 * @package Scheduler
 * @subpackage Conversion
 */
class KOperationManager
{
	/**
	 * @param int $type
	 * @param KalturaConvartableJobData $data
	 * @param KalturaBatchJob $job
	 * @return KOperationEngine
	 */
	public static function getEngine($type, KalturaConvartableJobData $data, KalturaBatchJob $job)
	{
		$engine = self::createNewEngine($type, $data);
		if(!$engine)
			return null;
			
		$engine->configure($data, $job);
		return $engine;
	}
	
	/**
	 * @param int $type
	 * @param KalturaConvartableJobData $data
	 * @return KOperationEngine
	 */
	protected static function createNewEngine($type, KalturaConvartableJobData $data)
	{
		// TODO - remove after old version deprecated
		/*
		 * The 'flavorParamsOutput' is not set only for SL/ISM collections - that is definently old engine' flow
		 */		
		if(!isset($data->flavorParamsOutput) || !$data->flavorParamsOutput->engineVersion)
		{
			return new KOperationEngineOldVersionWrapper($type, $data);
		}
		
		switch($type)
		{ 
			case KalturaConversionEngineType::MENCODER:
				return new KOperationEngineMencoder(KBatchBase::$taskConfig->params->mencderCmd, $data->destFileSyncLocalPath);
				
			case KalturaConversionEngineType::ON2:
				return new KOperationEngineFlix(KBatchBase::$taskConfig->params->on2Cmd, $data->destFileSyncLocalPath);
				
			case KalturaConversionEngineType::FFMPEG:
				return new KOperationEngineFfmpeg(KBatchBase::$taskConfig->params->ffmpegCmd, $data->destFileSyncLocalPath);
				
			case KalturaConversionEngineType::FFMPEG_AUX:
				return new KOperationEngineFfmpegAux(KBatchBase::$taskConfig->params->ffmpegAuxCmd, $data->destFileSyncLocalPath);
				
			case KalturaConversionEngineType::FFMPEG_VP8:
				return new KOperationEngineFfmpegVp8(KBatchBase::$taskConfig->params->ffmpegVp8Cmd, $data->destFileSyncLocalPath);
				
			case KalturaConversionEngineType::ENCODING_COM :
				return new KOperationEngineEncodingCom(
					KBatchBase::$taskConfig->params->EncodingComUserId, 
					KBatchBase::$taskConfig->params->EncodingComUserKey, 
					KBatchBase::$taskConfig->params->EncodingComUrl);
		}
		
		if($data instanceof KalturaConvertCollectionJobData)
		{
			$engine = self::getCollectionEngine($type, $data);
			if($engine)
				return $engine;
		}
		$engine = KalturaPluginManager::loadObject('KOperationEngine', $type, array('params' => KBatchBase::$taskConfig->params, 'outFilePath' => $data->destFileSyncLocalPath));
		
		return $engine;
	}
	
	protected static function getCollectionEngine($type, KalturaConvertCollectionJobData $data)
	{
		switch($type)
		{
			case KalturaConversionEngineType::EXPRESSION_ENCODER3:
				return new KOperationEngineExpressionEncoder3(KBatchBase::$taskConfig->params->expEncoderCmd, $data->destFileName, $data->destDirLocalPath);
		}
		
		return  null;
	}
}


