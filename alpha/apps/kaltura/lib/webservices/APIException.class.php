<?php
class APIException extends kException
{
	public $api_code = null;
	public $extra_data = null;
	
	public function __construct()
	{
		$args = func_get_args();
		call_user_func_array(array($this, 'parent::__construct'), $args);
		
		$this->api_code = $this->kaltura_code;
	}
}
?>