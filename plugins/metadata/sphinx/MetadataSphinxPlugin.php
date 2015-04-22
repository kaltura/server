<?php

/**
 * Enable indexing and searching of metadata objects in sphinx
 * @package plugins.metadataSphinx
 */
class MetadataSphinxPlugin extends KalturaPlugin implements IKalturaCriteriaFactory
{
	const PLUGIN_NAME = 'metadataSphinx';
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaCriteriaFactory::getKalturaCriteria()
	 */
	public static function getKalturaCriteria($objectType)
	{
		if ($objectType == "Metadata")
			return new SphinxMetadataCriteria();
			
		return null;
	}
}
