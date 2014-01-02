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
		
		$fsDescArr = array();
		$fsDesc = new KalturaDestFileSyncDescriptor();
		$fsDesc->fileSyncLocalPath = "$newIsmBaseName.ism";
		$fsDesc->fileSyncObjectSubType = 3; //".ism";
		$fsDescArr[] = $fsDesc;
		$fsDesc = new KalturaDestFileSyncDescriptor();
		$fsDesc->fileSyncLocalPath = "$newIsmBaseName.ismc";
		$fsDesc->fileSyncObjectSubType = 4; //".ismc";
		$fsDescArr[] = $fsDesc;
		
		$this->data->extraDestFileSyncs  = $fsDescArr;

		$this->data->destFileSyncLocalPath = null;
		$this->outFilePath = null;
		return $res;
	}
}
