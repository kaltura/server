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

	public function configure(KSchedularTaskConfig $taskConfig, KalturaConvartableJobData $data, KalturaClient $client)
	{
		parent::configure($taskConfig, $data, $client);
		KalturaLog::info("taskConfig-->".print_r($taskConfig,true)."\ndata->".print_r($data,true));
	}
	
	public function operate(kOperator $operator = null, $inFilePath, $configFilePath = null)
	{
		parent::operate($operator, $inFilePath, $configFilePath);
	}
	
}
