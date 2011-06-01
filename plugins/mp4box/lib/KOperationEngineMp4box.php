<?php
/**
 * @package plugins.mp4box
 * @subpackage lib
 */
class KOperationEngineMp4box  extends KSingleOutputOperationEngine
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
