<?php

class DocumentPlugin implements IKalturaServicesPlugin, IKalturaObjectLoaderPlugin, IKalturaEventConsumersPlugin
{
	const PLUGIN_NAME = 'document';
	const DOCUMENT_OBJECT_CREATED_HANDLER = 'DocumentCreatedHandler';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public function getInstances($intrface)
	{
		if($this instanceof $intrface)
			return array($this);
			
		return array();
	}

	/**
	 * @param KalturaPluginManager::OBJECT_TYPE $objectType
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($objectType, $enumValue, array $constructorArgs = null)
	{

		// ENTRY
		if($objectType == KalturaPluginManager::OBJECT_TYPE_ENTRY && $enumValue == entry::ENTRY_TYPE_DOCUMENT)
		{
			return new DocumentEntry();
		}
		
		
		// KALTURA FLAVOR PARAMS
		
		if($objectType == KalturaPluginManager::OBJECT_TYPE_KALTURA_FLAVOR_PARAMS)
		{
			switch($enumValue)
			{
				case flavorParams::CONTAINER_FORMAT_PDF:
					return new KalturaPdfFlavorParams();
					
				case flavorParams::CONTAINER_FORMAT_SWF:
					return new KalturaSwfFlavorParams();
				
				default:
					return null;	
			}
		}
	
		if($objectType == KalturaPluginManager::OBJECT_TYPE_KALTURA_FLAVOR_PARAMS_OUTPUT)
		{
			switch($enumValue)
			{
				case flavorParams::CONTAINER_FORMAT_PDF:
					return new KalturaPdfFlavorParamsOutput();
					
				case flavorParams::CONTAINER_FORMAT_SWF:
					return new KalturaSwfFlavorParamsOutput();
				
				default:
					return null;	
			}
		}
		
		
		// OPERATION ENGINES
		
		if($objectType == KalturaPluginManager::OBJECT_TYPE_OPERATION_ENGINE && $enumValue == kConvertJobData::CONVERSION_ENGINE_PDF_CREATOR)
		{
			if(!isset($constructorArgs['params']) || !isset($constructorArgs['outFilePath']))
				return null;
			
			return new KOperationEnginePdfCreator($constructorArgs['params']->pdfCreatorCmd, $constructorArgs['outFilePath']);
		}

		
		if($objectType == KalturaPluginManager::OBJECT_TYPE_OPERATION_ENGINE && $enumValue == kConvertJobData::CONVERSION_ENGINE_PDF2SWF)
		{
			if(!isset($constructorArgs['params']) || !isset($constructorArgs['outFilePath']))
				return null;
			
			return new KOperationEnginePdf2Swf($constructorArgs['params']->pdf2SwfCmd, $constructorArgs['outFilePath']);
		}
		
		
		// KDL ENGINES
		
		if($objectType == KalturaPluginManager::OBJECT_TYPE_KDL_ENGINE && $enumValue == kConvertJobData::CONVERSION_ENGINE_PDF_CREATOR)
		{
			return new KDLTranscoderPdfCreator($enumValue);
		}
				
		if($objectType == KalturaPluginManager::OBJECT_TYPE_KDL_ENGINE && $enumValue == kConvertJobData::CONVERSION_ENGINE_PDF2SWF)
		{
			return new KDLTranscoderPdf2Swf($enumValue);
		}
		
		
		return null;
	}

	
	/**
	 * @param KalturaPluginManager::OBJECT_TYPE $objectType
	 * @param string $enumValue
	 * @return string
	 */
	public static function getObjectClass($objectType, $enumValue)
	{
		// DOCUMENT ENTRY
		if($objectType == KalturaPluginManager::OBJECT_TYPE_ENTRY && $enumValue == entry::ENTRY_TYPE_DOCUMENT)
		{
			return 'DocumentEntry';
		}
		
		// FLAVOR PARAMS
		if($objectType == KalturaPluginManager::OBJECT_TYPE_FLAVOR_PARAMS)
		{
			switch($enumValue)
			{
				case flavorParams::CONTAINER_FORMAT_PDF:
					return 'PdfFlavorParams';
					
				case flavorParams::CONTAINER_FORMAT_SWF:
					return 'SwfFlavorParams';
				
				default:
					return null;	
			}
		}
	
		if($objectType == KalturaPluginManager::OBJECT_TYPE_FLAVOR_PARAMS_OUTPUT)
		{
			switch($enumValue)
			{
				case flavorParams::CONTAINER_FORMAT_PDF:
					return 'PdfFlavorParamsOutput';
					
				case flavorParams::CONTAINER_FORMAT_SWF:
					return 'SwfFlavorParamsOutput';
				
				default:
					return null;	
			}
		}
		
		return null;
	}

	/**
	 * @return array<string,string> in the form array[serviceName] = serviceClass
	 */
	public static function getServicesMap()
	{
		$map = array(
			'documents' => 'DocumentsService',
		);
		return $map;
	}
	
	/**
	 * @return string - the path to services.ct
	 */
	public static function getServiceConfig()
	{
		return realpath(dirname(__FILE__).'/config/document.ct');
	}

	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array(
			self::DOCUMENT_OBJECT_CREATED_HANDLER,
		);
	}
}
