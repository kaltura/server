<?php
/**
 * Expose the schema definitions for syndication MRSS, bulk upload XML and other schema types. 
 * 
 * @service schema
 * @package api
 * @subpackage services
 */
class SchemaService extends KalturaBaseService 
{
	const CORE_SCHEMA_NAME = 'core';
	
	/* (non-PHPdoc)
	 * @see KalturaBaseService::partnerRequired()
	 */
	protected function partnerRequired($actionName)
	{
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaBaseService::isPermitted()
	 */
	protected function isPermitted(&$allowPrivatePartnerData)
	{
		return true;
	}
	
	/**
	 * Serves the requested XSD according to the type and name. 
	 * 
	 * @action serve
	 * @param KalturaSchemaType $type  
	 * @param string $name
	 * @return file 
	 */
	function serveAction($type, $name = null)
	{
		$ns = 'http://' . kConf::get('www_host') . "/$type";
		if($name)
			$ns .= "/$name";
						
		$xsdElement = null;		
		$xsd = '<xs:schema targetNamespace="' . $ns . '" xmlns:xs="http://www.w3.org/2001/XMLSchema">';
			
		if(!$name)
		{
			$xsd .= '</xs:schema>';
			$xsdElement = new SimpleXMLElement($xsd);
		
			$redefine = $xsd->addChild('redefine');
			$redefine->addAttribute('schemaLocation', 'http://' . kConf::get('cdn_host') . "/api_v3/service/schema/action/serve/type/$type/name/" . self::CORE_SCHEMA_NAME);
		
			$schemaContributors = KalturaPluginManager::getPluginInstances('IKalturaSchemaContributor');
			foreach($schemaContributors as $key => $schemaContributor)
				$schemaContributor->contributeToSchema($type, $xsd);
		}
		elseif ($name == self::CORE_SCHEMA_NAME)
		{
			$xsd .= file_get_contents(kConf::get("{$type}_core_xsd_path"));
			$xsd .= '</xs:schema>';
			$xsdElement = new SimpleXMLElement($xsd);
		}
		else 
		{
			
		}
				
		header("Content-Type: text/plain; charset=UTF-8");
		echo $xsdElement->saveXML();
		kFile::closeDbConnections();
		exit;
	}
}
