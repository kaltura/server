<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_Exception extends Exception 
{
    public function __construct($message, $code) 
    {
    	$this->code = $code;
		parent::__construct($message);
    }
}
