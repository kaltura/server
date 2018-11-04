<?php
/**
 * @package Scheduler
 */
class KalturaBatchException extends KalturaException 
{
	public function __construct($message, $code, $arguments = null)
	{
		parent::__construct($message, $code, $arguments);
	}
}