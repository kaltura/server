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
		KalturaLog::debug("creating directory:".$this->outFilePath);
		kFile::fullMkfileDir($this->outFilePath, 0777, true);
		$res = parent::operate($operator, $inFilePath, $configFilePath);
		kFileBase::kRename("$this->outFilePath//playlist.m3u8", "$this->outFilePath//playlist.tmp");
		self::parsePlayList("$this->outFilePath//playlist.tmp","$this->outFilePath//playlist.m3u8");
		return $res;
	}

	private function parsePlayList($fileIn, $fileOut)
	{
		$fdIn = kFileBase::kFOpen($fileIn, "r");
		if($fdIn==false)
			return false;
		$fdOut  = kFileBase::kFOpen($fileOut, "w");
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
