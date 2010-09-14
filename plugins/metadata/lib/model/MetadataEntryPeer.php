<?php
class MetadataEntryPeer extends entryPeer implements iMetadataPeer
{
	public static function saveToSphinx($objectId, array $data)
	{
		$entry = self::retrieveByPK($objectId);
		if(!$entry)
		{
			KalturaLog::err("Entry [$objectId] not found");
			return;
		}
			
		// TODO - remove after solving the replace bug that removes all fields
		$entry->saveToSphinx(false, true);
		return;
		
		$pluginsData = $entry->getPluginData();
		$sphinxPluginsData = array();
		if($pluginsData && is_array($pluginsData))
		{
			foreach($pluginsData as $pluginName => $pluginData)
			{
				$sphinxPluginData = $entry->array2sphinxData($pluginsData, $pluginName);
				if($sphinxPluginData)
					$sphinxPluginsData[] = $sphinxPluginData;
			}
		}
		
		$sphinxPluginData = $entry->array2sphinxData($data, MetadataPlugin::PLUGIN_NAME);
		if($sphinxPluginData)
			$sphinxPluginsData[] = $sphinxPluginData;
				
		if(count($sphinxPluginsData))
		{
			$value = implode(',', $sphinxPluginsData);
			$entry->updateSphinx('plugins_data', $value);
		}
		else
		{
			KalturaLog::debug("No plugins data should be saved to the entry [$objectId]");
		}
	}
}
