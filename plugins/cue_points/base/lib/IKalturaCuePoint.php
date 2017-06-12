<?php
/**
 * @package plugins.cuePoint
 */
interface IKalturaCuePoint extends IKalturaPermissions, IKalturaEnumerator, IKalturaPending, IKalturaObjectLoader, IKalturaSchemaContributor
{
	/**
	 * @param string $valueName the name of the value
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getCuePointTypeCoreValue($valueName);
	
	/**
	 * @param string $valueName the name of the value
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName);
	
	/**
	 * @return array of core cue point types to index on entry.
	 */
	public static function getTypesToIndexOnEntry();

   /**
	 * @param entry $entry the cloned entry
	 * @return boolean that indicates if clone is needed
	 */
	public static function shouldCloneByProperty(entry $entry);

	/**
	 * @return array of core cue point types to index to elasticsearch on entry.
	 */
	public static function getTypesToElasticIndexOnEntry();
}
