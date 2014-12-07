<?php 

class LiveReportDateFormatter extends LiveReportFormatter {
	
	protected $timeZoneOffset;
	
	public function LiveReportDateFormatter($timeZoneOffset = 0) {
		$this->timeZoneOffset = $timeZoneOffset;
	}
	
	/* (non-PHPdoc)
	 * @see LiveReportFormatter::format()
	 */
	public function format($input) {
		return date(LiveReportConstants::DATE_FORMAT, $input + $this->timeZoneOffset);
	}
}