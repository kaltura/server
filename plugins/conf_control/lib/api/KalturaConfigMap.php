<?php
/**
 * Created by IntelliJ IDEA.
 * User: moshe.maor
 * Date: 12/11/2018
 * Time: 9:52 PM
 */

/**
 * @package plugins.confControl
 * @subpackage api.objects
 * @relatedService ConfControlService
 */

class KalturaConfigMap extends KalturaObject implements IRelatedFilterable
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
	 */
	public $version;

	/**
	 * @var KalturaConfMapSourceLocation
	 * @insertonly
	 */
	public $sourceLocation;

	public function validateContent(array $content)
	{
		$initStr = iniUtils::arrayToIniString($content);
		if(!parse_ini_string($initStr,true))
		{
			throw new KalturaAPIException(KalturaErrors::CANNOT_PARSE_CONTENT, "Cannot parse INI", $initStr);
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