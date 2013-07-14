<?php
/**
 * @package plugins.expressionEncoder
 * @subpackage lib
 */
class KOperationEngineExpressionEncoder  extends KSingleOutputOperationEngine
{
	/**
	 * @var string
	 */
//	protected $outDir;
//	protected $configData;
	
	/**
	 * @var string
	 */
	
	/**
	 * @param string $outDir
	 */
	public function __construct($cmd, $outFilePath)
	{
		parent::__construct($cmd,$outFilePath);
		KalturaLog::info(": cmd($cmd), outFilePath($outFilePath)");
	}

	protected function getCmdLine()
	{
		$outDir = realpath(dirname($this->outFilePath));
		$this->outDir = $outDir;
		$this->configFilePath = $this->outFilePath.".xml";
				// The EE3 preset xml allows 'clean' outfile names only (no full path)  
		$this->outFilePath = basename($this->outFilePath);
		$this->inFilePath = realpath(dirname($this->inFilePath))."\\".basename($this->inFilePath);
		KalturaLog::info("outFilePath(dirname($this->outFilePath)),auxPath($outDir)");
			// Add slashes to solve JSON serialization issue
		$xmlStr = str_replace ('\"' , '"' ,  $this->operator->config);
		file_put_contents($this->configFilePath, $xmlStr);

		$exeCmd =  parent::getCmdLine();
		KalturaLog::info(print_r($this,true));
		return $exeCmd;
	}

	public function operate(kOperator $operator = null, $inFilePath, $configFilePath = null)
	{	
			/*
			 * The EE3 tends to lock the source file for a short period. Since the same source file might be used 
			 * by number of transcoding process, occasionally, one of the transcoding processes gets denied on an 
			 * attempt to access the source file. 
			 * The code catches the Exceptions thrown by the KOperrationEngine. 
			 * On exception caused by the above state - 3 retries are performed w/10sec delay in between.
			 * All other exceptions are 're-thrown'.
			 *
			 * Note: For PHP 5.3 the exception re-throw should be enhanced with 'e->getPrevious()'
			 */
		$ex=null;
		for($iEx=0; $iEx<5; $iEx++) {
			try {
					/*
					 * Successfull execution attempt
					 */
				return parent::operate($operator, $inFilePath, $configFilePath);
			}
			catch(Exception $e) {
				$ex=$e;

				$logStr = $this->getLogData();
					/* 
					 * 'Progress:100' in the log means that the conversion was completed successfully, 
					 * and got hang on exit. An external script will hopefully kill the EE task.
					 * If the log contains the 'Progress:100' string, wait 10 sec and retry (up to 3 retries). 
					 * Otherwise - halt retries and re-through the same exception
					 */ 
				if(strstr($logStr,"Progress:100")==false)
					break;
				$secsToSleep=rand(10,30);
				KalturaLog::info("EE got hang up. Waiting $secsToSleep sec for next attempt. Attempt:$iEx");
				sleep($secsToSleep);
			}
		}
		throw new KOperationEngineException($ex->getMessage(), $ex->getCode());
	}
	
}
