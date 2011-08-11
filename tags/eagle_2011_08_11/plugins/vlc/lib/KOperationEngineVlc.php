<?php
/**
 * @package plugins.vlc
 * @subpackage lib
 */
class KOperationEngineVlc  extends KSingleOutputOperationEngine
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

}
