<?php
/**
 * @package Scheduler
 * @subpackage Conversion.engines
 */
class KConversionEngineChunkedFfmpeg  extends KConversionEngineFfmpeg
{
	const CHUNKED_FFMPEG = "chunked_ffmpeg";
	
	public function getName()
	{
		return self::CHUNKED_FFMPEG;
	}
	
	public function getType()
	{
		return KalturaConversionEngineType::CHUNKED_FFMPEG;
	}
	
	public function getCmd ()
	{
		return KBatchBase::$taskConfig->params->ffmpegCmd;
	}
	
	/**
	 *
	 */
	protected function execute_conversion_cmdline($command, &$returnVar)
	{
		KalturaLog::log($command);
		if(isset(KBatchBase::$taskConfig->params->chunkedEncodeScriptPath) && strstr($command,KBatchBase::$taskConfig->params->ffmpegCmd)!==false) {
			$output=$this->execute_chunked_encode($command, $returnVar);
		}
		else {
			$output = system($command, $returnVar);
		}
		KalturaLog::log("rv($returnVar),".print_r($output,1));
		return $output;
	}

	/**
	 *
	 */
	protected function execute_chunked_encode($cmdLine, &$returnVar)
	{
		KalturaLog::log("Original cmdLine:$cmdLine");
			/*
			 * Clean up the cmd line - remove 'ffmpeg' and log file redirection instructions
			 * those will be handled by the Chunked flow
			 */
		{
			$cmdLineAdjusted = str_replace(KBatchBase::$taskConfig->params->ffmpegCmd, KDLCmdlinePlaceholders::BinaryName, $cmdLine);
			$cmdValsArr = explode(' ', $cmdLineAdjusted);
			if(($idx=array_search('>>', $cmdValsArr))!==false){
				$cmdValsArr = array_slice ($cmdValsArr,0,$idx);
			}
			if(($idx=array_search(KDLCmdlinePlaceholders::BinaryName, $cmdValsArr))!==false){
				unset($cmdValsArr[$idx]);
			}
			if(($idx=array_search('&&', $cmdValsArr))!==false){
				$cmdValsArr[$idx] = "ANDAND";
			}

			foreach($cmdValsArr as $idx=>$val){
				$val = trim($val);
				if(!isset($val) || $val==' ' || $val==""){
					unset($cmdValsArr[$idx]);
				}
			}
			$cmdLineAdjusted = implode(" ",$cmdValsArr);
			$cmdLineAdjusted = str_replace(KDLCmdlinePlaceholders::BinaryName, KBatchBase::$taskConfig->params->ffmpegCmd, $cmdLineAdjusted);
			KalturaLog::log("Cleaned up cmdLine:$cmdLineAdjusted");
		}
//		$cmdLineAdjusted = KChunkedEncodeSessionManager::quickFixCmdline($cmdLineAdjusted);
//		KalturaLog::log("Fixed cmdLine:$cmdLineAdjusted");
		
			/*
			 * Initialze the Chunked setup object
			 * Use task::params fields -
			 * - chunkedEncodeMaxConcurrent
			 * - chunkedEncodeMinConcurrent
			 * otherwise defaults will be used.
			 */
		{
			$setup = new KChunkedEncodeSetup();
			$setup->ffmpegBin = KBatchBase::$taskConfig->params->ffmpegCmd;
			$setup->cmd = $cmdLineAdjusted;

			if(isset(KBatchBase::$taskConfig->params->chunkedEncodeMaxConcurrent)){
				$setup->concurrent = KBatchBase::$taskConfig->params->chunkedEncodeMaxConcurrent;
			}
			else {
				$setup->concurrent = KBatchBase::$taskConfig->maxInstances;
			}
			if(isset(KBatchBase::$taskConfig->params->chunkedEncodeMinConcurrent)) {
				$setup->concurrentMin = KBatchBase::$taskConfig->params->chunkedEncodeMinConcurrent;
			}
			$chunkedEncodeScriptParams = " -cleanUp 0";//null; //
			$chunkedEncodeScriptParams.= " -concurrent $setup->concurrent";
			$chunkedEncodeScriptParams.= " -concurrentMin $setup->concurrentMin";
		}
		

			/*
			 * Initialize the Chunked Encode Session Manager -
			 * if failed ==> fallback to 'normal' ffmpeg transcoding.
			 * The output file should be 'signed' with 'chunkEncodeToken' to allow 
			 * the KChunkedEncodeSessionManager::concurrencyLogic to take it in account, 
			 * along with 'chunked' conversions. Otherwise the hosting server might get overloaded.
			 */
		$runChunkedEncode = new KChunkedEncodeSessionManager($setup);
		if($runChunkedEncode->Initialize()!=true){
			$output = $runChunkedEncode->ExecuteFallback($cmdLine, $returnVar);
			return $output;
		}
		$sessionFilename = $runChunkedEncode->chunker->getSessionName("session");
		
		$chunkedEncodeScriptCmdLine = "php ".KBatchBase::$taskConfig->params->chunkedEncodeScriptPath;
//		$chunkedEncodeScriptCmdLine.= " -sessionFile $sessionFilename >> ".$this->logFilePath." 2>&1";
		$chunkedEncodeScriptCmdLine.= "$chunkedEncodeScriptParams $cmdLineAdjusted >> ".$this->logFilePath." 2>&1";
		KalturaLog::log("Final cmdLine:$chunkedEncodeScriptCmdLine");

		$output = system($chunkedEncodeScriptCmdLine, $returnVar);
		KalturaLog::log("rv($returnVar),".print_r($output,1));
		return $output;
	}
	
}

