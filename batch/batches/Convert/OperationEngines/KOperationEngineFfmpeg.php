<?php
/**
 * @package Scheduler
 * @subpackage Conversion
 */
class KOperationEngineFfmpeg  extends KSingleOutputOperationEngine
{
	protected function getCmdLine()
	{
		$cmdLine=parent::getCmdLine();
		$cmdLine=KConversionEngineFfmpeg::expandForcedKeyframesParams($cmdLine);
		return $cmdLine;
	}
}
