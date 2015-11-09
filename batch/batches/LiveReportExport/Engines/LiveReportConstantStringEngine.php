<?php 

/**
 * Simple engine to print a simple constant string with parameters
 */
class LiveReportConstantStringEngine extends LiveReportEngine
{
	protected $constString;
	protected $params;

	public function __construct($const, $params = array()) {
		$this->constString = $const;
		$this->params = $params;
	}

	public function run($fp, array $args = array()) {
		$params = array();
		foreach($this->params as $param) {
			$this->checkParams($args, array($param));
			$params[] = $args[$param];
		}
		fwrite($fp, vsprintf($this->constString, $params));
	}
}
