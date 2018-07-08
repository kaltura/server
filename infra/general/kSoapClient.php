<?php
class kSoapClient extends SoapClient 
{

	public function __construct($wsdl,$options=array())
	{	
		$this->beforeCall();
		parent::__construct($wsdl, $options);
		$this->afterCall();
	}
	public function __soapCall ($function_name, $arguments, $options = NULL, $input_headers = NULL, &$output_headers = NULL)
	{
		$this->beforeCall();
		$ret = parent::__soapCall($function_name, $arguments);
		$this->afterCall();
		return $ret;
	}
	private function beforeCall()
	{
		stream_wrapper_restore('http');
		stream_wrapper_restore('https');
		libxml_disable_entity_loader(false);
	}
	private function afterCall()
	{
		libxml_disable_entity_loader(true);
		stream_wrapper_unregister('https');
		stream_wrapper_unregister('http');
	}

}
