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
	const ENUM_SCHEMA_NAME = 'enum';
	
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
		$xsd = '<xs:schema targetNamespace="' . $ns . '" xmlns:xs="http://www.w3.org/2001/XMLSchema"/>';
			
		if(!$name)
		{		
			$xsdElement = new SimpleXMLElement($xsd);
		
			$redefine = $xsdElement->addChild('redefine');
			$redefine->addAttribute('schemaLocation', 'http://' . kConf::get('cdn_host') . "/api_v3/service/schema/action/serve/type/$type/name/" . self::CORE_SCHEMA_NAME);
		
			$import = $xsd->addChild('import');
			$import->addAttribute('schemaLocation', 'http://' . kConf::get('cdn_host') . "/api_v3/service/schema/action/serve/type/$type/name/enum");
			
			$schemaContributors = KalturaPluginManager::getPluginInstances('IKalturaSchemaContributor');
			foreach($schemaContributors as $key => $schemaContributor)
				$schemaContributor->contributeToSchema($type, $xsdElement);
		}
		elseif ($name == self::ENUM_SCHEMA_NAME)
		{
			$cacheFile = kConf::get("cache_root_path") . '/api_v3/enum.xsd';
			if(file_exists($cacheFile))
			{
				$xsd = file_get_contents($cacheFile);
				$xsdElement = new SimpleXMLElement($xsd);
			}
			else
			{
				$xmlnsBase = "http://" . kConf::get('www_host') . "/$type";
				$xmlnsPlugin = "http://" . kConf::get('www_host') . "/$type/enum";
				
				$xsd = '<?xml version="1.0" encoding="UTF-8"?>
					<xs:schema 
						xmlns:xs="http://www.w3.org/2001/XMLSchema"
						xmlns="' . $xmlnsPlugin . '" 
						xmlns:core="' . $xmlnsBase . '" 
						targetNamespace="' . $xmlnsPlugin . '"
					>
				';
			
				$classMapFileLcoation = KAutoloader::getClassMapFilePath();
				$classMap = unserialize(file_get_contents($classMapFileLcoation));
				
				foreach($classMap as $class => $path)
				{
					if (strpos($class, 'Kaltura') === 0 && strpos($class, '_') === false && strpos($path, 'api') !== false) // make sure the class is api object
					{
						if(!is_subclass_of($class, 'KalturaEnum'))
							continue;
							
						$classTypeReflector = KalturaTypeReflectorCacher::get($class);
						
						$xsdType = 'int';
						if($classTypeReflector->isStringEnum())
							$xsdType = 'string';
						
						$xsd .= '
							<xs:simpleType name="' . $class . '">
								<xs:annotation><xs:documentation>http://' . kConf::get('www_host') . '/api_v3/testmeDoc/index.php?object=' . $class . '</xs:documentation></xs:annotation>
								<xs:restriction base="xs:' . $xsdType . '">';
					
						$contants = $classTypeReflector->getConstants();
						foreach($contants as $contant)
						{
							$xsd .= '<xs:enumeration value="' . $contant->getDefaultValue() . '"><xs:annotation><xs:documentation>' . $contant->getName() . '</xs:documentation></xs:annotation></xs:enumeration>';
						}
						
									
						$xsd .= '</xs:restriction>
							</xs:simpleType>
						';
	
					}
				}
				
				$xsd .= '</xs:schema>';
				file_put_contents($cacheFile, $xsd);
				$xsdElement = new SimpleXMLElement($xsd);
			}
		}
		elseif ($name == self::CORE_SCHEMA_NAME)
		{
			if($type == SchemaType::SYNDICATION)
			{
				$xsd = '<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">';
				$xsd .= file_get_contents(kConf::get("syndication_core_xsd_path"));
				$xsd .= '</xs:schema>';
				$xsdElement = new SimpleXMLElement($xsd);
			}
			else
			{
				$plugin = kPluginableEnumsManager::getPlugin($type);
				if($plugin instanceof IKalturaSchemaContributor)
				{
					KalturaLog::debug("Found plugin [" . get_class($plugin) . "]");
					$xsdElement = $plugin->getPluginSchema($type);
				}
				else
				{		
					$xsdElement = new SimpleXMLElement($xsd);
				}
			}
		}
		else 
		{
			$plugin = KalturaPluginManager::getPluginInstance($name);
			if($plugin && $plugin instanceof IKalturaSchemaContributor)
			{
				$xsdElement = $plugin->getPluginSchema($type);
			}
			else 
			{		
				$xsdElement = new SimpleXMLElement($xsd);
			}			
		}
				
		header("Content-Type: text/plain; charset=UTF-8");
		echo $xsdElement->saveXML();
		kFile::closeDbConnections();
		exit;
	}
}
