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
	 * execute_conversion_cmdline
	 *	Chunked Encoding can executed both in standalone and memcache managed modes.
	 *	'executionMode' config field used to differntiate between the modes, 
	 *	allowed values - 'standalone'/'memcache'
	 */
	protected function execute_conversion_cmdline($command, &$returnVar)
	{
		KalturaLog::log($command);
		if(!isset(KBatchBase::$taskConfig->params->executionMode)){
			$returnVar = -1;
			$errMsg = "ERROR: Missing executionMode value in the batch/worker.ini";
			KalturaLog::log($errMsg);
			return ($errMsg);
		}
		
		$executionMode = KBatchBase::$taskConfig->params->executionMode;
		if($executionMode=="standalone") {
			$output=$this->execute_chunked_encode_standalone($command, $returnVar);
		}
		else if($executionMode=="memcache"){
			$output=$this->execute_chunked_encode_memcache($command, $returnVar);
		}
		else {
			$returnVar = -1;
			$errMsg = "ERROR: Invalid executionMode value ($executionMode) in the batch/worker.ini";
			KalturaLog::log($errMsg);
			return ($errMsg);
		}
		KalturaLog::log("rv($returnVar),".print_r($output,1));
		return $output;
	}

	/**
	 * execute_chunked_encode_memcache
	 * 	Execute memcache based (distributed) Chunked Encode session
	 *	Uses following configuration fields - 
	 *	- chunkedEncodeMemcacheHost - memcache host URL (mandatory)
	 *	- chunkedEncodeMemcachePort - memcache host port (mandatory)
	 *	- chunkedEncodeMemcacheToken - token to differentiate between general/global Kaltura jobs and per customer dedicated servers (optional, default:null)
	 *	- chunkedEncodeMaxConcurrent - maximum concurrently executed chunks jobs, more or less servers core number (optional, default:5)
	 */
	protected function execute_chunked_encode_memcache($cmdLine, &$returnVar)
	{
		KalturaLog::log("Original cmdLine:$cmdLine");
		
				/*
				 * 'chunkedEncodeMemcacheHost' and 'chunkedEncodeMemcachePort'
				 * are mandatory
				 */
		if(!(isset(KBatchBase::$taskConfig->params->chunkedEncodeMemcacheHost) 
		&& isset(KBatchBase::$taskConfig->params->chunkedEncodeMemcachePort))){
			$returnVar = -1;
			$errMsg = "ERROR: Missing memcache host/port in the batch/worker.ini";
			KalturaLog::log($errMsg);
			return ($errMsg);
		}
			/*
			 * Clean up the cmd line - remove 'ffmpeg' and log file redirection instructions
			 * those will be handled by the Chunked flow
			 */
		$cmdLineAdjusted = $this->adjust_cmdline($cmdLine);
		
		{
			$host = KBatchBase::$taskConfig->params->chunkedEncodeMemcacheHost;
			$port = KBatchBase::$taskConfig->params->chunkedEncodeMemcachePort;
			
			if(isset(KBatchBase::$taskConfig->params->chunkedEncodeMemcacheToken)){
				$token = KBatchBase::$taskConfig->params->chunkedEncodeMemcacheToken;
			}
			else $token = null;
			
			if(isset(KBatchBase::$taskConfig->params->chunkedEncodeMaxConcurrent)){
				$concurrent = KBatchBase::$taskConfig->params->chunkedEncodeMaxConcurrent;
			}
			else 
				$concurrent = 5;

			$sessionName = null;
		}
		{
			$cmdLine = 'php -r "';
			$cmdLine.= 'require_once \'/opt/kaltura/app/batch/bootstrap.php\';';

			$cmdLine.= '\$rv=KChunkedEncodeMemcacheWrap::ExecuteSession(';
			$cmdLine.= '\''.($host).'\',';
			$cmdLine.= '\''.($port).'\',';
			$cmdLine.= '\''.($token).'\',';
			$cmdLine.= '\''.($concurrent).'\',';
			$cmdLine.= '\''.($sessionName).'\',';
			$cmdLine.= '\''.$cmdLineAdjusted.'\');';
			$cmdLine.= 'if(\$rv==false) exit(1);';
			$cmdLine.= '"';
		}
		$cmdLine.= " >> ".$this->logFilePath." 2>&1";
		KalturaLog::log("Final cmdLine:$cmdLine");

		$output = system($cmdLine, $returnVar);
		KalturaLog::log("rv($returnVar),".print_r($output,1));
		return $output;
	}
	
	/**
	 * execute_chunked_encode_standalone
	 * 	Execute standalone (one server) Chunked Encode session
	 *	Uses following configuration fields - 
	 *	- chunkedEncodeMaxConcurrent - maximum concurrently executed chunks jobs, more or less servers core number (optional, default:5)
	 */
	protected function execute_chunked_encode_standalone($cmdLine, &$returnVar)
	{
		KalturaLog::log("Original cmdLine:$cmdLine");
			/*
			 * Clean up the cmd line - remove 'ffmpeg' and log file redirection instructions
			 * those will be handled by the Chunked flow
			 */
		$cmdLineAdjusted = $this->adjust_cmdline($cmdLine);

		{
			if(isset(KBatchBase::$taskConfig->params->chunkedEncodeMaxConcurrent)){
				$concurrent = KBatchBase::$taskConfig->params->chunkedEncodeMaxConcurrent;
			}
			else
				$concurrent = 5;

			if(isset(KBatchBase::$taskConfig->params->chunkedEncodeMinConcurrent)) {
				$concurrentMin = KBatchBase::$taskConfig->params->chunkedEncodeMinConcurrent;
			}
			else
				$concurrentMin = 1;
			$sessionName = null;
			
			$cmdLine = 'php -r "';
			$cmdLine.= 'require_once \'/opt/kaltura/app/batch/bootstrap.php\';';
			
			$cmdLine.= '\$rv=KChunkedEncodeSessionManagerStandalone::ExecuteSession(';
			$cmdLine.= '\''.($concurrent).'\',';
			$cmdLine.= '\''.($concurrentMin).'\',';
			$cmdLine.= '\''.($sessionName).'\',';
			$cmdLine.= '\''.$cmdLineAdjusted.'\');';
                        $cmdLine.= 'if(\$rv==false) exit(1);';
                        $cmdLine.= '"';
		}
		$cmdLine.= " >> ".$this->logFilePath." 2>&1";
		KalturaLog::log("Final cmdLine:$cmdLine");

		$output = system($cmdLine, $returnVar);
		KalturaLog::log("rv($returnVar),".print_r($output,1));
		return $output;
	}

	/**
	 *
	 */
	private function adjust_cmdline($cmdLine)
	{
		KalturaLog::log("Original cmdLine:$cmdLine");
			/*
			 * Clean up the cmd line - remove 'ffmpeg' and log file redirection instructions
			 * those will be handled by the Chunked flow
			 */
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
		$cmdLineAdjusted = str_replace('\'', '\\\'',$cmdLineAdjusted);
		KalturaLog::log("Cleaned up cmdLine:$cmdLineAdjusted");
		
		return $cmdLineAdjusted;
	}
}
