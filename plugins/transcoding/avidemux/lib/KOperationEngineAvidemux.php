<?php
/**
 * @package plugins.avidemux
 * @subpackage lib
 */
class KOperationEngineAvidemux  extends KSingleOutputOperationEngine
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
		parent::operate($operator, $inFilePath, $configFilePath);
//		KalturaLog::info("operator($operator), inFilePath($inFilePath), configFilePath($configFilePath)");
	}
}
