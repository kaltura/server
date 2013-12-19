<?php
/**
 * @package plugins.ismIndex
 * @subpackage lib
 */
class KOperationEngineIsmIndex  extends KSingleOutputOperationEngine
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
		$res = parent::operate($operator, $inFilePath, $configFilePath);
		if($res==false) {
			return false;
		}

		$rvPath=pathinfo($inFilePath);
		$fileName = $rvPath['filename'];
		$baseName = $rvPath['basename'];
		$ismStr = file_get_contents("$this->outFilePath.ism");
		$ismStr = str_replace(array("$this->outFilePath.ismc", $inFilePath), array("$fileName.ismc",$baseName), $ismStr);
		file_put_contents("$this->outFilePath.ism", $ismStr);
		
		$rv=mkdir($this->outFilePath."_tmpism",0777);
		if($rv==false)
			return false;
		$newIsmBaseName = $this->outFilePath."_tmpism/$fileName";
		rename("$this->outFilePath.ism", "$newIsmBaseName.ism");
		rename("$this->outFilePath.ismc", "$newIsmBaseName.ismc");
		$faDescArr = array();
		$faDesc = KalturaFileAssetDescriptor();
		$faDesc->name = $faDesc->$fileSyncLocalPath = "$newIsmBaseName.ism";
		$faDesc->fileExt = ".ism";
		$faDescArr[] = $faDesc;
		$faDesc = KalturaFileAssetDescriptor();
		$faDesc->name = $faDesc->$fileSyncLocalPath = "$newIsmBaseName.ismc";
		$faDesc->fileExt = ".ismc";
		$faDescArr[] = $faDesc;
		
		$this->data->destFileAssets = $faDescArr;
		return $res;
	}
}
