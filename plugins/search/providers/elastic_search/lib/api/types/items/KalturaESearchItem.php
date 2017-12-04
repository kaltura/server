<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
abstract class KalturaESearchItem extends KalturaESearchBaseItem
{

	const MAX_SEARCH_TERM_LENGTH = 128;

	/**
	 * @var string
	 */
	public $searchTerm;

	/**
	 * @var KalturaESearchItemType
	 */
	public $itemType;

	/**
	 * @var KalturaESearchRange
	 */
	public $range;

	private static $map_between_objects = array(
		'searchTerm',
		'itemType',
		'range',
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	abstract protected function getItemFieldName();

	abstract protected function getDynamicEnumMap();

	abstract protected function getFieldEnumMap();

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if(strlen($this->searchTerm) > self::MAX_SEARCH_TERM_LENGTH)
		{
			$this->searchTerm = substr($this->searchTerm, 0, self::MAX_SEARCH_TERM_LENGTH);
			KalturaLog::log("Search term exceeded maximum allowed length, setting search term to [$this->searchTerm]");
		}

		$dynamicEnumMap = $this->getDynamicEnumMap();
		if(isset($dynamicEnumMap[$this->getItemFieldName()]))
		{
			try
			{
				$enumType = call_user_func(array($dynamicEnumMap[$this->getItemFieldName()], 'getEnumClass'));
				$SearchTermValue = kPluginableEnumsManager::apiToCore($enumType, $this->searchTerm);
				$object_to_fill->setSearchTerm($SearchTermValue);
				$props_to_skip[] = 'searchTerm';
			}
			catch (kCoreException $e)
			{
				if($e->getCode() == kCoreException::ENUM_NOT_FOUND)
					throw new KalturaAPIException(KalturaErrors::INVALID_ENUM_VALUE, $this->searchTerm, 'searchTerm', $dynamicEnumMap[$this->getItemFieldName()]);
			}

		}

		$fieldEnumMap = $this->getFieldEnumMap();
		if(isset($fieldEnumMap[$this->getItemFieldName()]))
		{
			$coreFieldName = $fieldEnumMap[$this->getItemFieldName()];
			$object_to_fill->setFieldName($coreFieldName);
			$props_to_skip[] = 'fieldName';
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}

}
