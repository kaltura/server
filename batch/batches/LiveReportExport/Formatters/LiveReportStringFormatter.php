<?php 

class LiveReportStringFormatter extends LiveReportFormatter {
	
	/* (non-PHPdoc)
	 * @see LiveReportFormatter::format()
	 */
	public function format($input) {
		return '"' . $input . '"';
	}
}