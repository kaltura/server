<?php
/*
 * Will be used to signal an exception in a conversion action - application should skip this specific file
 * while writing as much info to the log, and continue with other files. 
 */
class kConversionException extends Exception
{
	
}
?>