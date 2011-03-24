<?php
/**
 * @package plugins.segmenter
 * @subpackage lib
 */
class KOperationEngineSegmenter  extends KSingleOutputOperationEngine
{

	public function __construct($cmd, $outFilePath)
	{
		parent::__construct($cmd,$outFilePath);
		KalturaLog::info(": cmd($cmd), outFilePath($outFilePath)");
	}

	protected function getCmdLine()
	{
		$exeCmd =  parent::getCmdLine();
		KalturaLog::info(print_r($this,true));
		return $exeCmd;
	}

	public function operate(kOperator $operator = null, $inFilePath, $configFilePath = null)
	{
//$this->outFilePath = "k:".$this->outFilePath;
		KalturaLog::debug("creating directory:".$this->outFilePath);
		if(mkdir($this->outFilePath, 0777, true))
			KalturaLog::debug("SUCCESS");
		else 
			KalturaLog::debug("FAILURE");
		parent::operate($operator, $inFilePath, $configFilePath);
		rename("$this->outFilePath//playlist.m3u8", "$this->outFilePath//playlist.tmp");
		self::parsePlayList("$this->outFilePath//playlist.tmp","$this->outFilePath//playlist.m3u8");
//		rename("out_dummy.m3u8", "$this->outFilePath//out_dummy.m3u8");
//		KalturaLog::info("operator($operator), inFilePath($inFilePath), configFilePath($configFilePath)");
	}

	public function configure(KSchedularTaskConfig $taskConfig, KalturaConvartableJobData $data)
	{
		parent::configure($taskConfig, $data);
		KalturaLog::info("taskConfig-->".print_r($taskConfig,true)."\ndata->".print_r($data,true));
	}

	private function parsePlayList($fileIn, $fileOut)
	{
		$fdIn = fopen($fileIn, 'r');
		if($fdIn==false)
			return false;
		$fdOut = fopen($fileOut, 'w');
		if($fdOut==false)
			return false;
		$strIn=null;
		while ($strIn=fgets($fdIn)){
			if(strstr($strIn,"---")){
				$i=strrpos($strIn,"/");
				$strIn = substr($strIn,$i+1);
			}
			fputs($fdOut,$strIn);
			echo $strIn;
		}
		fclose($fdOut);
		fclose($fdIn);
		return true;
	}
}
