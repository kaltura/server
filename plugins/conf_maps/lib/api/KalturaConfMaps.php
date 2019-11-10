<?php
/**
 * @package plugins.confMaps
 * @subpackage api.objects
 * @relatedService ConfMapsService
 */

class KalturaConfMaps extends KalturaObject implements IRelatedFilterable
{
	/**
	 * Name of the map
	 *
	 * @var string
	 * @insertonly
	 * @filter eq
	 */
	public $name;

	/**
	 * Ini file content
	 *
	 * @var string
	 */
	public $content;

	/**
	 * @var string
	 */
	public $rawData;

	/**
	 * IsEditable - true / false
	 *
	 * @var bool
	 * @readonly
	 */
	public $isEditable;

	/**
	 * Time of the last update
	 *
	 * @var time
	 * @readonly
	 */
	public $lastUpdate;

	/**
	 * Regex that represent the host/s that this map affect
	 *
	 * @var string
	 * @filter eq
	 */
	public $relatedHost;

	/**
	 * @var int
	 * @readonly
	 * @filter eq
	 */
	public $version;

	/**
	 * @var KalturaConfMapsSourceLocation
	 * @insertonly
	 */
	public $sourceLocation;

	/**
	 * @var string
	 * @insertonly
	 */
	public $remarks;

	/**
	 * map status
	 *
	 * @var KalturaConfMapsStatus
	 * @filter eq
	 */
	public $status;



	private static $map_between_objects = array
	(
		"name" => "mapName",
		"relatedHost" => "hostName",
		"status",
		"version",
		"lastUpdate" => "createdAt",
		"remarks",
		"content"
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		if(strstr($this->relatedHost,'*'))
		{
			throw new KalturaAPIException(KalturaErrors::HOST_NAME_CONTAINS_ASTRIX ,$this->relatedHost );
		}
		if($this->sourceLocation == KalturaConfMapsSourceLocation::FS)
		{
			throw new KalturaAPIException(KalturaErrors::MAP_CANNOT_BE_CREATED_ON_FILE_SYSTEM);
		}
		parent::validateForInsert($propertiesToSkip);
	}

	public function validateAndHandleContent()
	{
		$content = json_decode($this->content, true);
		if (is_null($content))
		{
			throw new KalturaAPIException(KalturaErrors::CANNOT_PARSE_CONTENT, "Cannot JSON decode content", $this->content);
		}

		$filter = new KalturaConfMapsFilter();
		$filter->relatedHostEqual = $this->relatedHost;
		$filter->nameEqual = $this->name;
		kApiCache::disableCache();
		$configurationMap = $filter->getMap(true);
		$contentToValidate = null;
		if ($configurationMap)
		{
			$existingMapsContent = json_decode($configurationMap->content, true);
			if (!is_null($existingMapsContent))
			{
				$ini = new Zend_Config($existingMapsContent, true);
				$contentToValidate = iniUtils::arrayToIniString($ini->toArray());
			}
		}
		$contentToValidate .= "\n" . $content;
		try
		{
			$tempIniFile = tempnam(sys_get_temp_dir(), 'TMP_CONF_MAP_');
			file_put_contents($tempIniFile, $contentToValidate);
			new Zend_Config_Ini($tempIniFile);
			unlink($tempIniFile);
		}
		catch (Exception $e)
		{
			KalturaLog::err($e->getMessage());
			throw new KalturaAPIException(KalturaErrors::CANNOT_PARSE_CONTENT, "Cannot parse INI", $content);
		}
	}

	/* (non-PHPdoc)
 * @see IFilterable::getExtraFilters()
 */
	public function getExtraFilters()
	{
		return array();
	}

	/* (non-PHPdoc)
	 * @see IFilterable::getFilterDocs()
	 */
	public function getFilterDocs()
	{
		return array();
	}

}