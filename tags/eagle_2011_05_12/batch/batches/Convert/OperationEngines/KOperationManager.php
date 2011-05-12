<?php
/**
 * @package Scheduler
 * @subpackage Conversion
 */
class KOperationManager
{
	/**
	 * @param int $type
	 * @param KSchedularTaskConfig $taskConfig
	 * @param KalturaConvartableJobData $data
	 * @return KOperationEngine
	 */
	public static function getEngine($type, KSchedularTaskConfig $taskConfig, KalturaConvartableJobData $data)
	{
		$engine = self::createNewEngine($type, $taskConfig, $data);
		if(!$engine)
			return null;
			
		$engine->configure($taskConfig, $data);
		return $engine;
	}
	
	/**
	 * @param int $type
	 * @param KSchedularTaskConfig $taskConfig
	 * @param KalturaConvartableJobData $data
	 * @return KOperationEngine
	 */
	protected static function createNewEngine($type, KSchedularTaskConfig $taskConfig, KalturaConvartableJobData $data)
	{
		// TODO - remove after old version deprecated
		if(!$data->flavorParamsOutput->engineVersion)
		{
			return new KOperationEngineOldVersionWrapper($type, $taskConfig, $data);
		}
		
		switch($type)
		{
			case KalturaConversionEngineType::MENCODER:
				return new KOperationEngineMencoder($taskConfig->params->mencderCmd, $data->destFileSyncLocalPath);
				
			case KalturaConversionEngineType::ON2:
				return new KOperationEngineFlix($taskConfig->params->on2Cmd, $data->destFileSyncLocalPath);
				
			case KalturaConversionEngineType::FFMPEG:
				return new KOperationEngineFfmpeg($taskConfig->params->ffmpegCmd, $data->destFileSyncLocalPath);
				
			case KalturaConversionEngineType::FFMPEG_AUX:
				return new KOperationEngineFfmpegAux($taskConfig->params->ffmpegAuxCmd, $data->destFileSyncLocalPath);
				
			case KalturaConversionEngineType::FFMPEG_VP8:
				return new KOperationEngineFfmpegVp8($taskConfig->params->ffmpegVp8Cmd, $data->destFileSyncLocalPath);
				
			case KalturaConversionEngineType::ENCODING_COM :
				return new KOperationEngineEncodingCom(
					$taskConfig->params->EncodingComUserId, 
					$taskConfig->params->EncodingComUserKey, 
					$taskConfig->params->EncodingComUrl);
		}
		
		if($data instanceof KalturaConvertCollectionJobData)
		{
			$engine = self::getCollectionEngine($type, $taskConfig, $data);
			if($engine)
				return $engine;
		}
		
		$engine = KalturaPluginManager::loadObject('KOperationEngine', $type, array('params' => $taskConfig->params, 'outFilePath' => $data->destFileSyncLocalPath));
		
		return $engine;
	}
	
	protected static function getCollectionEngine($type, KSchedularTaskConfig $taskConfig, KalturaConvertCollectionJobData $data)
	{
		switch($type)
		{
			case KalturaConversionEngineType::EXPRESSION_ENCODER3:
				return new KOperationEngineExpressionEncoder3($taskConfig->params->expEncoderCmd, $data->destFileName, $data->destDirLocalPath);
		}
		
		return  null;
	}
}


