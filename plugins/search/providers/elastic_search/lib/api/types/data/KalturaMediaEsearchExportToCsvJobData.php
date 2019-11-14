<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */

class KalturaMediaEsearchExportToCsvJobData extends KalturaExportCsvJobData
{
	/**
	 * Esearch parameters for the entry search
	 *
	 * @var KalturaESearchEntryParams
	 */
	public $searchParams;
	/**
	 * Human readble choice
	 * @var boolean
	 */
	public $humanReadable;
	
	private static $map_between_objects = array
	(
		'humanReadable',
		'searchParams',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill))
		{
			throw new KalturaAPIException(KalturaErrors::OBJECT_TYPE_ABSTRACT, "KalturaExportCsvJobData");
		}
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
