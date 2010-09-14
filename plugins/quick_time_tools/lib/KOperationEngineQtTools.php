<?php
/**
 * 
 * @package Scheduler
 * @subpackage Conversion
 *
 */
class KOperationEngineQtTools  extends KSingleOutputOperationEngine
{
	public function operate(kOperator $operator = null, $inFilePath, $logFilePath, $configFilePath = null)
	{
		$qtInFilePath = "$inFilePath.stb";
		if(rename($inFilePath, $qtInFilePath))
			$inFilePath = $qtInFilePath;
		
		parent::operate($operator, $inFilePath, $logFilePath, $configFilePath);
	}	
}
