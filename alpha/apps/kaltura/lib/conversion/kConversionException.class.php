<?php
/**
 * Will be used to signal an exception in a conversion action - application should skip this specific file
 * while writing as much info to the log, and continue with other files. 
 * 
 * @package Core
 * @subpackage Conversion
 * @deprecated
 */
class kConversionException extends Exception
{
	
}
?>