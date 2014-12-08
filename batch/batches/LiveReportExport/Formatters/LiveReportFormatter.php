<?php 

/**
 *	This class is base class for reports formatters 
 */
abstract class LiveReportFormatter {
	
	/**
	 * Gets an input and formats it by the matching formatter
	 * @param unknown_type $input The input we'd like to format
	 */
	abstract public function format($input);
	
}