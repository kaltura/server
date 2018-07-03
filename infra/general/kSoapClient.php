<?php
class kSoapClient extends SoapClient 
{

	public function __construct($wsdl,$options=array())
	{	
		stream_wrapper_restore('http');
		stream_wrapper_restore('https');
		libxml_disable_entity_loader(false);

		parent::__construct($wsdl, $options);

		libxml_disable_entity_loader(true);
		stream_wrapper_unregister('https');
		stream_wrapper_unregister('http');
	}
}
