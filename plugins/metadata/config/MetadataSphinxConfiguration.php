<?php
class MetadataSphinxConfiguration{
	public static function getConfiguration() {
		$kalturaEntryFields = Array ();

		$numOfDateFields = MetadataPlugin::getSphinxLimitField(MetadataPlugin::SPHINX_EXPENDER_FIELD_DATE);
		$numOfIntFields = MetadataPlugin::getSphinxLimitField(MetadataPlugin::SPHINX_EXPENDER_FIELD_INT);
			
		for ($i=0; $i < $numOfDateFields; $i++)
			$kalturaEntryFields[MetadataPlugin::getSphinxFieldName(MetadataPlugin::SPHINX_EXPENDER_FIELD_DATE) . $i] = SphinxFieldType::RT_ATTR_TIMESTAMP;
			
		for ($i=0; $i < $numOfIntFields; $i++)
			$kalturaEntryFields[MetadataPlugin::getSphinxFieldName(MetadataPlugin::SPHINX_EXPENDER_FIELD_INT) . $i] = SphinxFieldType::RT_ATTR_BIGINT;
		
		$kalturaEntryFields[MetadataPlugin::getSphinxFieldName(MetadataPlugin::SPHINX_EXPENDER_FIELD_DATA)] = SphinxFieldType::RT_FIELD;
		//TODO - change to be taken using kSphinxManager::getSphinxIndexName('entry::table_name')
		$sphinxSchema[kSphinxSearchManager::getSphinxIndexName('entry')]['fields'] = $kalturaEntryFields;
		return $sphinxSchema;
	}

}
