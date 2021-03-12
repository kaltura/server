<?php
/**
 * @package Scheduler
 * @subpackage Conversion.engines
 */
class KConversionEngineFfmpegAux  extends KJobConversionEngine
{
	const FFMPEG_AUX = "ffmpeg_aux";
	
	public function getName()
	{
		return self::FFMPEG_AUX;
	}
	
	public function getType()
	{
		return KalturaConversionEngineType::FFMPEG_AUX;
	}
	
	public function getCmd ()
	{
		return KBatchBase::$taskConfig->params->ffmpegAuxCmd;
	}
	
	protected function getCmdLine ($cmd_line , $add_log )
	{
		// I have commented out the audio parameters so we don't decrease the quality - it stays as-is
		$binName = $this->getCmd();
		$inputFilePath = kFile::buildDirectUrl($this->inFilePath);
		kBatchUtils::addReconnectParams("http", $inputFilePath,$binName);
		
		$exec_cmd = $binName . " " .
			str_replace (
				array(KDLCmdlinePlaceholders::InFileName, KDLCmdlinePlaceholders::OutFileName, KDLCmdlinePlaceholders::ConfigFileName, KDLCmdlinePlaceholders::BinaryName),
				array('"' . $inputFilePath . '"', $this->outFilePath, $this->configFilePath, $binName),
				$cmd_line);
		
		if ( $add_log )
		{
			// redirect both the STDOUT & STDERR to the log
			$exec_cmd .= " >> \"{$this->logFilePath}\" 2>&1";
		}
		
		return $exec_cmd;
	}
}
