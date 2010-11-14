<?php

class DocumentPlugin implements IKalturaPlugin, IKalturaServices, IKalturaObjectLoader, IKalturaEventConsumers
{
	const PLUGIN_NAME = 'document';
	const DOCUMENT_OBJECT_CREATED_HANDLER = 'DocumentCreatedHandler';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	public function getInstance($interface)
	{
		if($this instanceof $interface)
			return $this;
			
		return null;
	}

	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @param array $constructorArgs
	 * @return object
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{

		// ENTRY
		if($baseClass == entryPeer::OM_CLASS && $enumValue == entryType::DOCUMENT)
		{
			return new DocumentEntry();
		}
		
		
		// KALTURA FLAVOR PARAMS
		
		if($baseClass == 'KalturaFlavorParams')
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
	
		if($baseClass == 'KalturaFlavorParamsOutput')
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
		
		if($baseClass == 'KOperationEngine' && $enumValue == KalturaConversionEngineType::PDF_CREATOR)
		{
			if(!isset($constructorArgs['params']) || !isset($constructorArgs['outFilePath']))
				return null;
			
			return new KOperationEnginePdfCreator($constructorArgs['params']->pdfCreatorCmd, $constructorArgs['outFilePath']);
		}

		
		if($baseClass == 'KOperationEngine' && $enumValue == KalturaConversionEngineType::PDF2SWF)
		{
			if(!isset($constructorArgs['params']) || !isset($constructorArgs['outFilePath']))
				return null;
			
			return new KOperationEnginePdf2Swf($constructorArgs['params']->pdf2SwfCmd, $constructorArgs['outFilePath']);
		}
		
		
		// KDL ENGINES
		
		if($baseClass == 'KDLOperatorBase' && $enumValue == conversionEngineType::PDF_CREATOR)
		{
			return new KDLTranscoderPdfCreator($enumValue);
		}
				
		if($baseClass == 'KDLOperatorBase' && $enumValue == conversionEngineType::PDF2SWF)
		{
			return new KDLTranscoderPdf2Swf($enumValue);
		}
		
		
		return null;
	}

	
	/**
	 * @param string $baseClass
	 * @param string $enumValue
	 * @return string
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		// DOCUMENT ENTRY
		if($baseClass == entryPeer::OM_CLASS && $enumValue == entryType::DOCUMENT)
		{
			return 'DocumentEntry';
		}
		
		// FLAVOR PARAMS
		if($baseClass == flavorParamsPeer::OM_CLASS)
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
	
		if($baseClass == flavorParamsOutputPeer::OM_CLASS)
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
