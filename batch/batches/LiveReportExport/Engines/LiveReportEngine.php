<?php
/**
 * @package Scheduler
 * @subpackage LiveReportExport
 */
abstract class LiveReportEngine  
{
	protected function checkParams(array $args, array $paramsNames) {
		foreach($paramsNames as $param) {
			if(!array_key_exists($param, $args))
				throw new KOperationEngineException("Missing mandatory argument : " . $param);
		}
	}
	
	/**
	 * Executes the given engine
	 * @param pointer resource $fp - A file system pointer resource.
	 * @param array $args The args to run with
	 */
	abstract public function run($fp, array $args = array());
}

