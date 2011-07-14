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
	 * @return file 
	 */
	function serveAction($type)
	{
		header("Content-Type: text/plain; charset=UTF-8");
		
		$cacheXsdFile = kConf::get("cache_root_path") . "/$type.xsd";
		if(file_exists($cacheXsdFile))
			kFile::dumpFile($cacheXsdFile);
		
		$xsdFile = fopen($cacheXsdFile, 'w');
		
		$namespace = 'http://' . kConf::get('www_host') . "/$type";
		fwrite($xsdFile, '<xs:schema targetNamespace="' . $namespace . '" xmlns:xs="http://www.w3.org/2001/XMLSchema">');
	
		$baseXsdElement = new SimpleXMLElement('<xs:schema targetNamespace="' . $namespace . '" xmlns:xs="http://www.w3.org/2001/XMLSchema"/>');
		if($type == SchemaType::SYNDICATION)
		{
			$baseXsdElement = new SimpleXMLElement(kConf::get("syndication_core_xsd_path"), null, true);
		}
		else
		{
			$plugin = kPluginableEnumsManager::getPlugin($type);
			if($plugin instanceof IKalturaSchemaDefiner)
			{
				KalturaLog::debug("Found plugin [" . get_class($plugin) . "]");
				$baseXsdElement = $plugin->getPluginSchema($type);
			}
		}
	
		foreach($baseXsdElement->children('http://www.w3.org/2001/XMLSchema') as $element)
		{
			/* @var $element SimpleXMLElement */
			fwrite($xsdFile, '
	' . $element->asXML());
		}
		
		$schemaContributors = KalturaPluginManager::getPluginInstances('IKalturaSchemaContributor');
		foreach($schemaContributors as $key => $schemaContributor)
		{
			/* @var $schemaContributor IKalturaSchemaContributor */
			$elements = $schemaContributor->contributeToSchema($type);
			if($elements)
				fwrite($xsdFile, $elements);
		}
		
		$cacheEnumFile = kConf::get("cache_root_path") . '/api_v3/enum.xsd';
		if(file_exists($cacheEnumFile))
		{
			fwrite($xsdFile, file_get_contents($cacheEnumFile));
		}
		else
		{
			$enumFile = fopen($cacheEnumFile, 'w');
			
			$classMapFileLcoation = KAutoloader::getClassMapFilePath();
			$classMap = unserialize(file_get_contents($classMapFileLcoation));
			
			foreach($classMap as $class => $path)
			{
				if (strpos($class, 'Kaltura') !== 0) // class should start with 'Kaltura...'
					continue;

				if (strpos($class, '_') !== false) // class shouldn't contain underscore like in Zend standard
					continue;
					
				if(strpos($path, 'api') === false) // class must be under any api folder
					continue;
					
				if(!is_subclass_of($class, 'KalturaEnum') && !is_subclass_of($class, 'KalturaStringEnum')) // class must be enum
					continue;
					
				$classTypeReflector = KalturaTypeReflectorCacher::get($class);
						
				$xsdType = 'int';
				if($classTypeReflector->isStringEnum())
					$xsdType = 'string';
				
				$xsd = '
	<xs:simpleType name="' . $class . '">
		<xs:annotation><xs:documentation>http://' . kConf::get('www_host') . '/api_v3/testmeDoc/index.php?object=' . $class . '</xs:documentation></xs:annotation>
		<xs:restriction base="xs:' . $xsdType . '">';
			
				$contants = $classTypeReflector->getConstants();
				foreach($contants as $contant)
				{
					$xsd .= '
			<xs:enumeration value="' . $contant->getDefaultValue() . '"><xs:annotation><xs:documentation>' . $contant->getName() . '</xs:documentation></xs:annotation></xs:enumeration>';
				}
				
							
				$xsd .= '
		</xs:restriction>
	</xs:simpleType>
				';
				
				fwrite($xsdFile, $xsd);
				fwrite($enumFile, $xsd);
			}
			fclose($enumFile);
		}
		
		fwrite($xsdFile, '
</xs:schema>');
		
		fclose($xsdFile);
		
		kFile::dumpFile($cacheXsdFile);
	}
}
