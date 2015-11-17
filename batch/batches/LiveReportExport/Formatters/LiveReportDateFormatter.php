<?php 

class LiveReportDateFormatter extends LiveReportFormatter {
	
	protected $format;
	protected $timeZoneOffset;
	
	public function __construct($format, $timeZoneOffset = 0) {
		$this->format = $format;
		$this->timeZoneOffset = $timeZoneOffset;
	}
	
	/* (non-PHPdoc)
	 * @see LiveReportFormatter::format()
	 */
	public function format($input) {
		return date($this->format, $input + $this->timeZoneOffset);
	}
}
