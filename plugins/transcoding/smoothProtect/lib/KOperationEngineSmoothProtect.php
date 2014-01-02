<?php
/**
 * @package plugins.smoothProtect
 * @subpackage lib
 */
class KOperationEngineSmoothProtect  extends KSingleOutputOperationEngine
{

	public function __construct($cmd, $outFilePath)
	{
		parent::__construct($cmd,$outFilePath);
		KalturaLog::info(": cmd($cmd), outFilePath($outFilePath)");
	}

	/* ---------------------------
	 * getCmdLine
	 */
	protected function getCmdLine()
	{
		$exeCmd =  parent::getCmdLine();
// SmoothProtect.exe -input AAA.ism -output OUT_FOLDER -keyId KEY_ID -keySeed KEY_SEED -laUrl http://playready.directtaps.net/pr/svc/rightsmanager.asmx?
//		SmoothProtectPlugin::PARAMS_STUB
		$pathInfo = pathinfo($this->inFilePath);
		$ismFilePath = $pathInfo['dirname']."/".$pathInfo['filename'].".ism";
		$outFilePath = dirname($this->outFilePath);
		$paramsStr = " -input $ismFilePath -output $outFilePath ".$this->getLicenseParamsStr();
		$paramsStr = str_replace("/", "\\", $paramsStr);
		$exeCmd = str_replace(SmoothProtectPlugin::PARAMS_STUB, $paramsStr, $exeCmd);
		KalturaLog::info(print_r($this,true));
		return $exeCmd;
	}

	/* ---------------------------
	 * operate
	 */
	public function operate(kOperator $operator = null, $inFilePath, $configFilePath = null)
	{
		$res = parent::operate($operator, $inFilePath, $configFilePath);
		if($res==false) {
			return false;
		}

		$inPathInfo=pathinfo($inFilePath);
		$inFileName = $inPathInfo['filename'];

		$outPathInfo=pathinfo($this->outFilePath);
		$outFileName = $outPathInfo['filename'];
		$outBaseName = $outPathInfo['basename'];
		$outFolderName = $outPathInfo['dirname'];
		
		$auxOutName = "$outFolderName//$inFileName";
		
		/*
		 * Update ISM manifest file with correct output file names
		 */
		$ismStr = file_get_contents($auxOutName.".ism");
		KalturaLog::info("Before file name update:\n$ismStr");
		$ismXml = new SimpleXMLElement($ismStr);
		$ismXml->head->meta['content'] = $outFileName.".ismc";
		if(isset($ismXml->body->switch->video)) $ismXml->body->switch->video['src'] = $outBaseName;
		if(isset($ismXml->body->switch->audio)) $ismXml->body->switch->audio['src'] = $outBaseName;
		$ismStr = $ismXml->asXML();
		KalturaLog::info("After file name update:\n$ismStr");
		file_put_contents($auxOutName.".ism", $ismStr);
		
		/*
		 * Update the ISM/ISMC/ISMV file names to correct output file names
		 */
		rename($auxOutName.".ism",  "$outFolderName//$outFileName.ism");
		rename($auxOutName.".ismc", "$outFolderName//$outFileName.ismc");
		rename($auxOutName.".ismv", "$outFolderName//$outBaseName");

		/*
		 * Notify batch job flow to bind the ISM/ISMC files to the asset
		 */
		$fsDescArr = array();
		$fsDesc = new KalturaDestFileSyncDescriptor();
		$fsDesc->fileSyncLocalPath = "$outFolderName//$outFileName.ism";
		$fsDesc->fileSyncObjectSubType = 3; //".ism";
		$fsDescArr[] = $fsDesc;
		$fsDesc = new KalturaDestFileSyncDescriptor();
		$fsDesc->fileSyncLocalPath = "$outFolderName//$outFileName.ismc";
		$fsDesc->fileSyncObjectSubType = 4; //".ismc";
		$fsDescArr[] = $fsDesc;
		$this->data->extraDestFileSyncs  = $fsDescArr;
		return $res;
	}

	/* ---------------------------
	 * getLicenseParamsStr
	*/
	private function getLicenseParamsStr()
	{
		$keyId = "b6e16839-eebd-4ff6-ab76-8d482d8d2b6a";
		$keySeed = "XVBovsmzhP9gRIZxWfFta3VVRPzVEWmJsazEJ46I";
		$laSrv = "http://playready.directtaps.net/pr/svc/rightsmanager.asmx?";
		$paramsStr = " -keyId $keyId -keySeed $keySeed -laUrl $laSrv";
		return $paramsStr;
	}
}
